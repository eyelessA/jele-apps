<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RegistrationService
{
    public function registration(array $data): User|Model
    {
        return User::query()->create([
            'email' => $data['email'],
            'email_verified_at' => now(),
            'password' => bcrypt($data['password']),
            'gender' => $data['gender'],
        ]);
    }
}
