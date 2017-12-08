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

    public function __construct(YavlFieldsMap $fields, ?YavlLocaleMap $locale = null) {
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
        $this->locale = is_null($locale) ? new YavlLocaleMap([]) : $locale;
    }

    public static function fromJson(string $rulesPath, ?string $localePath=null) : self{
        $fields = JsonRead::from($rulesPath);
        $localeArray = is_null($localePath) ? [] : JsonRead::from($localePath);

        if(!array_key_exists("fields", $fields))
            throw new InvalidArgumentException("The given rules JSON file doesn't have a 'fields' property");

        $fields = new YavlFieldsMap($fields["fields"]);
        $locale = new YavlLocaleMap($localeArray);

        return new self($fields, $locale);
    }

    public function validate(?array $fieldsValues = null) : array{
        if(is_null($fieldsValues)){
            if($_SERVER["REQUEST_METHOD"] === "GET")
                return $this->validate($_GET);
            else
                return $this->validate($_POST);
        }

        if(!AssociativeArrayHelper::isAssociative($fieldsValues))
            throw new InvalidArgumentException("The fields values array is not an associative array : fieldName=>fieldValue");

        $errors = [];
        $fields = $this->fields->getMap();

        array_walk($fields, function(array $fieldSettings, string $field) use($fieldsValues, &$errors){
            $rules = array_key_exists("rules", $fieldSettings) ? $fieldSettings["rules"] : null;
            $isFilled = isset($fieldsValues[$field]);
            $required = $this->isRequired($fieldSettings);


            if($isFilled || $required){
                if($required){
                    if(!$isFilled) {
                        $errors[$field] = $this->locale->get("required");
                        return false;
                    }
                }

                $value = $fieldsValues[$field];

                if($rules){
                    if(array_key_exists("type", $fieldSettings)){
                        switch($fieldSettings["type"]){
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
                            $invalid = $this->coreRules["{$rule}"]->call(
                                $this->locale,
                                $value,
                                $expected,
                                $fieldsValues
                            );

                            if(!is_null($invalid)) {
                                $errors[$field] = str_replace("%value%", $expected, $invalid);
                                break;
                            }
                        }elseif(array_key_exists($rule, $this->pluginRules)){
                            $invalid = $this->pluginRules["{$rule}"]->call(
                                $this->locale,
                                $value,
                                $expected,
                                $fieldsValues
                            );

                            if(!is_null($invalid)) {
                                $errors[$field] = str_replace("%value%", $expected, $invalid);
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

    protected function isRequired(array $fieldSettings) : bool{
        if(array_key_exists("required", $fieldSettings))
            return boolval( $fieldSettings["required"] );

        return false;
    }

    public function registerPlugin(YavlValidationFunction $ruleFunction) : self{
        $ruleName = $ruleFunction->getNameForJson();
        $this->pluginRules[$ruleName] = $ruleFunction;
        return $this;
    }

    public function removePlugin(string $ruleName) : self{
        unset($this->pluginRules[$ruleName]);
        return $this;
    }

    public function getPluginNamesList() : array{
        return array_keys($this->pluginRules);
    }
}