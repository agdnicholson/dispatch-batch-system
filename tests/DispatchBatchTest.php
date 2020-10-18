<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;
use App\Classes\CourierCollection;
use App\Classes\DispatchBatchLog;
use App\Classes\DispatchBatch;

/**
 * DispatchBatch Class Test Cases
 * 
 * @todo make testing email and ftp possible through valid
 *      credentials and email testing wrapper. This will then
 *      make it possible to test the endbatch method more
 *      thoroughly.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
final class DispatchBatchTest extends TestCase
{
    /**
     * Test we can create a dispatch batch instance
     * 
     * @return void
     */
    public function testDispatchBatchInit(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $this->assertInstanceOf(DispatchBatch::class, $dispatchBatch);
    }

    /**
     * Test we can start a dispatch batch and have an empty
     *  consignment stack
     * 
     * @return void
     */
    public function testDispatchBatchStart(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $dispatchBatch->startBatch();
        $this->assertEquals([], $dispatchBatch->getConsignmentsSoFar("RM"));
        $this->assertEquals([], $dispatchBatch->getConsignmentsSoFar("ANC"));
    }

    /**
     * Test we can start a dispatch batch and try to start a batch
     *  again before one is ended has no influence.
     * 
     * @return void
     */
    public function testDispatchBatchDoubleStart(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $dispatchBatch->startBatch();
        $dispatchBatch->startBatch();
        $this->assertEquals([], $dispatchBatch->getConsignmentsSoFar("RM"));
        $this->assertEquals([], $dispatchBatch->getConsignmentsSoFar("ANC"));
    }

    /**
     * Test we can add consignments to a batch that has started and these are
     *  kept track off within the dispatchbatch instance.
     * 
     * @return void
     */
    public function testAddConsignments(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $ANC = new ANC("ANC", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $courierCollection->addCourier($ANC, "ANC");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $dispatchBatch->startBatch();
        $rmConsignments = [];
        $rmConsignments[] = $dispatchBatch->addConsignment("RM");
        $rmConsignments[] = $dispatchBatch->addConsignment("RM");
        $rmConsignments[] = $dispatchBatch->addConsignment("RM");
        $ancConsignments[] = $dispatchBatch->addConsignment("ANC");
        $ancConsignments[] = $dispatchBatch->addConsignment("ANC");
        $this->assertEquals($rmConsignments, 
            $dispatchBatch->getConsignmentsSoFar("RM"));
        $this->assertEquals($ancConsignments,
             $dispatchBatch->getConsignmentsSoFar("ANC"));
    }
    
    /**
     * Test we cannot add consignments when batch has not started.
     * We expect an error string to be returned.
     * 
     * @return void
     */
    public function testAddConsignmentsWithoutBatchStart(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");

        $rmConsignment = $dispatchBatch->addConsignment("RM");
        $this->assertEquals("Error - batch has not started.", 
            $rmConsignment);
    }

    /**
     * Test we cannot add consignments with a non existent courier reference.
     * We expect an error string to be returned.
     * 
     * @return void
     */
    public function testAddConsignmentsNonExistentCourier(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $dispatchBatch->startBatch();
        $rmConsignment = $dispatchBatch->addConsignment("ANC");
        $this->assertEquals("Error - courier does not exist.", 
            $rmConsignment);
    }

     /**
     * Test we can start batch, add consignments and end the batch.
     * When we start a new batch the consignment stack should be emmpty.
     * 
     * @return void
     */
    public function testStartAndEndBatch(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $courierCollection = new CourierCollection();
        $courierCollection->addCourier($royalMail, "RM");
        $dispatchBatch = new DispatchBatch($courierCollection, "tmp/");
        $dispatchBatch->startBatch();
        $rmConsignment = $dispatchBatch->addConsignment("ANC");
        $dispatchBatch->endBatch();
        $dispatchBatch->startBatch();
        $this->assertEquals([], 
            $dispatchBatch->getConsignmentsSoFar("RM"));
    }
}