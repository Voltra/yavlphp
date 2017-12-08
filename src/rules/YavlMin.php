<?php
namespace YavlPhp\Rules;


use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;

final class YavlMin extends YavlValidationFunction{

    public function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
        return ($value >= $expected) ? null : $locale->get($this->getNameForJson());
    }

    public function getNameForJson(): string {
        return "min";
    }
}