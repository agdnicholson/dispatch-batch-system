<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;
use App\Classes\CourierCollection;
use App\Classes\Consignment;
use App\Classes\DispatchBatchManager;

/**
 * DispatchBatchManager Class Test Cases
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
final class DispatchBatchManagerTest extends TestCase
{
    /**
     * Test we can create a dispatch batch manager instance
     * 
     * @return void
     */
    public function testDispatchBatchManagerInit(): void
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
        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $this->assertInstanceOf(DispatchBatchManager::class, $dispatchBatchManager);
    }

    /**
     * Test we can start a dispatch batch and have an empty
     *  consignment stack
     * 
     * @return void
     */
    public function testDispatchBatchStart(): void
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
        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $dispatchBatchManager->startBatch();
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($ANC->getName()));
    }

    /**
     * Test we can start a dispatch batch and try to start a batch
     *  again before one is ended has no influence.
     * 
     * @return void
     */
    public function testDispatchBatchDoubleStart(): void
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

        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $dispatchBatchManager->startBatch();
        $dispatchBatchManager->startBatch();
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($ANC->getName()));
    }

    /**
     * Test we can add consignments to a batch that has started and these are
     *  kept track off within the dispatchbatch instance.
     * 
     * @return void
     */
    public function testAddConsignments(): void
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

        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $dispatchBatchManager->startBatch();
        $consignment1 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
        $consignment2 = new Consignment($ANC->getName(), $ANC->getConsignmentNumber());
        $consignment3 = new Consignment($ANC->getName(), $ANC->getConsignmentNumber());
        $consignment4 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
        $consignment5 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
        $consignment6 = new Consignment($royalMail->getName(), $royalMail->getConsignmentNumber());
        $dispatchBatchManager->addConsignment($consignment1);
        $dispatchBatchManager->addConsignment($consignment2);
        $dispatchBatchManager->addConsignment($consignment3);
        $dispatchBatchManager->addConsignment($consignment4);
        $dispatchBatchManager->addConsignment($consignment5);
        $dispatchBatchManager->addConsignment($consignment6);
        $rmConsignments = [$consignment1, $consignment4, $consignment5, $consignment6];
        $ancConsignments = [$consignment2, $consignment3];
        $this->assertEquals($rmConsignments, 
            $dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
        $this->assertEquals($ancConsignments,
            $dispatchBatchManager->getConsignmentsSoFar($ANC->getName()));
    }
    
    /**
     * Test if we try to add consignments when a batch has not started
     *  nothing happens
     * 
     * @return void
     */
    public function testAddConsignmentsWithoutBatchStart(): void
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

        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $consignment = new Consignment($royalMail->getName(), 
            $royalMail->getConsignmentNumber());
        $dispatchBatchManager->addConsignment($consignment);
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
    }

     /**
     * Test we can start batch, add consignments and end the batch.
     * When we start a new batch the consignment stack should be emmpty.
     * 
     * @return void
     */
    public function testStartAndEndBatch(): void
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

        $dispatchBatchManager = new DispatchBatchManager($courierCollection);
        $dispatchBatchManager->startBatch();
        $consignment = new Consignment($royalMail->getName(), 
            $royalMail->getConsignmentNumber());
        $dispatchBatchManager->addConsignment($consignment);
        $dispatchBatchManager->endBatch();
        $dispatchBatchManager->startBatch();
        $this->assertEquals([], 
            $dispatchBatchManager->getConsignmentsSoFar($royalMail->getName()));
    }
}