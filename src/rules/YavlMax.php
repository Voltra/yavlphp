<?php
namespace YavlPhp\rules;


use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;

final class YavlMax extends YavlValidationFunction {

    public function __call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
        return ($value <= $expected) ? null : $locale->get("max");
    }
}