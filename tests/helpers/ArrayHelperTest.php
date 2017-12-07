<?php
namespace YavlPhp\Tests\Helpers;

use stdClass;
use YavlPhp\Tests\PHPUnit;
use YavlPhp\Helpers\ArrayHelper;

class ArrayHelperTest extends PHPUnit{
    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider allArray
     * @param array $arr
     */
    public function allValuesAreArraysMeansAllValuesAreArrays(array $arr){
        self::assertTrue(ArrayHelper::valuesAreArray($arr));
    }

    public function allArray(){
        return [
            [
                [[4,5,6], [2,3], [1]]
            ],
            [
                [["a",5,6], ["a", "b"], [1]]
            ]
        ];
    }

    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider someAreNotArrays
     * @param array $arr
     */
    public function someValuesAreNotArraysMeansAllValuesAreNotArrays(array $arr){
        self::assertFalse(ArrayHelper::valuesAreArray($arr));
    }

    public function someAreNotArrays(){
        return [
            [
                [["a", "b"], [0], 1]
            ],
            [
                [["a", "b"], [0, "ab", new stdClass], 1]
            ],
            [
                [["a", "b"], 0, 1]
            ],
        ];
    }

    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider noneIsArray
     * @param array $arr
     */
    public function noneIsAnArrayMeansAllValuesAreNotArrays(array $arr){
        self::assertFalse(ArrayHelper::valuesAreArray($arr));
    }

    public function noneIsArray(){
        return [
            [
                ["a",0],
            ],
            [
                [0],
            ],
            [
                [],
            ],
        ];
    }





    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider allStrings
     * @param array $arr
     */
    public function allValuesAreStringsMeansAllValuesAreStrings(array $arr){
        self::assertTrue(ArrayHelper::valuesAreString($arr));
    }

    public function allStrings(){
        return [
            [
                ["", "a", "cdc", "404", "unit", "test"]
            ],
            [
                ["", "a", "404", "php", "0"]
            ]
        ];
    }

    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider someAreNotStrings
     * @param array $arr
     */
    public function someValuesAreNotStringsMeansAllValuesAreNotStrings(array $arr){
        self::assertFalse(ArrayHelper::valuesAreString($arr));
    }

    public function someAreNotStrings(){
        return [
            [
                ["ace", 0, "batman"]
            ],
            [
                ["jack", true, false]
            ],
            [
                ["3.5mm", null, NAN]
            ],
        ];
    }

    /**
     * @test
     * @covers ArrayHelper::valuesAreArray
     * @dataProvider noneIsString
     * @param array $arr
     */
    public function noneIsAStringMeansAllValuesAreNotStrings(array $arr){
        self::assertFalse(ArrayHelper::valuesAreString($arr));
    }

    public function noneIsString(){
        return [
            [
                [true,0],
            ],
            [
                [0, new stdClass, []],
            ],
            [
                [],
            ],
        ];
    }
}