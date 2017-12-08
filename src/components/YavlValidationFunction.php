<?php
namespace YavlPhp\Components;


abstract class YavlValidationFunction {
    abstract function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues) : ?string;
    abstract function getNameForJson() : string;
}