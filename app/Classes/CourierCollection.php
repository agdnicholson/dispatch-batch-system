<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Courier Collection class.
 * An instance of this can hold Courier objects as array
 * 	by using a reference / the name as a key. This will
 * 	make lookup much easier / quicker.
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
class CourierCollection
{
	/**
	 * @var array
	 */
	protected $couriers;

	/**
	 * Courier Collection constructor
	 */
	public function __construct()
	{
		$this->couriers = [];
	}

	/**
	 * Adds a courier object to the collection.
	 * We use the name of the courier as a key for quick look up.
	 * 
	 * @param Courier $courier
	 * 
	 * @return void
	 */
	public function addCourier(Courier $courier) : void 
	{
		if (!array_key_exists($courier->getName(), $this->couriers)) {
			$this->couriers[$courier->getName()] = $courier;
		}
	}

	/**
	 * If valid courier reference is passed, it unsets the 
	 * 	associated courier object in the collection
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
	 * 	courier object from the collection.
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
	 * Returns the array of courier objects
	 * 
	 * @return array
	 */
	public function getAllCouriers() : array 
	{
		return $this->couriers;
	}
}