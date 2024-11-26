<?php

namespace App\Http\Middleware;

use App\Services\User\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJWT
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;

    }

    public function handle($request, Closure $next)
    {
        $token = $request->header('Bearer');

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $payload = $this->jwtService->verifyJWT($token);

        if (!$payload || !is_array($payload) || $payload['exp'] < time()) {
            return response()->json(['error' => 'Token invalid or expired'], 401);
        }

        $request->attributes->add(['user' => $payload]);

        return $next($request);
    }
}
