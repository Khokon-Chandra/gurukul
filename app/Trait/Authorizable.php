<?php

namespace App\Trait;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Request;

trait Authorizable
{

    /**
     * Override of callAction to perform the authorization before.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws AuthorizationException
     */

    public function callAction($method, $parameters)
    {
        if ($ability = $this->getAbility()) {
            $this->authorize($ability);
        }

        return parent::callAction($method, $parameters);
    }

    public function getAbility()
    {
        return "user.access.".Request::route()->getName();
        
    }

   
}
