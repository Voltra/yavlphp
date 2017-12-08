<?php
namespace YavlPhp\Tests\components;

use InvalidArgumentException;
use stdClass;
use YavlPhp\Tests\PHPUnit;
use YavlPhp\Components\YavlLocaleMap;

class YavlLocaleMapTest extends PHPUnit{
    /**
     * @test
     * @covers YavlLocaleMap::__construct
     * @dataProvider regularArrayProvider
     * @expectedException InvalidArgumentException
     * @param array $arr
     */
    public function cannotConstructFromRegularArray(array $arr){
        new YavlLocaleMap($arr);
    }

    public function regularArrayProvider(){
        return [
            [ [1,2,3] ],
            [["a", "b"]],
            [ [new stdClass] ]
        ];
    }

    /**
     * @test
     * @covers YavlLocaleMap::__construct
     * @dataProvider fakeAssociativeArrayProvider
     * @expectedException InvalidArgumentException
     * @param array $arr
     */
    public function cannotConstructFromFakeAssociativeArray(array $arr){
        new YavlLocaleMap($arr);
    }

    public function fakeAssociativeArrayProvider(){
        return [
            [
                ["0"=>1, "1"=>1]
            ],
            [
                ["0"=>1, "1"=>1, "2"=>2]
            ],
        ];
    }

    /**
     * @test
     * @covers YavlLocaleMap::__construct
     * @dataProvider regularAssociativeArrayProvider
     * @expectedException InvalidArgumentException
     * @param array $arr
     */
    public function cannotConstructFromRegularAssociativeArray(array $arr){
        new YavlLocaleMap($arr);
    }

    public function regularAssociativeArrayProvider(){
        return [
            [
                ["a"=>1, "cd"=>1]
            ],
            [
                ["unit"=>1, "test"=>1, "php"=>2]
            ],
        ];
    }

    /**
     * @test
     * @covers YavlLocaleMap::__construct
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function canConstructFromCorrectAssociativeArray(array $arr){
        new YavlLocaleMap($arr);
        self::assertTrue(true);
    }

    public function correctAssociativeArrayProvider(){
        return [
            [
                ["a"=>"b", "cd"=>"ef"]
            ],
            [
                ["unit"=>"test", "test"=>"code", "php"=>"language", "a"=>"b"]
            ],
        ];
    }

    /**
     * @test
     * @covers YavlLocaleMap::__construct
     */
    public function canConstructFromEmptyArray(){
        new YavlLocaleMap([]);
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers YavlLocaleMap::__construct
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function hasDefaultKeysWhenConstructed(array $arr){
        $loc = new YavlLocaleMap($arr);
        $locKeys = array_keys($loc->asArray());
        $defaultKeys = array_keys(YavlLocaleMap::defaults);

        self::assertTrue(array_intersect_assoc($locKeys, $defaultKeys) === $defaultKeys);
    }

    /**
     * @test
     * @covers YavlLocaleMap::has
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function ifNotInLocaleThenItDoesNotHaveIt(array $arr){
        $loc = new YavlLocaleMap($arr);
        self::assertFalse($loc->has("UNDEFINED KEY HERE"));
    }

    /**
     * @test
     * @covers YavlLocaleMap::has
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function ifInLocaleThenItHasIt(array $arr){
        $loc = new YavlLocaleMap($arr);
        self::assertTrue($loc->has("a"));
    }

    /**
     * @test
     * @covers YavlLocaleMap::get
     * @dataProvider correctAssociativeArrayProvider
     * @expectedException InvalidArgumentException
     * @param array $arr
     */
    public function cannotGetErrorMessageIfNotInTheLocale(array $arr){
        $loc = new YavlLocaleMap($arr);
        $loc->get("UNDEFINED KEY HERE");
    }

    /**
     * @test
     * @covers YavlLocaleMap::get
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function canGetErrorMessageIfInTheLocale(array $arr){
        $loc = new YavlLocaleMap($arr);
        self::assertTrue(is_string($loc->get("a")));
    }
}