<?php
namespace YavlPhp\Tests;

use InvalidArgumentException;
use phpmock\Mock;
use phpmock\MockBuilder;
use phpmock\MockEnabledException;
use PHPUnit\Util\Json;
use ReflectionClass;
use stdClass;
use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlValidationFunction;
use YavlPhp\Helpers\ArrayHelper;
use YavlPhp\Helpers\AssociativeArrayHelper;
use YavlPhp\Helpers\JsonRead;
use YavlPhp\Tests\PHPUnit;
use YavlPhp\Yavl;

function rel(string $path){
    return dirname(__FILE__) . "/{$path}";
}

class YavlTest extends PHPUnit{
    public static function getNamespaceFor(string $class){
        $ref = new ReflectionClass($class);
        return $ref->getNamespaceName();
    }

    protected $str_replace;

    public function __construct(?string $name = null, array $data = [], string $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->str_replace = self::getMockForStrReplace();
        try {
            $this->str_replace->enable();
        }catch(MockEnabledException $e){
            $this->str_replace->disable();
            $this->str_replace->enable();
        }
    }

    public function __destruct() {
        $this->str_replace->disable();
    }

    public static function getMockForStrReplace() : Mock{
        return (new MockBuilder())
            ->setName("str_replace")
            ->setNamespace(self::getNamespaceFor(Yavl::class))
            ->setFunction(function(string $re, string $rep, string $subject){
                return $subject;
            })->build();
    }

    /**
     * @test
     * @covers Yavl::__construct
     * @covers Yavl::fromJson
     * @expectedException InvalidArgumentException
     */
    public function cannotConstructFromJsonWithBadLocale(){
        Yavl::fromJson(rel("form.json"), rel("invalidLocale.json"));
    }

    /**
     * @test
     * @covers Yavl::__construct
     * @covers Yavl::fromJson
     * @expectedException InvalidArgumentException
     */
    public function cannotConstructFromJsonWithBadFormSettings(){
        Yavl::fromJson(rel("invalidForm.json"), rel("locale.json"));
    }

    /**
     * @test
     * @covers Yavl::__construct
     */
    public function canConstructFromValidJson(){
        Yavl::fromJson(rel("form.json"), rel("locale.json"));
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers Yavl::__construct
     * @covers Yavl::fromJson
     * @depends canConstructFromValidJson
     */
    public function canConstructWithoutLocale(){
        Yavl::fromJson(rel("form.json"));
        self::assertTrue(true);
    }

    protected function getYavl() : Yavl{
        return Yavl::fromJson( rel("form.json") );
    }

    /**
     * @test
     * @covers Yavl::validate
     * @depends canConstructWithoutLocale
     * @dataProvider validDataProvider
     * @param array $data
     */
    public function ifValidThenNoErrors(array $data){
        $v = $this->getYavl();
        $this->assertEmpty(
            $v->validate($data)
        );
    }

    public function validDataProvider(){
        return [
            [[
                "a" => "abcd123",
                "unit" => "16"
            ]],
            [[
                "a" => "acdc44",
                "unit" => "10"
            ]],
            [[
                "a" => "abcd12345"
            ]],
        ];
    }

    /**
     * @test
     * @covers Yavl::validate
     * @depends canConstructWithoutLocale
     * @dataProvider nonAssociativeDataProvider
     * @expectedException InvalidArgumentException
     * @param array $data
     */
    public function ifDataIsNotAssociativeThenQuits(array $data){
        $v = $this->getYavl();
        $v->validate($data);
    }

    public function nonAssociativeDataProvider(){
        return [
            [[]],
            [[1,2,3]],
            [["a", 1, new stdClass]]
        ];
    }

    /**
     * @test
     * @covers Yavl::validate
     * @depends canConstructWithoutLocale
     * @dataProvider invalidDataProvider
     * @param array $data
     */
    public function ifInvalidThenErrors(array $data){
        $v = $this->getYavl();
        $res = $v->validate($data);

        $this->assertNotEmpty($res);
        $this->assertTrue(AssociativeArrayHelper::isAssociative($res));
        $this->assertTrue(ArrayHelper::valuesAreString($res));
    }

    public function invalidDataProvider(){
        return [
            [[
                "a" => "abcd0123",
                "unit" => "911"
            ]],
            [[
                "a" => "a",
                "unit" => "404"
            ]],
            [[
                "a" => 0
            ]],
        ];
    }

    /**
     * @test
     * @covers Yavl::validate
     * @depends ifInvalidThenErrors
     * @dataProvider invalidAndErrorDataProvider
     * @param array $data
     */
    public function ifInvalidThenCorrespondingError(array $data){
        $errors = $data["ERRORS"];
        unset($data["ERRORS"]);
        $v = $this->getYavl();
        $res = $v->validate($data);

        self::assertNotEmpty(array_filter($res, function(string $err_msg, string $key) use($errors){
            $relatedErrorMessages = $errors[$key];
            return in_array($err_msg, $relatedErrorMessages);
        }, ARRAY_FILTER_USE_BOTH));
    }

    public function invalidAndErrorDataProvider(){
        return [
            [[
                "ERRORS" => [
                    "a" =>  [YavlLocaleMap::defaults["minLength"]],
                    "unit" => [YavlLocaleMap::defaults["min"]]
                ],
                "a" => "aa12",
                "unit" => "3"
            ]],
            [[
                "ERRORS" => [
                    "a" => [
                        YavlLocaleMap::defaults["nomatch_regex"],
                        YavlLocaleMap::defaults["minLength"]
                    ],
                    "unit" => [ YavlLocaleMap::defaults["max"] ]
                ],
                "a" => "a",
                "unit" => "21"
            ]],
        ];
    }

    /**
     * @test
     * @covers Yavl::registerPlugin
     * @dataProvider pluginProvider
     * @param YavlValidationFunction $function
     */
    public function onceRegisteredThePluginAppearsAsModifiedInTheListOfPlugins(YavlValidationFunction $function){
        $v = $this->getYavl();
        $beforeList = $v->getPluginNamesList();
        $v->registerPlugin($function);
        $afterList = $v->getPluginNamesList();

        self::assertEquals(
            [$function->getNameForJson()],
            array_diff($afterList, $beforeList)
        );
    }

    public function pluginProvider(){
        $plugin = new class extends YavlValidationFunction{
            function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
                return $value ? null : "error";
            }

            function getNameForJson(): string {
                return "UNIT TEST";
            }
        };
        return [
            [$plugin],
            [$plugin],
            [$plugin],
            [$plugin]
        ];
    }

    public function getYavlWithUnitTestPlugin() : Yavl{
        $v = $this->getYavl();
        $v->registerPlugin(new class extends YavlValidationFunction{
            function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues): ?string {
                return $value ? null : "error";
            }

            function getNameForJson(): string {
                return "unitTest";
            }
        });

        return $v;
    }

    /**
     * @test
     * @depends onceRegisteredThePluginAppearsAsModifiedInTheListOfPlugins
     * @covers Yavl::removePlugin
     */
    public function onceRemovedTheDifferenceIsTheRemovedPlugin(){
        $v = $this->getYavlWithUnitTestPlugin();
        $before = $v->getPluginNamesList();
        $v->removePlugin("unitTest");
        $after = $v->getPluginNamesList();

        self::assertEquals(
            ["unitTest"],
            array_diff($before, $after)
        );
    }
}