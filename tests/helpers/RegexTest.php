<?php
namespace YavlPhp\Tests\Helpers;

use YavlPhp\Helpers\Regex;
use YavlPhp\Tests\PHPUnit;

class RegexTest extends PHPUnit{
    protected function make(string $re) : Regex{
        return new Regex($re);
    }

    /**
     * @test
     * @covers Regex::appliesTo
     * @dataProvider notDigit
     * @param string $subject
     */
    public function noMatchMeansThatItDoesNotApply(string $subject){
        $re = self::make("/\\d/");
        self::assertFalse($re->appliesTo($subject));
    }

    public function notDigit(){
        return [
            ["ac"],
            ["a"],
            ["abdc"]
        ];
    }

    /**
     * @test
     * @covers Regex::appliesTo
     * @dataProvider digits_a
     * @param string $subject
     */
    public function matchMeansThatItApplies(string $subject){
        $re = self::make("/\\d+a/");
        self::assertTrue($re->appliesTo($subject));
    }

    public function digits_a(){
        return [
            ["24a"],
            ["2a"],
            ["2a"],
        ];
    }

    /**
     * @test
     * @covers Regex::extractGroupsFrom
     * @dataProvider digits_a
     * @param string $subject
     */
    public function noGroupWhenDoesNotMatch(string $subject){
        $re = self::make("/^(€)\d+.(\d)$/");
        self::assertEmpty($re->extractGroupsFrom($subject));
    }

    /**
     * @test
     * @covers Regex::extractGroupsFrom
     * @dataProvider money
     * @param string $subject
     */
    public function groupsAreInCorrectOrder(string $subject){
        $re = self::make("/^(€)[1-9]+\d*(,)\d*[1-9]+$/");
        self::assertEquals(
            ["€", ","],
            $re->extractGroupsFrom($subject)
        );
    }

    public function money(){
        return [
            ["€1,23"],
            ["€108,99"]
        ];
    }
}