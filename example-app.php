<?php 
use App\Classes\Courier;
use App\Classes\CourierCollection;
use App\Classes\DispatchBatch;

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

/**
 *  RoyalMail courier class, extends the abstract courier class.
 *  Implements the required consignment algorithm method.
 * 
 * @author Andrew Nicholson (18 October 2020)
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

/**
 * ANC courier class, extends the abstract courier class
 * Implements the required consignment algorithm method.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
class ANC extends Courier {

	/**
     * The required get consignmentnumber method.
     * A test implementation of the number algorithm.
     * 
	 * @return string
	 */
	public function getConsignmentNumber() : string
	{
		$randomNumber = date('Ymd')."";
		for ($i = 0; $i < 6; $i++) {
			$randomNumber .= strval(rand(0,9));
		}
		return $randomNumber;
	}
}

//Create the demo courier instances
$rmTransportCreds = ["to"=>$testEmailAddress, "from"=>$testFromAddress];
$royalMail = new RoyalMail("Royal Mail", "email", $rmTransportCreds);
$ancTransportCreds = ["server"=>$ftpServer, 
	"username"=>$ftpUsername, 
	"password", $ftpPassword];
$ANC = new ANC("ANC", "ftp", $ancTransportCreds);

//Create the courier collection and add our couriers to it using courier reference keys.
$courierCollection = new CourierCollection();
$courierCollection->addCourier($royalMail, "RM");
$courierCollection->addCourier($ANC, "ANC");

/**
 * Create the dispatchbatch instance with the courier collection and set the local
 *	temporary storage folder for any temporary files that are generated for transport. 
 */
$dispatchBatch = new DispatchBatch($courierCollection, 'tmp/');
$dispatchBatch->startBatch();
$consignmentNos = [];
$consignmentNos[] = $dispatchBatch->addConsignment("RM");
$consignmentNos[] = $dispatchBatch->addConsignment("ANC");
$consignmentNos[] = $dispatchBatch->addConsignment("ANC");
$consignmentNos[] = $dispatchBatch->addConsignment("RM");
$consignmentNos[] = $dispatchBatch->addConsignment("RM");
$consignmentNos[] = $dispatchBatch->addConsignment("RM");

// we can print out the consignment numbers created
var_dump($consignmentNos);

//adding a consignment of a courier that doesn't exist flags error
var_dump($dispatchBatch->addConsignment("DPD"));

/** 
 * We can make sure the dispatch batch instance is keeping track
 * 	of the consignments correctly.
 */
var_dump($dispatchBatch->getConsignmentsSoFar("RM"));
var_dump($dispatchBatch->getConsignmentsSoFar("ANC"));

//end the batch, this will transport the consignments via email or ftp 
$dispatchBatch->endBatch();

//we should not be able to add a new consignment now has a new batch hasn't started
$consignmentNo = $dispatchBatch->addConsignment("RM");
var_dump($consignmentNo);

//starting a new batch clears the consignment stack
$dispatchBatch->startBatch();

//now our consignment arrays for each courier should be empty.
var_dump($dispatchBatch->getConsignmentsSoFar("RM"));
var_dump($dispatchBatch->getConsignmentsSoFar("ANC"));