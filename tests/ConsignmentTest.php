<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Consignment;

/**
 * Courier Class Test Cases
 *
 * @author Andrew Nicholson (21 October 2020)
 */
final class ConsignmentTest extends TestCase
{
    /**
     * Tests we can create a Consignment object.
     * 
     * @return void
     */
    public function testConsignmentInstance(): void
    {
        $consignment = new Consignment("Royal Mail", "123456789-GB");
        $this->assertInstanceOf(Consignment::class, $consignment);
    }

    /**
     * Tests we can get the Courier Reference back
     * 
     * @return void
     */
    public function testConsignmentCourierRef(): void
    {
        $consignment = new Consignment("Royal Mail", "123456789-GB");
        $this->assertEquals("Royal Mail", 
            $consignment->getCourierRef());
    }

     /**
      * Tests we can get the consignment number back
      *
      * @return void
      */
      public function testConsignmentNumber(): void
    {
        $consignment = new Consignment("Royal Mail", "123456789-GB");
        $this->assertEquals("123456789-GB", 
            $consignment->getConsignmentNumber());
    }
}