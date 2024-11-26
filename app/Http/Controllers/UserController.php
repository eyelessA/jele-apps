<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\User\JWTService;
use App\Services\User\RegistrationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private RegistrationService $registrationService;
    private JWTService $jwtService;

    public function __construct(
        RegistrationService $registrationService,
        JWTService $jwtService,
    )
    {
        $this->registrationService = $registrationService;
        $this->jwtService = $jwtService;
    }

    public function registration(UserRequest $userRequest): JsonResponse
    {
        $data = $userRequest->validated();
        $user = $this->registrationService->registration($data);

        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'gender' => $user->gender,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $token = $this->jwtService->generateJWT($payload);
        return response()->json(['token' => $token]);
    }

    /**
     * @throws Exception
     */
    public function profile(Request $request): array
    {
        $token = $request->header('Bearer');

        $segments = explode('.', $token);

        if (count($segments) != 3) {
            throw new Exception('Неверный формат токена.');
        }

        json_decode(base64_decode($segments[0]), true);
        $payload = json_decode(base64_decode($segments[1]), true);

        return UserResource::make($payload)->resolve();
    }
}
