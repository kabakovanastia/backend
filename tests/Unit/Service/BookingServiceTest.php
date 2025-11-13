<?php

namespace App\Tests\Unit\Service;

use App\Service\CsvBookingService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BookingServiceTest extends TestCase
{
    private string $testFile;
    private Filesystem $fs;

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->testFile = sys_get_temp_dir() . '/test_bookings.csv';
        if ($this->fs->exists($this->testFile)) {
            $this->fs->remove($this->testFile);
        }
    }

    protected function tearDown(): void
    {
        if ($this->fs->exists($this->testFile)) {
            $this->fs->remove($this->testFile);
        }
    }

    public function testCreateBooking(): void
    {
        $service = new CsvBookingService($this->testFile);
        $booking = $service->createBooking(1, 100, 'Test comment');

        $this->assertEquals(1, $booking->id);
        $this->assertEquals(1, $booking->houseId);
        $this->assertEquals(100, $booking->userId);
        $this->assertEquals('Test comment', $booking->comment);

        $content = file_get_contents($this->testFile);
        $this->assertStringContainsString('1,1,100,"Test comment"', $content);
    }
}