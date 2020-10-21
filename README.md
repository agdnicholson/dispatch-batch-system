# DispatchBatch System

A system that facilitates day consignment batching and sending consignment
    numbers to different couriers via transport methods (such as email and ftp)

A courier is required to supply an algorithm for how their own consignment
    numbers are determined.

This system is currently a skeleton project of classes and does not have database
    writing and reading implementation for example.

## Getting Started
The following classes have been implemented:\
app\Classes\Courier.php\
app\Classes\CourierCollection.php\
app\Classes\Consignment.php\
app\Classes\DispatchBatchManager.php\
app\Classes\DispatchBatchTransport.php\
app\Classes\DispatchBatchTransportLog.php


The classes can be used via autoload. Composer is recommended.

Once composer is installed or if you already have it simply running the following command should set things up:
```console
composer install
```

A temporary directory is required for any files generated for transport (For ftp in the example). Run the following
command from the project root to set this up:
```console
mkdir tmp
```

NOTE: The directory name should always correspond with the one provided in the DispatchBatchTransport constructor.

It should now be possible to run the example app (example-app.php implementation found in the root of the project.

This can be executed via command line or in a browser if for example you have apache or
    a virtual hosting environment such as Docker already running.

```console
php example-app.php
```

You should see the demo output in command line or browser window if example-app.php is ran from there.

## Class usage
Below follows a set of examples of how to use some of the class functionality provided with this system.
A more comprehensive example can be found in example-app.php in the root of the project.

The email function has been tested and only needs to be uncommented in\
app\Classes\DispatchBatchTransport.php for it to work.

Currently the ftp functionality is blocked via an if statement but will presumably work ok when
    valid credentials are provided. This part of the transport functionality is still to be tested.

We can create a required consignment number algorithm for a new Courier instance as so:
```php
$rmConsignmentNoAlgorithm = function()
{
	$randomNumber = "".strval(rand(1,9));
	for ($i = 0; $i < 9; $i++) {
		$randomNumber .= strval(rand(0,9));
	}
	return $randomNumber."-GB";
};
```

Create the demo courier instance
```php
$rmTransportCreds = ["to"=>$testEmailAddress, "from"=>$testFromAddress];
$royalMail = new Courier("Royal Mail", "email", $rmTransportCreds, $rmConsignmentNoAlgorithm);
```

Create the courier collection and add our courier to it.
```php
$courierCollection = new CourierCollection();
$courierCollection->addCourier($royalMail);
```

Create the dispatchbatch manager instance with the courier collection.
```php
$dispatchBatch = new DispatchBatchManager($courierCollection);
```

Start the batch
```php
$dispatchBatch->startBatch();
```

Create a consignment. We pass the courier name and a generated consignment number upon
instantiation.
```php
$consignment = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
```

Add the consignment to the batch
```php
$dispatchBatch->addConsignment($consignment);
```

End the batch
```php
$dispatchBatch->endBatch();
```

##	To run the automated tests
PHPUnit tests for the classes have been provided in the test\ directory.

The following command should run these automated unit tests:
```console
./vendor/bin/phpunit tests
```
## Version history
v2.0 - 21 October 2020: Improved Version
    -   Consignment class / objects for better future enhancements as we
        can imagine consignments will hold many more details.
    -   Pass consignment number algorithm as parameter to Courier classs,
        meaning no need for abstract extension.
    -   Separate Dispatch Batch Transport from Dispatch Batch Manager class and
        rename log class to reflect that it is only dealing with transport tracking.    

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
