<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Classes\DispatchBatchLog;

/**
 * DispatchBatchLog Class Test Cases
 * 
 * @author Andrew Nicholson (18 October 2020)
 */
final class DispatchBatchLogTest extends TestCase
{
    /**
     * Test we can make a success log
     * 
     * @return void
     */
    public function testSuccessLog(): void
    {
        $this->assertEquals(TRUE, 
            DispatchBatchLog::logSuccess("RM", date('Ymd'), "email", []));
    }

    /**
     * Test we can make an error log
     * 
     * @return void
     */
    public function testErrorLog(): void
    {
        $this->assertEquals(TRUE, 
            DispatchBatchLog::logError("RM", date('Ymd'), "email", []));
    }
}