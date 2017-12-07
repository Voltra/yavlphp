<?php
namespace YavlPhp\Helpers;


abstract class ArrayHelper {
    public static function valuesAreArray(array $arr) : bool{
        return count(
            array_filter(
                array_values($arr),
                'is_array'
            )
        ) > 0;
    }

    public static function valuesAreString(array $arr) : bool{
        return count(
            array_filter(
                array_values($arr),
                "is_string"
            )
        ) > 0;
    }
}