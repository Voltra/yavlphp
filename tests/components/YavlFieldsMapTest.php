<?php
namespace YavlPhp\Tests\Components;

use InvalidArgumentException;
use stdClass;
use YavlPhp\Components\YavlFieldsMap;
use YavlPhp\Helpers\AssociativeArrayHelper;
use YavlPhp\Tests\PHPUnit;

class YavlFieldsMapTest extends PHPUnit{
    /**
     * @test
     * @covers YavlFieldsMap::__construct
     * @expectedException InvalidArgumentException
     * @dataProvider regularArrayProvider
     * @param array $arr
     */
    public function cannotConstructWithRegularArray(array $arr){
        new YavlFieldsMap($arr);
    }

    public function regularArrayProvider(){
        return [
            [[0,1,2,new stdClass]],
            [["a", "b", 0]],
            [[0,1,2]],
            [["a", "b", "c"]]
        ];
    }


    /**
     * @test
     * @covers YavlFieldsMap::__construct
     * @expectedException InvalidArgumentException
     * @dataProvider fakeAssociativeArrayProvider
     * @param array $arr
     */
    public function cannotConstructWithFakeAssociativeArray(array $arr){
        new YavlFieldsMap($arr);
    }

    public function fakeAssociativeArrayProvider(){
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
     * @covers YavlFieldsMap::__construct
     * @expectedException InvalidArgumentException
     * @dataProvider associativeArrayProvider
     * @param array $arr
     */
    public function cannotConstructWithRegularAssociativeArray(array $arr){
        new YavlFieldsMap($arr);
    }

    public function associativeArrayProvider(){
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

    /**
     * @test
     * @covers YavlFieldsMap::__construct
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function canConstructWithCorrectAssociativeArray(array $arr){
        new YavlFieldsMap($arr);
        self::assertTrue(true);
    }

    public function correctAssociativeArrayProvider(){
        return [
            [
                ["0"=>["unit"=>"test"], "b"=>["a"=>1]]
            ],
            [
                ["0"=>["ab"=>"ba"], "1"=>["a"=>1], "b"=>["a"=>1]]
            ],
            [
                ["a"=>["z0"=>0], "b"=>["1"=>1]]
            ]
        ];
    }

    /**
     * @test
     * @covers YavlFieldsMap::getMap
     * @dataProvider correctAssociativeArrayProvider
     * @param array $arr
     */
    public function innerMapIsTheSameAsProvidedMap(array $arr){
        $v = new YavlFieldsMap($arr);
        self::assertEquals($arr, $v->getMap());
    }

    /**
     * @test
     * @covers YavlFieldsMap::hasRulesFor
     * @dataProvider invalidFieldNameAndRulesProvider
     * @param string $fieldName
     * @param array $rules
     */
    public function notInArrayMeansNoRulesForIt(string $fieldName, array $rules){
        $v = new YavlFieldsMap($rules);
        self::assertFalse($v->hasRulesFor($fieldName));
    }

    public function invalidFieldNameAndRulesProvider(){
        return array_map(function(array $paramList){
            return array_merge(["INVALID NAME HERE"], $paramList);
        }, $this->correctAssociativeArrayProvider());
    }

    /**
     * @test
     * @covers YavlFieldsMap::hasRulesFor
     * @dataProvider validFieldNameAndRulesProvider
     * @param string $fieldName
     * @param array $rules
     */
    public function inArrayMeansRulesForIt(string $fieldName, array $rules){
        $v = new YavlFieldsMap($rules);
        self::assertTrue($v->hasRulesFor($fieldName));
    }

    public function validFieldNameAndRulesProvider(){
        return array_map(function(array $paramList){
            return array_merge(["b"], $paramList);
        }, $this->correctAssociativeArrayProvider());
    }

    /**
     * @test
     * @covers YavlFieldsMap::getRulesFor
     * @dataProvider invalidFieldNameAndRulesProvider
     * @expectedException InvalidArgumentException
     * @param string $fieldName
     * @param array $rules
     */
    public function notInArrayMeansThatCannotGetItsRules(string $fieldName, array $rules){
        $v = new YavlFieldsMap($rules);
        $v->getRulesFor($fieldName);
    }

    /**
     * @test
     * @covers YavlFieldsMap::getRulesFor
     * @dataProvider validFieldNameAndRulesProvider
     * @param string $fieldName
     * @param array $rules
     */
    public function inArrayMeansThatCanGetItsRules(string $fieldName, array $rules){
        $v = new YavlFieldsMap($rules);
        $r = $v->getRulesFor($fieldName);
        self::assertTrue(AssociativeArrayHelper::isAssociative($r));
    }

}