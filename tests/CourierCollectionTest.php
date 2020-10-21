<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;
use App\Classes\CourierCollection;

/**
 * CourierCollection Class Test Cases
 * 
 * @author Andrew Nicholson (21 October 2020)
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
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $royalMail = new Courier("Royal Mail", 
                "email", 
                [], 
                $rmConsignmentNoAlgorithm
            );

        $ancConsignmentNoAlgorithm = function() : string
        {
            $randomNumber = date('Ymd')."";
            for ($i = 0; $i < 6; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber;
        };
        $ANC = new Courier("ANC", "email", [], $ancConsignmentNoAlgorithm);

        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail);
        $courierCollection->addCourier($ANC);

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
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $royalMail = new Courier("Royal Mail", 
                "email", 
                [], 
                $rmConsignmentNoAlgorithm
            );

        $ancConsignmentNoAlgorithm = function() : string
        {
            $randomNumber = date('Ymd')."";
            for ($i = 0; $i < 6; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber;
        };
        $ANC = new Courier("ANC", "email", [], $ancConsignmentNoAlgorithm);

        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail);
        $courierCollection->addCourier($royalMail);
        $courierCollection->addCourier($ANC);
        $courierCollection->addCourier($ANC);

        $this->assertCount(2, $courierCollection->getAllCouriers());
    }

    /**
     * Test that we can delete a courier from the collection
     * 
     * @return void
     */
    public function testDeleteCourier(): void 
    {
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $royalMail = new Courier("Royal Mail", 
                "email", 
                [], 
                $rmConsignmentNoAlgorithm
            );

        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail);
        $courierCollection->deleteCourier("Royal Mail");

        $this->assertEquals(NULL, $courierCollection->getCourier("Royal Mail"));
    }

    /**
     * Test that we can get a courier object from the collection
     *  by passing the courier reference
     * 
     * @return void
     */
    public function testGetCourier(): void 
    {
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $royalMail = new Courier("Royal Mail", 
                "email", 
                [], 
                $rmConsignmentNoAlgorithm
            );

        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail);

        $this->assertEquals($royalMail, $courierCollection->getCourier("Royal Mail"));
    }

    /**
     * Test that we can get ALL courier objects from a courier collection
     * 
     * @return void
     */
    public function testGetAllCouriers(): void 
    {
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $royalMail = new Courier("Royal Mail", 
                "email", 
                [], 
                $rmConsignmentNoAlgorithm
            );

        $ancConsignmentNoAlgorithm = function() : string
        {
            $randomNumber = date('Ymd')."";
            for ($i = 0; $i < 6; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber;
        };
        $ANC = new Courier("ANC", "email", [], $ancConsignmentNoAlgorithm);

        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail);
        $courierCollection->addCourier($ANC);

        $this->assertEquals(
            ["Royal Mail" => $royalMail, "ANC" => $ANC], 
            $courierCollection->getAllCouriers());
    }
}
