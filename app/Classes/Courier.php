<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Courier class
 * Contains name, transport method, transport credentials and
 * 	consignment number algorithm (to be passed as a callable function).
 * A courier instance will probably hold more information in a real world
 * 	scenario.
 * 
 * @todo implement consignment number lookup to ensure only unique one is returned
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
class Courier
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $transportMethod;

	/**
	 * @var array
	 */
	protected $transportCredentials;

	/**
	 * @var callable
	 */
	protected $consignmentAlgorithm;

	/**
	 * Courier constructor
	 * Requires name, transport method, transport creds
	 * 	and a callable consignment algorithm number 
	 * 	function to be initiated.
	 * 
	 * @param string $name
	 * @param string $transportMethod
	 * @param array $transportCredentials
	 * @param callable $consignmentAlgorithm
	 */
	public function __construct(string $name,
		string $transportMethod,
		array $transportCredentials,
		callable $consignmentAlgorithm
	) {
		$this->name = $name;
		$this->transportMethod = $transportMethod;
		$this->transportCredentials = $transportCredentials;
		$this->consignmentAlgorithm = $consignmentAlgorithm;
	}

	/**
	 * Getter for the courier name
	 * 
	 * @return string
	 */
	public function getName() : string 
	{
		return $this->name;
	}

	/**
	 * Getter for the transport method
	 * 
	 * @return string
	 */
	public function getTransportMethod() : string 
	{
		return $this->transportMethod;
	}

	/**
	 * Getter for the transport creds.
	 * 
	 * @return array
	 */
	public function getTransportCredentials() : array
	{
		return $this->transportCredentials;
	}

	/**
	 * The consignment number getter which will call the callable 
	 * 	function that will have been passed. Here we can also implement
	 * 	a lookup routine to ensure the number returned is unique and 
	 * 	hasn't been used before.
	 * 
	 * @todo implement consignment number lookup to ensure we return unique new one.
	 * 
	 * @return string
	 */
	public function getConsignmentNumber() : string
	{
		$newConsignmentNumber= call_user_func($this->consignmentAlgorithm);
		while (1===2) {
			/**
			 * Here we would want to call a database model / instance to ensure 
			 * 	our generated consignment number is unique. Loop until we have a new 
			 *	unique one.
			*/
			$newConsignmentNumber= call_user_func($this->consignmentAlgorithm);
		}
		return $newConsignmentNumber;
	}
}