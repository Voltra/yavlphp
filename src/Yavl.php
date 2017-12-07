<?php
namespace YavlPhp;

use InvalidArgumentException;
use YavlPhp\Components\YavlLocaleMap;
use YavlPhp\Components\YavlFieldsMap;
use YavlPhp\Components\YavlValidationFunction;
use YavlPhp\Helpers\AssociativeArrayHelper;
use YavlPhp\Helpers\JsonRead;
use YavlPhp\rules\YavlMatch;
use YavlPhp\rules\YavlMax;
use YavlPhp\Rules\YavlMaxLength;
use YavlPhp\Rules\YavlMin;
use YavlPhp\Rules\YavlMinLength;
use YavlPhp\Rules\YavlRegex;

class Yavl {
    /**
     * @var YavlFieldsMap
     */
    protected $fields;

    /**
     * @var YavlLocaleMap
     */
    protected $locale;

    /**
     * @var array
     */
    protected $pluginRules;

    /**
     * @var array
     */
    protected $coreRules;

    public function __construct(YavlFieldsMap $fields, YavlLocaleMap $locale) {
        $this->pluginRules = [];
        $this->coreRules = [];

        //Defining core rules
        $this->coreRules["min"] = new YavlMin();
        $this->coreRules["max"] = new YavlMax();
        $this->coreRules["regex"] = new YavlRegex();
        $this->coreRules["minLength"] = new YavlMinLength();
        $this->coreRules["maxLength"] = new YavlMaxLength();
        $this->coreRules["match"] = new YavlMatch();

        $this->fields = $fields;
        $this->locale = $locale;
    }

    public static function fromJson(string $rulesPath, string $localePath) : self{
        $fields = JsonRead::from($rulesPath);
        $localeArray = JsonRead::from($localePath);

        $fields = new YavlFieldsMap($fields["fields"]);
        $locale = new YavlLocaleMap($localeArray);

        return new self($fields, $locale);
    }

    public function validate(?array $fieldsValues = null) : array{
        if(is_null($fieldsValues)){
            if($_SERVER["REQUEST_METHOD"] === "GET")
                $fieldsValues = array_merge([], $_GET);
            else
                $fieldsValues = array_merge([], $_POST);
        }

        if(!AssociativeArrayHelper::isAssociative($fieldsValues))
            throw new InvalidArgumentException("The fields values array is not an associative array : fieldName=>fieldValue");

        $errors = [];

        array_walk($this->fields->getMap(), function(array $rules, string $field) use($fieldsValues, $errors){
            $isFilled = $fieldsValues[$field] !== "";
            $required = $this->isRequired($field, $rules);


            if($isFilled || $required){
                $value = $fieldsValues[$field];

                if($required){
                    if(!$isFilled) {
                        $errors[$field] = $this->locale->get("required");
                        return false;
                    }
                }

                if($rules){
                    if(array_key_exists("type", $rules)){
                        switch($rules["type"]){
                            case "int":
                                $value = intval($value);
                                if(is_nan($value))
                                    return $errors[$field] = $this->locale->get("NaN");
                                break;

                            case "float":
                                $value = floatval($value);
                                if(is_nan($value))
                                    return $errors[$field] = $this->locale->get("NaN");
                                break;

                            case "bool":
                                $value = boolval($value);
                                break;
                            default:
                                break;
                        }
                    }

                    foreach($rules as $rule => $expected){
                        if(array_key_exists($rule, $this->coreRules)){
                            $invalid = $this->coreRules["{$rule}"](
                                $this->locale,
                                $value,
                                $expected,
                                $fieldsValues
                            );

                            if($invalid) {
                                $errors[$field] = $invalid;
                                break;
                            }
                        }elseif(array_key_exists($rule, $this->pluginRules)){
                            $invalid = $this->pluginRules["{$rule}"](
                                $this->locale,
                                $value,
                                $expected,
                                $fieldsValues
                            );

                            if($invalid) {
                                $errors[$field] = $invalid;
                                break;
                            }
                        }
                    }
                }
            }
            return false;
        });

        return $errors;
    }

    protected function isRequired(string $field, array $rules) : bool{
        if(array_key_exists("required", $rules))
            return boolval( $rules["required"] );

        return false;
    }

    public function registerPlugin(string $ruleName, YavlValidationFunction $ruleFunction) : self{
        $this->pluginRules[$ruleName] = $ruleFunction;
        return $this;
    }

    public function removePlugin(string $ruleName) : self{
        unset($this->pluginRules[$ruleName]);
        return $this;
    }
}