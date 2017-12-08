<?php
namespace YavlPhp\Rules;


use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;

final class YavlMaxLength extends YavlValidationFunction {

    public function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
        return (strlen("{$value}") <= intval($expected)) ? null : $locale->get($this->getNameForJson());
    }

    public function getNameForJson(): string {
        return "maxLength";
    }
}