<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\Courier;

/**
 * Courier Class Test Cases
 *
 * @author Andrew Nicholson (21 October 2020)
 */
final class CourierTest extends TestCase
{
    /**
     * Tests we can create a Courier object.
     * 
     * @return void
     */
    public function testCourierInstance(): void
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
        $this->assertInstanceOf(Courier::class, $royalMail);
    }

    /**
     * Tests we cam get the courier name back
     * 
     * @return void
     */
    public function testCourierName(): void
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
        $rmConsignmentNoAlgorithm = function()
        {
            $randomNumber = "".strval(rand(1,9));
            for ($i = 0; $i < 9; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber."-GB";
        };
        $transportCreds = ["to" => "some-email@somedomain.com",
            "from" => "some-email@somedomain.com"];
        $royalMail = new Courier("Royal Mail", 
                "email", 
                $transportCreds, 
                $rmConsignmentNoAlgorithm
            );
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
        $ancConsignmentNoAlgorithm = function() : string
        {
            $randomNumber = date('Ymd')."";
            for ($i = 0; $i < 6; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber;
        };
        $ANC = new Courier("ANC", "email", [], $ancConsignmentNoAlgorithm);
        $this->assertStringStartsWith(date('Ymd'), 
            $ANC->getConsignmentNumber());
    }

    /**
     * Tests that the consignment algorithm returns the correct string length
     * 
     * @return void
     */
    public function testConsigmentAlgorithmLength(): void 
    {
        $ancConsignmentNoAlgorithm = function() : string
        {
            $randomNumber = date('Ymd')."";
            for ($i = 0; $i < 6; $i++) {
                $randomNumber .= strval(rand(0,9));
            }
            return $randomNumber;
        };
        $ANC = new Courier("ANC", "email", [], $ancConsignmentNoAlgorithm);
        $this->assertEquals(strlen(date('Ymd')."123456"), 
            strlen($ANC->getConsignmentNumber()));
    }
}