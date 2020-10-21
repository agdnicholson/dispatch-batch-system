<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Consignment Class
 * 
 * Class to contain all details relating to a consignment.
 * Requires more implementation such as order and customer
 *  details. We are only simulating the consignment functionality
 *  using the consignment number and used courier reference at 
 *  the moment.
 * 
 * @todo implement customer details, order details etc.
 * @author Andrew Nicholson (21 October 2020)
 */
class Consignment 
{
    /**
     * @var string
     */
    protected $courierRef;
    
    /**
     * @var string
     */
    protected $consignmentNumber;

    /**
     * Consignment constructor
     * 
     * @param string $courierRef 
     * @param string $consignmentNumber
     */
    public function __construct(string $courierRef,
        string $consignmentNumber
    ) {
        $this->courierRef = $courierRef;
        $this->consignmentNumber = $consignmentNumber;
    }

    /**
     * Returns the courier reference for this consignment.
     * 
     * @return string
     */
    public function getCourierRef() : string 
    {
        return $this->courierRef;
    }

    /**
     * Returns the unique consignment number for this consignment.
     * 
     * @return string
     */
    public function getConsignmentNumber() : string 
    {
        return $this->consignmentNumber;
    }
}