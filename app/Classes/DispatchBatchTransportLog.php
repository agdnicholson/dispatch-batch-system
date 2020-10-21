<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Log class for dispatch batches transport instances.
 * Contains static methods to write a success and/or error log.
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
class DispatchBatchTransportLog
{

	/**
	 * Static method to write success log.
	 * Requires courier reference, batch date, transport method and the consigment
	 * 	array for this courier. Returns true if log was written
	 * 
	 * @todo Finish implementation
	 * 
	 * @param string $courierRef
	 * @param string $batchDate
	 * @param string $batchTransportMethod
	 * @param array $consignmentArr
	 * 
	 * @return bool
	 */
	public static function logSuccess(
		string $courierRef, 
		string $batchDate, 
		string $batchTransportMethod, 
		array $consignmentArr
	) : bool {
		/**
		* Log dispatch success record for this courier with batch contents 
		*	(database or file for example)
		*/
		$logWritten = TRUE;

		return $logWritten;
	}

	/**
	 * Static method to write error log.
	 * Requires courier reference, batch date, transport method and the consignment
	 * 	array for this courier. Returns true if log was written.
	 * 
	 * @todo Finish implementation
	 * 
	 * @param string $courierRef
	 * @param string $batchDate
	 * @param string $batchTransportMethod
	 * @param array $consignmentArr
	 * 
	 * @return bool
	 */
	public static function logError(
		string $courierRef, 
		string $batchDate, 
		string $batchTransportMethod, 
		array $consignmentArr
	) : bool {
		/**
		* Log dispatch error record for this courier with batch contents 
		*	(database or file for example)
		*/
		$logWritten = TRUE;

		return $logWritten;
	}
}
