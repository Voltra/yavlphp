<?php
namespace YavlPhp\Tests\rules;

use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\rules\YavlMatch;
use YavlPhp\Tests\Components\YavlFunctionTest_Abstract;

class YavlMatchTest extends YavlFunctionTest_Abstract{

    public function validProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();
        return [
            [$loc, "a", "d", $fields],
            [$loc, "word", "passe", $fields]
        ];
    }

    public function invalidProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();
        return [
            [$loc, "z", "passe", $fields],
            [$loc, "word", "unit", $fields]
        ];
    }

    public function makeFields(): array {
        return [
            "a" => "z",
            "b" => "c",
            "d" => "a",
            "e" => "a",
            "unit" => "test",
            "pass" => "word",
            "passe" => "word"
        ];
    }

    public function getClassName(): string {
        return YavlMatch::class;
    }
}