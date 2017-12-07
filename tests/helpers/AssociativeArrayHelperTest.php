<?php
namespace YavlPhp\Tests\Helpers;

use stdClass;
use YavlPhp\Tests\PHPUnit;
use YavlPhp\Helpers\AssociativeArrayHelper;

class AssociativeArrayHelperTest extends PHPUnit {
    /**
     * @test
     * @covers AssociativeArrayHelper::isAssociative
     * @dataProvider nonAssociative
     * @param array $arr
     */
    public function nonAssociativeIsNotAssociative(array $arr){
        self::assertFalse(
            AssociativeArrayHelper::isAssociative($arr)
        );
    }

    public function nonAssociative(){
        return [
            [ [0,1,2] ],
            [ ["a","b"] ],
            [ ["a",new stdClass] ],
        ];
    }

    /**
     * @test
     * @covers AssociativeArrayHelper::isAssociative
     * @dataProvider fakeAssociative
     * @param array $arr
     */
    public function fakeAssociativeIsNotAssociative(array $arr){
        self::assertFalse(
            AssociativeArrayHelper::isAssociative($arr)
        );
    }

    public function fakeAssociative(){
        return [
            [
                ["0"=>1, 1=>1]
            ],
            [
                ["0"=>1, "1"=>1]
            ],
            [
                [0=>1, 1=>1]
            ]
        ];
    }

    /**
     * @test
     * @covers AssociativeArrayHelper::isAssociative
     * @dataProvider associative
     * @param array $arr
     */
    public function associativeIsAssociative(array $arr){
        self::assertTrue(
            AssociativeArrayHelper::isAssociative($arr)
        );
    }

    public function associative(){
        return [
            [
                ["0"=>1, "a"=>1]
            ],
            [
                ["0"=>1, "1"=>1, "b"=>"c"]
            ],
            [
                ["a"=>1, "b"=>1]
            ]
        ];
    }
}