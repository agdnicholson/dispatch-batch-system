<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Courier Collection class.
 * An istance of this can hold extended Courier objects as array
 * 	by using a reference for each as a key.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
class CourierCollection
{
	/**
	 * @var array
	 */
	protected $couriers;

	/**
	 */
	public function __construct()
	{
		$this->couriers = [];
	}

	/**
	 * Adds an extended courier object to the collection
	 * 
	 * @param Courier $courier
	 * @param string $courierRef
	 * 
	 * @return void
	 */
	public function addCourier(Courier $courier, string $courierRef) : void 
	{
		if (!array_key_exists($courierRef, $this->couriers)) {
			$this->couriers[$courierRef] = $courier;
		}
	}

	/**
	 * If valid courier reference is passed, it unsets the 
	 * 	associated extended courier object in the collection
	 * 
	 * @param string $courierRef
	 * 
	 * @return void
	 */
	public function deleteCourier(string $courierRef) : void 
	{
		if (array_key_exists($courierRef, $this->couriers)) {
			unset($this->couriers[$courierRef]);
		}
	}

	/**
	 * If valid courier reference is passed it returns the associated
	 * 	extended courier object from the collection
	 * 
	 * @param string $courierRef
	 * 
	 * @return Courier|null
	 */
	public function getCourier(string $courierRef) : ?Courier 
	{
		return array_key_exists($courierRef, $this->couriers) ?
			$this->couriers[$courierRef] : NULL;
	}

	/**
	 * Returns the array of extended courier objects
	 * 
	 * @return array
	 */
	public function getAllCouriers() : array 
	{
		return $this->couriers;
	}
}