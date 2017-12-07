<?php
namespace YavlPhp\Rules;


use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;
use YavlPhp\Helpers\Regex;

final class YavlRegex extends YavlValidationFunction {

    public function __call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
        $re = new Regex("/{$expected}/");
        return ($re->appliesTo("{$value}") ? null : $locale->get("nomatch_regex"));
    }
}