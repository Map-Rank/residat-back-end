<?php

namespace App\Models;


class UtilService
{

    public static function get_descendants ($children, $descendants)
    {
        foreach ($children as $child) {
            $child = $child->load('children');
            $descendants->push($child);
            if ($child->children != null) {
                $descendants = UtilService::get_descendants($child->children, $descendants);
            }
        }
        return $descendants;
    }
}
