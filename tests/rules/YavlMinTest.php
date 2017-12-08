<?php
namespace YavlPhp\Tests\rules;

use YavlPhp\rules\YavlMin;
use YavlPhp\Tests\Components\YavlFunctionTest_Abstract;

class YavlMinTest extends YavlFunctionTest_Abstract{

    public function validProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();
        return [
            [$loc, $fields["a"], -1, $fields],
            [$loc, $fields["unit"], 0, $fields],
            [$loc, $fields["test"], 0, $fields],
            [$loc, $fields["is"], 0, $fields],
            [$loc, $fields["pain"], 5, $fields]
        ];
    }

    public function invalidProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();
        return [
            [$loc, $fields["a"], 10, $fields],
            [$loc, $fields["unit"], 10, $fields],
            [$loc, $fields["test"], 50, $fields],
            [$loc, $fields["is"]-4, $fields["is"], $fields],
            [$loc, $fields["pain"]-4, $fields["pain"], $fields]
        ];
    }

    public function makeFields(): array {
        return [
            "a" => 0,
            "unit" => 1,
            "test" => 42,
            "is" => 29,
            "pain" => 10
        ];
    }

    public function getClassName(): string {
        return YavlMin::class;
    }
}