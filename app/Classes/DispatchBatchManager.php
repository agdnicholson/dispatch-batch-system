<?php
declare(strict_types=1);

namespace App\Classes;

use App\Classes\DispatchBatchTransport;
use App\Classes\Consignment;

/**
 * Dispatch Batch Manager class
 * Allows for a dispatch batch manager object to be created by passing through
 * 	a courier collection.
 * Methods included: starting a new batch (sets the batch date and clears the
 * 	consignment stack), add a consignment object to the batch, return all
 * 	so far and end the current batch (creates a transport instance).
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
class DispatchBatchManager 
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
	 * @var bool
	 */
	protected $batchStarted;

	/**
	 * Dispatch Batch Manager constructor.
	 * Requires the used courier collection to be supplied as parameter.
	 * 
	 * @param CourierCollection $couriersCol
	 */
	public function __construct(CourierCollection $couriersCol) 
	{
		// Default Dispatch Batch Values
        $this->couriers = $couriersCol->getAllCouriers();
        $this->batchStarted = FALSE;
        $this->consignmentStack = [];
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

            // Iterate through the couriers
            foreach ($this->couriers as $courierRef => $courier) {

                // If there are any consigments on the stack for this courier
                if (array_key_exists($courierRef, $this->consignmentStack)) {
                    $dbTransport = new DispatchBatchTransport(
                            $this->batchDate,
                            $courier, 
                            $this->consignmentStack[$courierRef]
                        );
                    $dbTransport->send();
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
	 * @param Consignment $consignment
	 * 
	 * @return void
	 */
	public function addConsignment(Consignment $consignment) : void
	{
		if ($this->batchStarted) {
            $courierRef = $consignment->getCourierRef();

			if (array_key_exists($courierRef, $this->consignmentStack)) {
				$this->consignmentStack[$courierRef][] = $consignment;
			} else {
				$this->consignmentStack[$courierRef] = [$consignment];
            }
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
}