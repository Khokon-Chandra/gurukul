<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;
    protected $guarded = ['id'];

    public function modulePermission(array $permissionIds = null)
    {
        $queryResult = $permissionIds ? self::whereIn('id', $permissionIds)->get() : self::get();
        $items = $queryResult->filter(function ($item) {
            //Name Pattern Must Follow user.access.*
            try {
                $level = explode('.', $item->name);

                logger('log level');
                logger($level);
                $condition2 = count($level) >= 2 &&
                    $level[0] === 'user' &&
                    $level[1] === 'access';

                return $item->type === 'user' && $condition2;
            } catch (\Exception $e) {
                Log::error($e);
                return false;
            }
        })->map(function ($item) {
            $itemLevels = explode('.', $item->name);
            if (count($itemLevels) > 3) {
                $newItem['sub_module_name'] = $itemLevels[3];
                $newItem['items'][$itemLevels[3]] = $item->toArray();
            } else {
                $newItem['items'] = collect($item->toArray());
            }
            $newItem['module_name'] = $itemLevels[2];
            return $newItem;
        });

        return $items->groupBy('module_name');
    }
}
