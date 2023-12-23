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
//        logger('parameters');
//        logger($parameters);
        $abilities = config('abilities');
        $permissions = $abilities['route_permissions'];

        $routeName = Request::route()->getName();
        $permission = $permissions[$routeName];

        $unProtectedRoutes = array_merge(
            array_map(fn ($routeName) => route($routeName), $abilities['unprotected_route_names']),
            array_map(fn ($routeUrl) => url($routeUrl), $abilities['unprotected_route_url']),
        );

        if (! in_array(route($routeName, ['*']), $unProtectedRoutes)) {
            $this->authorize($permission['name']);
        }

        return parent::callAction($method, $parameters);
    }
}