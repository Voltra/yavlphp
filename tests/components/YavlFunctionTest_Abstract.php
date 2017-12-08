<?php
namespace YavlPhp\Tests\Components;

use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Tests\PHPUnit;
use YavlPhp\Components\YavlValidationFunction;

abstract class YavlFunctionTest_Abstract extends PHPUnit{
    /**
     * @var YavlLocaleMap
     */
    protected $locale;

    /**
     * @var string
     */
    protected $className = YavlValidationFunction::class;

    public function __construct(?string $name = null, array $data = [], string $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->className = $this->getClassName();
        $this->locale = $this->makeLocale();
    }

    public abstract function validProvider() : array;
    public abstract function invalidProvider() : array;
    public function makeLocale() : YavlLocaleMap{
        $className = $this->className;
        $name = (new $className())->getNameForJson();
        return new YavlLocaleMap([
            "{$name}" => "unit test"
        ]);
    }
    public abstract function makeFields() : array;
    public abstract function getClassName() : string;

    /**
     * @test
     * @covers YavlValidationFunction::__call
     * @dataProvider validProvider
     * @param YavlLocaleMap $locale
     * @param $value
     * @param $expected
     * @param array $fieldsValues
     */
    public function ifValidThenNull(YavlLocaleMap $locale, $value, $expected, array $fieldsValues){
        $test = new $this->className();

        self::assertNull($test->call($locale, $value, $expected, $fieldsValues));
    }

    /**
     * @test
     * @covers YavlValidationFunction::__call
     * @dataProvider invalidProvider
     * @param YavlLocaleMap $locale
     * @param $value
     * @param $expected
     * @param array $fieldsValues
     */
    public function ifInvalidThenAppropriateErrorMessage(YavlLocaleMap $locale, $value, $expected, array $fieldsValues){
        $test = new $this->className();
        self::assertEquals(
            $locale->get($test->getNameForJson()),
            $test->call($locale, $value, $expected, $fieldsValues)
        );
    }
}