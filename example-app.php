<?php 
declare(strict_types=1);

use App\Classes\Courier;
use App\Classes\CourierCollection;
use App\Classes\DispatchBatchManager;
use App\Classes\Consignment;

$loader = require __DIR__ . '/vendor/autoload.php';

/**
 * Test email address; enter a valid address and uncomment
 * 	the mail function in the DispatchBatch class.
 */
$testEmailAddress = "someemailaddress@somedomain.com";
$testFromAddress = "no-reply@dispatchbatchsystem.com";

//test ftp credentials
$ftpServer = "ftp.someserver.com";
$ftpUsername = "anonymous";
$ftpPassword = "someemailaddress@somedomain.com";

$rmConsignmentNoAlgorithm = function()
{
	$randomNumber = "".strval(rand(1,9));
	for ($i = 0; $i < 9; $i++) {
		$randomNumber .= strval(rand(0,9));
	}
	return $randomNumber."-GB";
};

$ancConsignmentNoAlgorithm = function() : string
{
	$randomNumber = date('Ymd')."";
	for ($i = 0; $i < 6; $i++) {
		$randomNumber .= strval(rand(0,9));
	}
	return $randomNumber;
};

//Create the demo courier instances
$rmTransportCreds = ["to"=>$testEmailAddress, "from"=>$testFromAddress];
$royalMail = new Courier("Royal Mail", "email", $rmTransportCreds, $rmConsignmentNoAlgorithm);

$ancTransportCreds = ["server"=>$ftpServer, 
	"username"=>$ftpUsername, 
	"password", $ftpPassword];
$ANC = new Courier("ANC", "ftp", $ancTransportCreds, $ancConsignmentNoAlgorithm);

//Create the courier collection and add our couriers to it.
$courierCollection = new CourierCollection();
$courierCollection->addCourier($royalMail);
$courierCollection->addCourier($ANC);

//Create the dispatchbatch manager instance with the courier collection.
$dispatchBatchManager = new DispatchBatchManager($courierCollection);
$dispatchBatchManager->startBatch();

//create consignments and add these to the batch.
$consignment1 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
$consignment2 = new Consignment($ANC->getName(), $ANC->getConsignmentNumber());
$consignment3 = new Consignment($ANC->getName(), $ANC->getConsignmentNumber());
$consignment4 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
$consignment5 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
$consignment6 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
$dispatchBatchManager->addConsignment($consignment1);
$dispatchBatchManager->addConsignment($consignment2);
$dispatchBatchManager->addConsignment($consignment3);
$dispatchBatchManager->addConsignment($consignment4);
$dispatchBatchManager->addConsignment($consignment5);
$dispatchBatchManager->addConsignment($consignment6);

$consignmentNos = [
	$consignment1->getConsignmentNumber(), 
	$consignment2->getConsignmentNumber(),
	$consignment3->getConsignmentNumber(),
	$consignment4->getConsignmentNumber(),
	$consignment5->getConsignmentNumber(),
	$consignment6->getConsignmentNumber()
];

// we can print out the consignment numbers created
var_dump($consignmentNos);

/** 
 * We can make sure the dispatch batch instance is keeping track
 * 	of the consignment objects correctly.
 */
var_dump($dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
var_dump($dispatchBatchManager->getConsignmentsSoFar($ANC->getName()));

//end the batch, this will transport the consignments via email or ftp 
$dispatchBatchManager->endBatch();

//we should not be able to add a new consignment now has a new batch hasn't started
$dispatchBatchManager->addConsignment($consignment1);
var_dump($dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));

//starting a new batch clears the consignment stack
$dispatchBatchManager->startBatch();

//now our consignment arrays for each courier should be empty.
var_dump($dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
var_dump($dispatchBatchManager->getConsignmentsSoFar($ANC->getName()));