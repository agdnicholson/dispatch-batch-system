<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;

/**
 * Courier Class Test Cases
 *
 * @author Andrew Nicholson (18 October 2020)
 */
final class CourierTest extends TestCase
{
    /**
     * Tests the extended courier classes are still also courier objects
     * 
     * @return void
     */
    public function testCourierInstance(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $this->assertInstanceOf(Courier::class, $royalMail);
    }

    /**
     * Tests we cam get the courier name back
     * 
     * @return void
     */
    public function testCourierName(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $this->assertEquals("Royal Mail", 
            $royalMail->getName());
    }

    /**
     * Tests we can get the transport method back
     * 
     * @return void
     */
    public function testCourierTransportMethod(): void
    {
        $royalMail = new RoyalMail("Royal Mail", "email", []);
        $this->assertEquals("email", 
            $royalMail->getTransportMethod());
    }

    /**
     * Tests we can get the transport credentials back
     * 
     * @return void
     */
    public function testCourierTransportCredentials(): void
    {
        $transportCreds = ["to" => "some-email@somedomain.com",
            "from" => "some-email@somedomain.com"];
        $royalMail = new RoyalMail("Royal Mail", "email", $transportCreds);
        $this->assertEquals($transportCreds, 
            $royalMail->getTransportCredentials());
    }

    /**
     * Tests that part of the consignment algorithm's return 
     *  is definitely correct
     * 
     * @return void
     */
    public function testConsignmentAlgorithmContent(): void
    {
        $royalMail = new ANC("ANC", "email", []);
        $this->assertStringStartsWith(date('Ymd'), 
            $royalMail->getConsignmentNumber());
    }

    /**
     * Tests that the consignment algorithm returns the correct string length
     * 
     * @return void
     */
    public function testConsigmentAlgorithmLength(): void 
    {
        $royalMail = new ANC("ANC", "email", []);
        $this->assertEquals(strlen(date('Ymd')."123456"), 
            strlen($royalMail->getConsignmentNumber()));
    }
}

/**
 *  RoyalMail courier class, extends the abstract courier class.
 *  Implements the required consignment algorithm method.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
class RoyalMail extends Courier {

	/**
     * The required get consignmentnumber method.
     * A test implementation of the number algorithm.
     * 
	 * @return string
	 */
	public function getConsignmentNumber() : string
	{
		$randomNumber = "".strval(rand(1,9));
		for ($i = 0; $i < 9; $i++) {
			$randomNumber .= strval(rand(0,9));
		}
		return $randomNumber."-GB";
	}
}

/**
 * ANC courier class, extends the abstract courier class
 * Implements the required consignment algorithm method.
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
class ANC extends Courier {

	/**
     * The required get consignmentnumber method.
     * A test implementation of the number algorithm.
     * 
	 * @return string
	 */
	public function getConsignmentNumber() : string
	{
		$randomNumber = date('Ymd')."";
		for ($i = 0; $i < 6; $i++) {
			$randomNumber .= strval(rand(0,9));
		}
		return $randomNumber;
	}
}