<?php
namespace YavlPhp\rules;


use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;

final class YavlMatch extends YavlValidationFunction{
    public function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
        $otherValue = $fieldsValues["{$expected}"];

        return ($value === $otherValue) ? null : $locale->get($this->getNameForJson());
    }

    public function getNameForJson(): string {
        return "notEqual";
    }
}