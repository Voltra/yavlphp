<?php
namespace YavlPhp\Tests\rules;

use YavlPhp\Tests\Components\YavlFunctionTest_Abstract;
use YavlPhp\Rules\YavlMaxLength;

class YavlMaxLengthTest extends YavlFunctionTest_Abstract{
    public function validProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();

        return [
            [$loc, $fields["a"], PHP_INT_MAX, $fields],
            [$loc, $fields["unit"], strlen($fields["unit"]), $fields],
            [$loc, $fields["test"], PHP_INT_MAX / 10, $fields],
            [$loc, $fields["is"], PHP_INT_MAX / 100, $fields],
            [$loc, $fields["pain"], PHP_INT_MAX / 1000, $fields],
        ];
    }

    public function invalidProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();

        return [
            [$loc, $fields["a"], strlen($fields["a"])-1, $fields],
            [$loc, $fields["unit"], strlen($fields["unit"])-2, $fields],
            [$loc, $fields["test"], 5, $fields],
            [$loc, $fields["is"], 7, $fields],
            [$loc, $fields["pain"], 0, $fields],
        ];
    }

    public function makeFields(): array {
        return [
            "a" => "a",
            "unit" => "aaa",
            "test" => "aaaaaa",
            "is" => "aaaaaaaa",
            "pain" => "aa"
        ];
    }

    public function getClassName(): string {
        return YavlMaxLength::class;
    }
}