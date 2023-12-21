<?php
namespace App\Trait;

use Illuminate\Support\Facades\Route;

trait RoutePermission
{
    public function permissions()
    {
        $routelist   = Route::getRoutes();

        $permissions = [];

        $sort = 0;

        foreach ($routelist as $route) {

            if (!$this->isRouteForAPI($route)) continue;

            $routeName   = $route->getName();
            
            $groupby     = $this->groupBy($routeName);

            if(!$groupby) continue;

            $sort++;
            
            $permissions[] = [
                'module_name'  => "user.access.".$routeName,
                'name'         => "user.access.".$routeName,
                'display_name' => $this->displayName($route),
                'group_by'     => $groupby,
                'sort'         => $sort,
            ];

        }

        return $permissions;
    }


    private function groupBy($routeName)
    {
        $arr = explode('.',$routeName);
        return @$arr[1];
    }

    private function displayName($route)
    {
        $actionName   = $route->getActionName();

        $routeName    = $route->getName();

        $method       = explode('@', $actionName)[1];

        $arrRouteName = explode('.',$routeName);

        if(count($arrRouteName) < 3) return false;

        $action       = array_reverse( $arrRouteName )[1];

        $ability      = config('abilities')[$method] ?? $method;

        $displayName  = "can $ability $action" ?? $method;

        return $displayName;
    }



    private function isRouteForAPI($route)
    {
        $prefix = 'api/' . config('app.api_version');

        return $route->getPrefix() == $prefix;
    }
}