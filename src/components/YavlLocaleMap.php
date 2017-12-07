<?php
namespace YavlPhp\Components;


use InvalidArgumentException;
use YavlPhp\Helpers\ArrayHelper;
use YavlPhp\Helpers\AssociativeArrayHelper;

final class YavlLocaleMap {
    const defaults = [
        "NaN" => "Invalid format (NaN)",
        "required" => "This field is required",
        "min" => "Must be &ge; %value%",
        "max" => "Must be &le; %value%",
        "nomatch_regex" => "Invalid format",
        "minLength" => "Expects a minimum of %value% characters",
        "maxLength" => "Expects a maximum of %value% characters",
        "notEqual" => "Value mismatch"
    ];

    protected $locale;

    public function __construct(array $localeMap){
        if(!ArrayHelper::valuesAreString($localeMap))
            throw new InvalidArgumentException("The values of the locale map are not all strings");

        if(!AssociativeArrayHelper::isAssociative($localeMap))
            throw new InvalidArgumentException("The given locale map is not an associative array");

        $this->locale = array_merge(self::defaults, $localeMap);
    }

    public function has(string $key) : bool{
        return array_key_exists($key, $this->locale);
    }

    public function get(string $key) : bool{
        if($this->has($key))
            return $this->locale[$key];

        throw new InvalidArgumentException("No locale error message available for: {$key}");
    }
}