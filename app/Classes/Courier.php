<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Abstract Courier class
 * To be extended by a courier class for useage.
 * Contains name, transport method and transport credentials.
 * 
 * The class enforces an implementation of a consignment number
 * 	algorithm.
 * Usage example:
 * 
 * class MyCourierCompany extends Courier {
 * 	public function getConsignmentNumber() : string
 *	{
 *		$randomNumber = "".strval(rand(1,9));
 *		for ($i = 0; $i < 9; $i++) {
 *			$randomNumber .= strval(rand(0,9));
 *		}
 *		return $randomNumber."-GB";
 * 	}
 * }
 * 
 * If two couriers share the same consignmentNumber algorithm it is wise
 * 	to re-use the extended class and give it a more generic name.
 * However you must make sure the courier references that are used in the
 * 	CourierCollection and DispatchBatch classes are still unique.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
abstract class Courier
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
	 * Constructor to be used to implement the extended courier class.
	 * Requires name, transport method and transport creds to be initiated.
	 * 
	 * @param string $name
	 * @param string $transportMethod
	 * @param array $transportCredentials
	 */
	public function __construct(string $name,
		string $transportMethod,
		array $transportCredentials
	) {
		$this->name = $name;
		$this->transportMethod = $transportMethod;
		$this->transportCredentials = $transportCredentials;
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
	 * Mandatory abstract method for implementing a consignment
	 * 	algorithm for a courier.
	 * 
	 * @return string
	 */
	abstract public function getConsignmentNumber() : string;
}