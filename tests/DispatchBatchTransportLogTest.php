<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\DispatchBatchTransportLog;

/**
 * DispatchBatchTransportLog Class Test Cases
 * 
 * @author Andrew Nicholson (21 October 2020)
 */
final class DispatchBatchTransportLogTest extends TestCase
{
    /**
     * Test we can make a success log
     * 
     * @return void
     */
    public function testSuccessLog(): void
    {
        $this->assertEquals(TRUE, 
        DispatchBatchTransportLog::logSuccess("Royal Mail", date('Ymd'), "email", []));
    }

    /**
     * Test we can make an error log
     * 
     * @return void
     */
    public function testErrorLog(): void
    {
        $this->assertEquals(TRUE, 
        DispatchBatchTransportLog::logError("Royal Mail", date('Ymd'), "email", []));
    }
}