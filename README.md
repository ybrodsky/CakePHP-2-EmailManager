# CakePHP 2 EmailManager
CakePHP 2 Behavior to interact with the EmailManager API

## Usage
Define the basic configuration
```php
  Configure::write('EmailManager', array(
	  'config' => array(
  		'domain' => 'YOUR_DOMAIN',
  		'username' => 'YOUR_USERNAME',
  		'password' => 'YOUR_PASSWORD',
  		'output' => 'php',
  		'language' => 'en_US | es_ES'    //Optional 
	  )	
));

```

Assign the behavior to your model
```php
  public $actsAs = array('EmailManager');
```

To interact with the api we simply call 'callMethod'. 
The first parameter is the API method you want to call, the second parameter is an array with options.

```php
  $res = $this->Model->callMethod('campaigns', array(
    'parent_id' => '10'
  ));
```

```php
  $res = $this->Model->callMethod('contacts', array(
    'date_begin' => '2015-01-01', 
    'limit' => 10
  ));
```

```php
  $res = $this->Model->callMethod('contactCreate', array(
    'name' => 'TehName TehSurname', 
    'email' => 'email@mail.com',
    'group_id' => 10
  ));
```
A full list of the methods can be found in EmailManager's API official documentation.
