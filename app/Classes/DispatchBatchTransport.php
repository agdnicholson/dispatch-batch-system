<?php
declare(strict_types=1);

namespace App\Classes;

use App\Classes\DispatchBatchTransportLog;
use App\Classes\Consignment;
use App\Classes\Courier;

/**
 * Dispatch Batch Transport class
 * Implements the actual transport methods of getting
 *  consignment data to the couriers
 * 
 * @todo implement further data to be sent to couriers
 *          with each consignment (name & address etc)
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
class DispatchBatchTransport
{
    /**
     * @var string
     */
    protected $batchDate;

    /**
     * @var Courier
     */
    protected $courier;

    /**
     * @var array
     */
    protected $consignmentStack;

    /**
     * @var string
     */
    protected $tmpStorageFolder;

    /**
     * Dispatch Batch Transport constructor
     * 
     * @param string $batchDate
     * @param Courier $courier 
     * @param array $consignmentStack
     */
    public function __construct(
        string $batchDate, 
        Courier $courier, 
        array $consignmentStack)
    {
        $this->batchDate = $batchDate;
        $this->courier = $courier;
        $this->consignmentStack = $consignmentStack;
        $this->tmpStorageFolder = "\tmp";
    }

    /**
     * Send method which triggers the appropriate 
     *  dispatch method for the courier.
     * 
     * @return void
     */
    public function send() : void 
    {
        // Switch on courier's tranport method & call function
        switch ($this->courier->getTransportMethod()) {
            case 'email' :
                $this->transportConsignmentNosEmail();
                break;
            case 'ftp' : 
                $this->transportConsignmentNosFTP();
                break;
        }
    }

    /**
	 * Email transport method. 
	 * Emails the consigments for a courier by using its credentials.
	 * Calls the appropriate static log methods upon success or failure.
	 * This method can only be called internally.
	 * 
	 * @todo uncomment mail function call to make emailing work
	 * 
	 * @return void
	 */
	protected function transportConsignmentNosEmail() : void
	{
		// get transport creds
		$transportCreds = $this->courier->getTransportCredentials();

		// start to construct the email
		$to = $transportCreds["to"];
		$subject = "Batch " . $this->batchDate . " " . $this->courier->getName();
		$consignmentBody = "";
		foreach ($this->consignmentStack as $consignment) {
			$consignmentBody .= $consignment->getConsignmentNumber() . "\r\n";
		}
		$headers = "From: " . $transportCreds["from"];

		// try to send mail. If success then write success log, if fails then write error log.
		try {
			
			//mail($to, $subject, $consignmentBody, $headers);

			//success log
			DispatchBatchTransportLog::logSuccess($this->courier->getName(), $this->batchDate,
				'email', $this->consignmentStack);
		} catch (Exception $e){
			/*
			* When there are any mail errors, log consignments to a log 
			* for example so it can be dealt with later / manually
			*/
			DispatchBatchTransportLog::logError($this->courier->getName(), 
				'email', $this->consignmentStack);
		}
	}

	/**
	 * FTP transport method.
	 * Creates a local batch file of consignments for a courier and FTPs it to a
	 * 	specific destination using courier transport credentials.
	 * Calls the appropriate static log methods upon success or failure.
	 * This method can only be called internally.
	 * 
	 * @todo remove blocking part in if statement to make ftp work.
	 * 
	 * @return void
	 */
	protected function transportConsignmentNosFTP() : void
	{
		// get transport creds
		$transportCreds = $this->courier->getTransportCredentials();

		$ftpSuccess = TRUE;
		//create local consignment batch file
		$consignmentFileContents = "";
		foreach ($this->consignmentStack as $consignment) {
			$consignmentFileContents .= $consignment->getConsignmentNumber() . "\r\n";
		}
		$localFile = $this->tmpStorageFolder."batch-".$this->batchDate.".txt";

		try {
			$fp = fopen($localFile, "w");
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
			DispatchBatchTransportLog::logSuccess($this->courier->getName(), $this->batchDate,
				'ftp', $this->consignmentStack);
		} else {
			/*
			* When there are any ftp errors, log consignments to a log 
			* for example so it can be dealt with later / manually
			*/
			DispatchBatchTransportLog::logError($this->courier->getName(), 
				'ftp', $this->consignmentStack);
		}
	}
}