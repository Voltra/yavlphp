<?php
namespace YavlPhp\Helpers;


abstract class ArrayHelper {
    public static function valuesAreArray(array $arr) : bool{
        if(empty($arr))
            return false;

        return array_reduce($arr, function(int $acc, $elem){
            if(!is_array($elem))
                return $acc + 1;

            return $acc;
        }, 0) === 0;
    }

    public static function valuesAreString(array $arr) : bool{
        if(empty($arr))
            return false;

        return array_reduce($arr, function(int $acc, $elem){
            if(!is_string($elem))
                return $acc + 1;

            return $acc;
        }, 0) === 0;
    }

    public static function valuesAreAssociativeArray(array $arr){
        if(empty($arr))
            return false;

        return array_reduce($arr, function(int $acc, $elem){
            if(!is_array($elem))
                return $acc + 1;

            if(!AssociativeArrayHelper::isAssociative($elem))
                return $acc + 1;

            return $acc;
        }, 0) === 0;
    }

    public static function isEmpty(array $arr) : bool{
        return empty($arr);
    }
}