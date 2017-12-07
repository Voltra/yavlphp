<?php
namespace YavlPhp\Components;

use InvalidArgumentException;
use YavlPhp\Helpers\ArrayHelper;
use YavlPhp\Helpers\AssociativeArrayHelper;

final class YavlFieldsMap {
    /**
     * @var array
     */
    protected $map;

    public function __construct(array $map) {
        if(!AssociativeArrayHelper::isAssociative($map))
            throw new InvalidArgumentException("The given rule map is not an associative array");

        if(!ArrayHelper::valuesAreArray($map))
            throw new InvalidArgumentException("The values of the rule map are not all arrays");

        $this->map = array_merge([], $map);
    }

    public function getMap() : array{
        return $this->map;
    }

    public function hasRulesFor(string $fieldName) : bool{
        return array_key_exists($fieldName, $this->map);
    }

    public function getRulesFor(string $fieldName) : array{
        return $this->map[$fieldName];
    }

    public function asArray() : array{
        return $this->map;
    }
}