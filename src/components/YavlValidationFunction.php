<?php
namespace YavlPhp\Components;


abstract class YavlValidationFunction {
    abstract function __call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues) : ?string;
}