<?php


/**
 * List of default method names of the Controllers and the related permission.
 */

return [
    ''           => 'read',
    'index'      => 'read',
    'index_data' => 'read',
    'index_list' => 'read',
    'show'       => 'show',
    'update'     => 'update',
    'store'      => 'create',
    'destroy'    => 'delete',
    'delete'     => 'delete',
    'restore'    => 'restore',
    'trashed'    => 'restore',
    'updateMultiple' => 'update_multiple',
    'deleteMultiple' => 'delete_multiple',
];
