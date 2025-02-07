<?php

namespace App\Foundation\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class AdminEloquentUserProvider extends EloquentUserProvider{
    
    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        $authPassword = $user->getAuthPassword();

        //dd('fromAdminEloquentUSER', $user,$plain,$user->getAuthPassword());
        //dd(sha1($authPassword['salt'] . sha1($authPassword['salt'] . sha1($plain)))==$authPassword['password']);
        //'password' => sha1($authPassword['salt'] . sha1($authPassword['salt'] . sha1($plain))),
        //return sha1($authPassword['salt'].$plain) == $authPassword['password'];
        //dd(sha1($authPassword['salt'] . sha1($authPassword['salt'] . sha1($plain))) == $authPassword['password']);
        return sha1($authPassword['salt'] . sha1($authPassword['salt'] . sha1($plain))) == $authPassword['password'];
        //原EloquentUserProvider /* return $this->hasher->check($plain, $user->getAuthPassword()); */
    }

}

?>