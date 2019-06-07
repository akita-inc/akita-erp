<?php

namespace App\CoreExtensions;

use Illuminate\Auth\SessionGuard;

class SessionGuardExtended extends SessionGuard
{
    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param  \Illuminate\Auth\Recaller  $recaller
     * @return mixed
     */
    protected function userFromRecaller($recaller)
    {
        if (! $recaller->valid() || $this->recallAttempted) {
            return null;
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = ! is_null($user = $this->provider->retrieveByToken(
            $recaller->id(), $recaller->token()
        ));

        // Check password in DB and password in cookie
        // If difference logout
        if($user->getAuthPassword() != $recaller->hash())
            return null;

        return $user;
    }
}