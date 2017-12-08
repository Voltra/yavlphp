# What is yavlphp ?

The PHP implementation of yavljs (https://www.npmjs.com/package/yavljs) that allows you to use the same definitions for front-end and back-end validation.





# Table of content

[TOC]

# Why YavlJS for PHP ?

[yavljs](https://www.npmjs.com/package/yavljs) is a javascript library for front-end form validation. Sadly, front-end validation is merely for the user's experience : a developer should **NEVER** rely on it for the complete validation of the submitted data.

Therefore, I decided to develop its equivalent in PHP.

My goal was that you could both validate on the front-end and back-end part of your application, all that using the same files.



# What are the key differences with YavlJS ?

There's only one difference, the **field's name** .

Where using [yavljs](https://www.npmjs.com/package/yavljs) you could simply give names for the sake of semantic, in [yavlphp] things are different, let's illustrate this with an example:

```html
<form id="form">
  <input type="text" name="field1"/>
  <input type="text" name="field2"/>
  <input type="text" name="field3"/>
  
  <button type="submit">
    Submit
  </button>
</form>
```

## With yavljs

```json
{
  "form": "#form",
  "fields": {
    "first field": {
      "selector": "[name='field1']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 3,
      	"max": 6
      }
    },
    "second field": {
      "selector": "[name='field2']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 8,
      	"max": 12
      }
    },
    "third field": {
      "selector": "[name='field3']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 24,
      	"max": 32
      }
    },
  }
}
```

## With yavlphp

```json
{
  "form": "#form",
  "fields": {
    "field1": {
      "selector": "[name='field1']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 3,
      	"max": 6
      }
    },
    "field2": {
      "selector": "[name='field2']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 8,
      	"max": 12
      }
    },
    "field3": {
      "selector": "[name='field3']",
      "error_selector": "*",
      "required": true,
      "type": "int",
      "rules": {
        "min": 24,
      	"max": 32
      }
    },
  }
}
```



As you can see, the names that were only used for semantic are now required to be the field's name.

This is due to the fact that the vast majority of use cases will use the request's variable to validate the form.

Other than this, there are no differene in how the validation process is executed.



# How to install yavlphp ?

I highly recommend you to use [composer](https://getcomposer.org/) if you are not using it already, it is a very handy PHP package manager.

With composer installed, simply run the following command in your application's folder:

`composer require voltra/yavlphp`

This will automatically install [yavlphp] and its dependencies (if there are any).



# How to use yavlphp ?

## The instance of the validator

This is very simple, you will use an instance of `YavlPhp\Yavl`. You can either create it directly from a `YavlPhp\Components\YavlFieldsMap` and a `YavlPhp\Components\YavlLocaleMap` .

```php
use YavlPhp\Yavl;
use YavlPhp\Components\YavlFieldsMap;
use YavlPhp\Components\YavlLocaleMap;

$validationSettings = new YavlFieldsMap(/* check the documentations for parameters */);
$errorMessages = new YavlLocaleMap(/* check the documentations for parameters */);

$v = new Yavl($validationSettings, $errorMessages);
```



Or you can directly skip those steps and use your JSON files instead :

```php
use YavlPhp\Yavl;

function getUrlFromServerRoot(string $path) : string{}

$v = Yavl::fromJson(
	getUrlFromServerRoot("assets/json/formx/form.json"),
  	getUrlFromServerRoot("assets/json/formx/locale.json")
);
```

**Sidenote :** Both `Yavl\Php\Components\YavlLocaleMap` and a path to the locale file are optionnal, they are defaulted (but it is highly recommended to give one).

## Actually validating the data

Now that you have youre `Yavl` instance ready to use, you will use its method `validate` which can take an argument : the associative array of data, the association being `fieldName => fieldValue`.

Note that if you don't provide this argument, it'll try to get the arguments itself from either `$_GET` or `$_POST` (will go for `$_GET` if the method is GET, will go for `$_POST` if it is not GET).



```php
$errors = $v->validate();

$errors = $v->validate($formFieldsAssociativeArray);
```

The validation is then being processed and errors are reported in the return value of the method.

The `validate` method returns an associative array that associates an error message to the field's name :

When a validation rule is not respected (required, regex, etc...) it will set the corresponding error message and go on with the other fields.



Therefore, if there were no validation error (meaning that the data passed is completely validated) then you would have an empty array as a result.

## Handling errors

Let's say that you have the following form:

```html
<form>
  <input type="text" name="username"/>
  <input type="text" name="password"/>
  <button type="submit">Submit</button>
</form>
```

You can handle errors like so :

```php
$errors = $v->validate;
if(!empty( $errorss )){
  rediret_to_form_with_errors("some/url/to/the/form", $errors);
  //This is not an actual function ;)
}else{
  //go on with your correct data !
}
```



# How to extend yavlphp (plugins) ?

## Creating the plugin

This is very simple, a validation rule/function is an object of a class that inherits from `YavlPhp\Components\YavlValidationFunction` which has two methods that you need to override :

* `call` &rarr; The method that checks whether or not the data is valid
* `getNameForJson` &rarr; The method that returns the name of this rule as it should be in the JSON file



Here are their signatures:

```php
abstract function call(YavlLocaleMap $locale, $value, $expected, array $fieldsValues) : ?string;

abstract function getNameForJson() : string;
```

Note that, if the data (`$value`) is valid, then `call` would return `null`, otherwise it would return the corresponding error message from `$locale`.



For instance, let's take a look at `YavlPhp\Rules\YavlMaxLength` which is the validation function for the `maxLength` rule (in the JSON file):

```php
final class YavlMaxLength extends YavlValidationFunction {

    public function call(
      YavlLocaleMap $locale,
      $value,
      $expected,
      array $fieldsValues
    ): ?string {
      //$expected is the value given in the JSON file for this rule
      //$value is the field's value
        return (strlen("{$value}") <= intval($expected))
          ? null //It is less than or equal to the maximum value, so return null
          : $locale->get($this->getNameForJson()); //It is not, fetch the error message
    }

    public function getNameForJson(): string {
        return "maxLength";
    }
}
```

If your error message needs any kind of manipulation (for instance, replacing `%input%` by the `$value` variable) you can use either `preg_replace` or `str_replace`.

Note that `%value%` is always replaced with the value of `$expected`, unless you replace it first.



## Adding the plugin to the validator

Once you have your plugin class ready, you can totally add it to your validator using its method `registerPlugin`:

```php
$v = /*get a Yavl instance*/;
$v->registerPlugin(new AwesomeYavlPlugin());
```

It will deduce the name (`getNameForJson`) automatically. Be aware that doing so, you might override someone else's plugin so be careful.



## Removing a plugin

If by any chance you wanted to remove a plugin, you can do so by using your validator's method `removePlugin`.

This method takes as an argument the name of your plugin (cf. the  `getNameForJson` method).



## Get a list of the defined plugins

You can also get a list of the defined plugins, this might be useful if you have to determine a new name for you plugin (if your initial one was already in use).

To do so, you need to call your validator's method `getPluginNamesList` which returns the names of all the plugins (cf. the `getNameforJson`method).

