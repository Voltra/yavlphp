<?php
namespace YavlPhp\Tests\Helpers;

use PHPUnit\Util\Json;
use YavlPhp\Helpers\AssociativeArrayHelper;
use YavlPhp\Helpers\JsonRead;
use YavlPhp\Tests\PHPUnit;

class JsonReadTest extends PHPUnit{
    /**
     * @test
     * @covers JsonRead::from
     */
    public function aJsonArrayIsAnArray(){
        $arr = JsonRead::from(dirname(__FILE__) . "/array.json");
        self::assertTrue(is_array($arr));
    }

    /**
     * @test
     * @covers JsonRead::from
     */
    public function aJsonObjectIsAnAssociativeArray(){
        $arr = JsonRead::from(dirname(__FILE__) . "/object.json");
        self::assertTrue(AssociativeArrayHelper::isAssociative($arr));
    }

    /**
     * @test
     * @covers JsonRead::from
     */
    public function aJsonStringIsAString(){
        $ctx = JsonRead::from(dirname(__FILE__) . "/string.json");
        self::assertTrue(is_string($ctx));
    }

    /**
     * @test
     * @covers JsonRead::from
     */
    public function aJsonNullIsNull(){
        $ctx = JsonRead::from(dirname(__FILE__) . "/null.json");
        self::assertTrue(is_null($ctx));
    }

    /**
     * @test
     * @covers JsonRead::from
     */
    public function aJsonNumberIsANumeric(){
        $ctx = JsonRead::from(dirname(__FILE__) . "/number.json");
        self::assertTrue(is_numeric($ctx));
    }

    /**
     * @test
     * @covers JsonRead::from
     */
    public function jsonContentIsEquivalentToParsedVariable(){
        $ctx = JsonRead::from(dirname(__FILE__) . "/json.json");
        self::assertJsonStringEqualsJsonFile(
            dirname(__FILE__) . "/json.json",
            json_encode($ctx)
        );
    }
}