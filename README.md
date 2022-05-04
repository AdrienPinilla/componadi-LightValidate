# Welcome to my LightValidate component 

### Version 1.1.0

## 1 - A simple call

This component valide an argument submited by a form

How it works really simply, this is an exemple of call :

```php
$name = new LightValidate();
$resultName = $name->validate('name', 'POST', 'a', [ 'min' => 3 ]);
```
- First parameter `'name'` -> is name of your variable in `$_POST['name']` (in this case is in POST method)
- Second parameter `'POST'` -> define method of your form
- Third parameter `'a'` -> define type of your variable in your form (in this case is a string, the types definition will be in the second part)
- Fourth parameter `[ 'min' => 3 ]` -> define a minimum lengt of our string variable in this case (the min/max definition will be in the second part)

### 1.1 - Result of `validate()` methode

The result of this method is an array with the information about test exemple :

if in `$_POST['name']` contains : Pierre

```php
$name = new LightValidate();
$resultName = $name->validate('name', 'POST', 'a', [ 'min' => 3 ]);

// $resultName contains
$resultName = [
    'data' => "Pierre",
    'isValide' => true,
    'errorMessage' => "everything is ok !"
];

// Whith others parameters
$name = new LightValidate();
$resultName = $name->validate('name', 'POST', 'a', [ 'min' => 8 ]);

//$resultName contains
$resultName = [
    'data' => "Pierre",
    'isValide' => false,
    'errorMessage' => "Your data is too short, your data need longer or equal than : 8"
];
```

## 2 - Method, Type, Min/Max Definitions  

### 2.1 - Method Definition

You need to definate a method when you call `validate()`, this is the array who contains valides method and call the rigth INPUT in `filter_input`:

```php
private $validateMethod = [
        'GET' => INPUT_GET,
        'POST' => INPUT_POST
    ];
```

### 2.2 - Type Definition

You need to definate a type of your variable you want to test when you call `validate()`, this is th array who contains valides type and call the right validation methode (or sanitize) in `filter_input`:

```php
private $validateType = [
    'i' => FILTER_VALIDATE_INT,
    'f' => FILTER_VALIDATE_FLOAT,
    'a' => FILTER_SANITIZE_SPECIAL_CHARS,
    'pwd' => FILTER_SANITIZE_SPECIAL_CHARS,
    'url' => FILTER_VALIDATE_URL,
    'mail' => FILTER_VALIDATE_EMAIL
];
```

- `'i'` => to test an integer
- `'f'` => to test a float
- `'a'` => to test a string
- `'pwd'` => to test a password (you can define or add spécial character authorized)
- `'url'` => to test an url
- `'mail'` => to test an email

### 2.3 - Min/Max Definition

You can define a min or max value you want for your variable, both or just one or nothing (this is an optionnal arguments).

Their are 4 value authorised : <br/>
  - `'min'` -> define a min value
  - `'min-e'` -> define a min or equal value (not available for string type)
  - `'max'` -> define a max value
  - `'max-e'` -> define a max or equal value (not available for string type)

If you want to define a min or max value you need to define it in an array like this :

```php
// Exemples about Fourth parameter
[ 'min' => 3 , 'max-e' => 20]
[ 'min-e' => 5 ]
[ 'max' => 10 ]
```

## 3 - Modification of Invalid Special Character to test password

By default invalide special character are : `[,:;=|'<>.^*()]`

But you can modify, add or remove special character

If you want to see or know current invalide special character you can use :
```php
$password = new LightValidate();

echo $password->getInvalideSpecialCharacter(); // -> return "[,:;=|'<>.^*()]" if you modify nothing before
```
### 3.1 - Method `addInvalideSpecialCharacter()`

You can add one or more invalide special character :

```php
$newInvalideSpecialCharacter = "{}";

$password = new LightValidate();

$password->addInvalideSpecialCharacter($newInvalideSpecialCharacter); 

// now if you want to see current invalide special character
echo $password->getInvalideSpecialCharacter(); // -> return "[,:;=|'<>.^*()]{}" 
```

if you want to add `[` or `]` character you need to write `\[` or `\]`

### 3.2 Method `removeInvalideSpecialCharacter()`

You can remove one or more invalid special character :

```php
// we have not modify $invalideSpecialCharacter before
// we have "[,:;=|'<>.^*()]" value
$removeInvalideSpecialCharacter = "=*";

$password = new LightValidate();

$password->removeInvalideSpecialCharacter($removeInvalideSpecialCharacter); 

// now if you want to see current invalide special character
echo $password->getInvalideSpecialCharacter(); // -> return "[,:;|'<>.^()]{}" 
```

### 3.3 Method `setInvalideSpecialCharacter()`

You can set invalid character
```php
// we have not modify $invalideSpecialCharacter before
// we have "[,:;=|'<>.^*()]" value
$InvalideSpecialCharacter = "^]!§";

$password = new LightValidate();

$password->setInvalideSpecialCharacter($InvalideSpecialCharacter); 

// now if you want to see current invalide special character
echo $password->getInvalideSpecialCharacter(); // -> return "^]!§" 
```