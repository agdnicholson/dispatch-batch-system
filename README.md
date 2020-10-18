# DispatchBatch System

A system that facilitates day consignment batching and sending consignment
    numbers to different couriers via transport methods (such as email and ftp)

A courier is required to supply an algorithm for how their own consignment
    numbers are determined.

This system is currently a skeleton project of classes and does not have database
    writing and reading implementation for example.

## Getting Started
The following classes have been implemented:\
app\Classes\Courier.php (Abstract class to be extended so that a consignment algorithm can be provided)\
app\Classes\CourierCollection.php\
app\Classes\DispatchBatchLog.php\
app\Classes\DispatchBatch.php

The classes can be used via autoload. Composer is recommended.

Once composer is installed or if you already have it simply running the following command should set things up:

composer install

It should now be possible to run the example app (example-app.php implementation found in the root of the project.

This can be executed via command line or in a browser if for example you have apache or
    a virtual hosting environment such as Docker already running.

php example-app.php\
You should see the demo output in command line or browser window if example-app.php is ran from there.

## Class usage
Below follows a set of examples of how to use some of the class functionality provided with this system.
A more comprehensive example can be found in example-app.php in the root of the project.

The email function has been tested and only needs to be uncommented in\
app\Classes\DispatchBatch.php to work

Currently the ftp functionality is blocked via an if statement but will presumably work ok when
    valid credentials are provided. This part of the endbatch functionality is still to be tested.

Extending the courier class is required so that we have a consignment algorithm for this courier:
```php
/**
 *  RoyalMail courier class, extends the abstract courier class.
 *  Implements the required consignment algorithm method.
 * 
 *  @author Andrew Nicholson (18 October 2020)
 */
class RoyalMail extends Courier {

    /**
     * The required get consignmentnumber method.
     * A test implementation of the number algorithm.
     * 
     * @return string
     */
	public function getConsignmentNumber() : string
	{
		$randomNumber = "".strval(rand(1,9));
		for ($i = 0; $i < 9; $i++) {
			$randomNumber .= strval(rand(0,9));
		}
		return $randomNumber."-GB";
	}
}
```

Create the demo courier instance
```php
$rmTransportCreds = ["to"=>"hello@some-domain.com, "from"=>"no-reply@some-domain.com"];
$royalMail = new RoyalMail("Royal Mail", "email", $rmTransportCreds);
```

Create the courier collection and add our courier to it using a courier reference key.
```php
$courierCollection = new CourierCollection();
$courierCollection->addCourier($royalMail, "RM");
```

Create the dispatchbatch instance with the courier collection and set the local
    temporary storage folder for any temporary files that are generated for transport. 
```php
$dispatchBatch = new DispatchBatch($courierCollection, 'tmp/');
```

Start the batch
```php
$dispatchBatch->startBatch();
```

Add a consignment
```php
$dispatchBatch->addConsignment("RM");
```

End the batch
```php
$dispatchBatch->endBatch();
```

##	To run the automated tests
The following command should run the automated unit tests:\
./vendor/bin/phpunit tests

## Version history
v1.0 - 18 October 2020: Initial Version. 

### Prerequisites
- PHP 7 is required.
- Composer and PHPUnit are required to run tests.

Tested with PHP 7.3.11

## Built With

* [Composer](https://getcomposer.org/) - PHP Dependency manager
* [PHPUnit](https://phpunit.de/) - The testing framework used

## Authors

* **[Andrew Nicholson](https://github.com/agdnicholson)**
