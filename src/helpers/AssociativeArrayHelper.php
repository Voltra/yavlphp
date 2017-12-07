<?php
namespace YavlPhp\Helpers;

abstract class AssociativeArrayHelper {
    public static function isAssociative(array $arr){
        if (empty($arr))
            return false;

        $keys = array_keys($arr);
        sort($keys);
        return $keys !== range(0, count($arr) - 1);
    }
}