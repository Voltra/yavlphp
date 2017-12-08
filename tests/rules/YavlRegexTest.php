<?php

namespace YavlPhp\Tests\rules;

use YavlPhp\Tests\Components\YavlFunctionTest_Abstract;
use YavlPhp\Rules\YavlRegex;

class YavlRegexTest extends YavlFunctionTest_Abstract{

    public function validProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();

        return [
            [$loc,$fields["a"],"^a+$",$fields],
            [$loc,$fields["unit"],"(€|\\$)",$fields],
            [$loc,$fields["test"],"4+",$fields],
            [$loc,$fields["is"],"\\d",$fields],
            [$loc,$fields["pain"],"^\\w{2}d+\\w$",$fields],
        ];
    }

    public function invalidProvider(): array {
        $loc = $this->locale;
        $fields = $this->makeFields();

        return [
            [$loc,$fields["a"],"^a$",$fields],
            [$loc,$fields["unit"],"^\\$$",$fields],
            [$loc,$fields["test"],"^4+$",$fields],
            [$loc,$fields["is"],"\\d{2,3}",$fields],
            [$loc,$fields["pain"],"^\\d+$",$fields],
        ];
    }

    public function makeFields(): array {
        return [
            "a" => "aaa",
            "unit" => "8.4€",
            "test" => "44,4",
            "is" => "1",
            "pain" => "acddb"
        ];
    }

    public function getClassName(): string {
        return YavlRegex::class;
    }
}