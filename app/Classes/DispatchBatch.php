<?php
declare(strict_types=1);

namespace App\Classes;

use App\Classes\DispatchBatchLog;

/**
 * Dispatch Batch class
 * Allows for a dispatch batch object to be created by passing through
 * 	a courier collection.
 * Methods included: starting a new batch (sets the batch date and clears the
 * 	consignment stack), add a consignment to the batch (using courier reference),
 * 	return all consignments and end the current batch (which implements the batch
 * 	transport methods for each courier.)
 * 
 * @todo Improve / replace the used consignment number implmentation. Should probably
 * 	be looking up database table or so.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
class DispatchBatch 
{
	/**
	 * @var array
	 */
	protected $consignmentStack;

	/**
	 * @var CourierCollection
	 */
	protected $couriers;

	/**
	 * @var string
	 */
	protected $batchDate;

	/**
	 * @var array
	 */
	protected $usedConsignmentNumbers;

	/**
	 * @var bool
	 */
	protected $batchStarted;

	/**
	 * @var string
	 */
	protected $tmpStorageFolder;


	/**
	 * Dispatch Batch construction.
	 * Requires the used courier collection to be supplied as parameter,
	 * 	along with temporary storage folder for ftp files.
	 * 
	 * @param CourierCollection $couriersCol
	 * @param mixed $tmpFolder
	 */
	public function __construct (CourierCollection $couriersCol, $tmpFolder) 
	{
		// Default Dispatch Batch Values
		$this->couriers = $couriersCol->getAllCouriers();
		$this->usedConsignmentNumbers = [];
		$this->batchStarted = FALSE;
		$this->tmpStorageFolder = $tmpFolder;
	}


	/**
	 * Method to start a daily batch of consignments.
	 * It clears the consignment stack and sets the date.
	 * 
	 * @return void
	 */
	public function startBatch() : void
	{
		// Start a new batch if one isn't active, clear stack & set date
		if (!$this->batchStarted) {
			$this->consignmentStack = [];
			$this->batchDate = date('Y-m-d');
			$this->batchStarted = TRUE;
		}
	}


	/**
	 * Method that ends a day's batch and transports the consignments 
	 * 	to each courier, based on their associated transport method.
	 * 
	 * @return void
	 */
	public function endBatch() : void
	{
		// IF batch has started we can end it.
		if ($this->batchStarted) {

			// Loop through the cou
			foreach ($this->couriers as $courierRef => $courier) {

				// If there are any consigments on the stack for this courier
				if (array_key_exists($courierRef, $this->consignmentStack)) {

					// Switch on courier's tranport method & call function
					switch ($courier->getTransportMethod()) {
						case 'email' :
							$this->transportConsignmentNosEmail($courierRef);
							break;
						case 'ftp' : 
							$this->transportConsignmentNosFTP($courierRef);
							break;
					}
				}
			}

			// Mark batch as started as false
			$this->batchStarted = FALSE;
		}
	}

	/**
	 * Adds a consignment to the consignment stack for a specific courier.
	 * It retrieves an available consignment number (based on the courier's
	 * 	consignment number algorithm) and returns it as a string.
	 * The function throws errors (as string) if courier ref is invalid or 
	 * 	if batch has not started since it is a public function.
	 * It is expected that the caller can deal with these errors.
	 * 
	 * @param string $courierRef
	 * 
	 * @return string
	 */
	public function addConsignment(string $courierRef) : string
	{
		if ($this->batchStarted) {

			//if courier doesn't exist return error
			if (!array_key_exists($courierRef, $this->couriers)) {
				return "Error - courier does not exist."; 
			}

			$consignmentNumber = 
				$this->couriers[$courierRef]->getConsignmentNumber();
			while (array_key_exists($consignmentNumber, 
				$this->usedConsignmentNumbers)) {
				$consignmentNumber = 
					$this->couriers[$courierRef]->getConsignmentNumber();
			}

			if (array_key_exists($courierRef, $this->consignmentStack)) {
				$this->consignmentStack[$courierRef][] = $consignmentNumber;
			} else {
				$this->consignmentStack[$courierRef] = [$consignmentNumber];
			}
			
			$this->usedConsignmentNumbers[] = $consignmentNumber;

			return $consignmentNumber;
		} else {
			return "Error - batch has not started.";
		}
	}

	/**
	 * Gets the array of consigments so far by passing a courier reference.
	 * The public function can be called at any time.
	 * Useful for mid day reporting and testing.
	 * 
	 * @param string $courierRef
	 * 
	 * @return array
	 */
	public function getConsignmentsSoFar(string $courierRef) : array
	{
		return array_key_exists($courierRef, $this->consignmentStack) ?
			$this->consignmentStack[$courierRef] : [];
	}

	/**
	 * Email transport method. 
	 * Emails the consigments for a courier by using its credentials.
	 * Calls the appropriate static log methods upon success or failure.
	 * This method can only be called internally so it shouldn't be
	 * 	necessary to check if a batch has started.
	 * 
	 * @todo uncomment mail function call to make emailing work
	 * 
	 * @param string $courierRef
	 * 
	 * @return void
	 */
	protected function transportConsignmentNosEmail(string $courierRef) : void
	{
		// get transport creds
		$courier = $this->couriers[$courierRef];
		$transportCreds = $courier->getTransportCredentials();

		// start to construct the email
		$to = $transportCreds["to"];
		$subject = "Batch " . $this->batchDate . " " . $courier->getName();
		$consignmentBody = "";
		foreach ($this->consignmentStack[$courierRef] as $consignment) {
			$consignmentBody .= $consignment . "\r\n";
		}
		$headers = "From: " . $transportCreds["from"];

		// try to send mail. If success then write success log, if fails then write error log.
		try {
			
			//mail($to, $subject, $consignmentBody, $headers);

			//success log
			DispatchBatchLog::logSuccess($courierRef, $this->batchDate,
				'email', $this->consignmentStack[$courierRef]);
		} catch (Exception $e){
			/*
			* When there are any mail errors, log consignments to a log 
			* for example so it can be dealt with later / manually
			*/
			DispatchBatchLog::logError($courierRef, 
				'email', $this->consignmentStack[$courierRef]);
		}
	}

	/**
	 * FTP transport method.
	 * Creates a local batch file of consignments for a courier and FTPs it to a
	 * 	specific destination using courier transport credentials.
	 * Calls the appropriate static log methods upon success or failure.
	 * This method can only be called internally so it shouldn't be
	 * 	necessary to check if a batch has started.
	 * 
	 * @todo remove blocking part in if statement to make ftp work.
	 * 
	 * @param string $courierRef
	 * 
	 * @return void
	 */
	protected function transportConsignmentNosFTP(string $courierRef) : void
	{
		// get transport creds
		$courier = $this->couriers[$courierRef];
		$transportCreds = $courier->getTransportCredentials();

		$ftpSuccess = TRUE;
		//create local consignment batch file
		$consignmentFileContents = "";
		foreach ($this->consignmentStack[$courierRef] as $consignment) {
			$consignmentFileContents .= $consignment . "\r\n";
		}
		$localFile = $this->tmpStorageFolder."batch-".$this->batchDate.".txt";

		try {
			$fp = fopen($localFile, "w") or die("Unable to open file!");
			fwrite($fp, $consignmentFileContents);
			fclose($fp);
		} catch (Exception $e) {
			$ftpSuccess = FALSE;
		}

		// @todo remove false statement below to make ftp work.
		if ($ftpSuccess && 1===2) {
			try {
				$conn = ftp_connect($transportCreds["server"]);

				//if we can establish connection
				if ($conn) {
					$login = ftp_login($conn, 
						$transportCreds["username"], 
						$transportCreds["password"]); 

					//if login is successful
					if ($login) {
						$filePut = ftp_put(
							$conn, 
							$remote, 
							$localFile, 
							FTP_ASCII);
						
						// if ftp-ing the file was successful
						if ($filePut) ftp_close($conn); else $ftpSuccess = FALSE;
					} else {
						$ftpSuccess = FALSE;
					}
				} else {
					$ftpSuccess = FALSE;
				}
			} catch (Exception $e) {
				$ftpSuccess = FALSE;
			}
		}
		//remove local consignment batch file
		@unlink($localFile);
		if ($ftpSuccess) {
			//success log
			DispatchBatchLog::logSuccess($courierRef, $this->batchDate,
				'ftp', $this->consignmentStack[$courierRef]);
		} else {
			/*
			* When there are any ftp errors, log consignments to a log 
			* for example so it can be dealt with later / manually
			*/
			DispatchBatchLog::logError($courierRef, 
				'ftp', $this->consignmentStack[$courierRef]);
		}
	}
}