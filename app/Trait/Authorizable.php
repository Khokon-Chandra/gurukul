<?php

namespace App\Trait;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Models\Permission;

trait Authorizable
{

    private $abilities = [];

    /**
     * Override of callAction to perform the authorization before.
     *
     * @param $method
     * @param $parameters
     * @return mixed
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
