<?php

namespace App\Trait;


trait ParseActivity
{
    public function parseUpdateAble($model, $attribute)
    {
        $data = [];
        foreach ($attribute as $key => $value) {
            if (!$value || $key == 'id') continue;
            $data[] = ucfirst($key) . ": " . $model->{$key} . " -> " . $value;
        }

        return implode(',',$data) ?? 'updated';
    }
}
