<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;
use App\Classes\CourierCollection;

/**
 * CourierCollection Class Test Cases
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
final class CourierCollectionTest extends TestCase
{
    /**
     * Test adding a courier to the collection
     * 
     * @return void
     */
    public function testAddCourier(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");

        $this->assertCount(2, $courierCollection->getAllCouriers());
    }

    /**
     * Test adding a duplicate courier should have no effect to 
     *  the collection
     * 
     * @return void
     */
    public function testAddingDuplicateCourierHasNoEffect(): void 
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");
        $courierCollection->addCourier($ANC, "ANC");

        $this->assertCount(2, $courierCollection->getAllCouriers());
    }

    /**
     * Test that we can delete a courier from the collection
     * 
     * @return void
     */
    public function testDeleteCourier(): void 
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->deleteCourier("RM");

        $this->assertEquals(NULL, $courierCollection->getCourier("RM"));
    }

    /**
     * Test that we can get a courier object from the collection
     *  by passing the courier reference
     * 
     * @return void
     */
    public function testGetCourier(): void 
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");

        $this->assertEquals($royalMail, $courierCollection->getCourier("RM"));
    }

    /**
     * Test that we can get ALL courier objects from a courier collection
     * 
     * @return void
     */
    public function testGetAllCouriers(): void 
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");

        $this->assertEquals(
            ["RM" => $royalMail, "ANC" => $ANC], 
            $courierCollection->getAllCouriers());
    }
}
