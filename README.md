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

- with [ visa - master - mada ]
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
- define the checkout return response url route with urway team EX: https://mysite.com/paymentresponse
- create route for response url 'paymentresponse' 
EX: Route::get('paymentresponse', 'PaymentsController@paymentresponse')->name('paymentresponse'); 
- create function for checkout response 'paymentresponse'
- use that function to check if payment failed or success

##inside 'paymentresponse' function use:
```php
use maree\urway\Urway;
$response = Urway::checkoutResponseStatus();  

```
return response like: 
```php

['key' => 'success' ,'msg' => 'checkout success' ,'result' => $result ,'data' => $_GET ]  

```
or 

```php

['key' => 'fail' , 'msg' => 'checkout failed','result' => $result ,'data' => $_GET ] 

```

- Test Card Details
- Card Number: Master:5123450000000008 - Visa: 4508 7500 1574 1019
- CVV: 100
- Expiry Date: 05/23
- Card Name: Test Family
- Custom ECI: Leave Blank
- Custom CAVV: Leave Blank

## for code errors open urway documentaion file.
## current urway package payment ways :
- visa
- master
- mada
- stc








