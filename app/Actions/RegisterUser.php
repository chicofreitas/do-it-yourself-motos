<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserRegistered;
use App\Models\User;

class RegisterUser
{
    use AsAction;

    public function handle(array $data)
    {
        $user = User::create($data);

        return $user;
    }
}
