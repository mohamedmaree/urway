# urway
## Installation

You can install the package via [Composer](https://getcomposer.org).

```bash
composer require maree/urway
```
Publish your sms config file with

```bash
php artisan vendor:publish --provider="maree\urway\UrwayServiceProvider" --tag="urway"
```
then change your urway config from config/urway.php file
```php
    "merchantKey"  => "" ,
    "name"         => "" ,
    "password"     => "" ,
```
## Usage

- with [visa - master - mada ]
```php
use maree\urway\Urway;
$customerInfo = ['email' => 'customer@site.com'];
Urway::checkout($amount = 1.0 , $customerInfo);  

```
- with [ stc ]

```php
use maree\urway\Urway;
$customerInfo = ['email' => 'customer@site.com'];
Urway::checkoutStc($amount = 1.0 , $customerInfo);  

```

## note 
- define the checkout resonse url with urway team 
- create route for response url
- create function for checkout response 
- use that function to check if payment failed or success

```php
use maree\urway\Urway;
Urway::checkoutResponseStatus();  

```
return json response with 
```php

['key' => 'success' ,'msg' => 'checkout success' ,'result' => $result ,'data' => $_GET ]  

```
or 

```php

['key' => 'fail' , 'msg' => 'checkout failed','result' => $result ,'data' => $_GET ] 

```
## current urway package payment ways :
- visa
- master
- mada
- stc








