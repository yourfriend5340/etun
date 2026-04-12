<?php

namespace App\Foundation\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminEloquentCustomerProvider extends EloquentUserProvider
{
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        $auth = $user->getAuthPassword();

        return sha1($auth['salt'] . sha1($auth['salt'] . sha1($plain))) === $auth['password'];
    }
}
