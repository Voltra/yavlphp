<?php
namespace YavlPhp\Tests\Components;

use InvalidArgumentException;
use stdClass;
use YavlPhp\Components\YavlFieldsMap;
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
}