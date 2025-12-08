<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Collapse;
use Tests\TestCase;

class CollapseTest extends TestCase
{
    // No database needed for helper tests
    public function test_collapse_small_number(): void
    {
        $result = Collapse::make(500);
        
        $this->assertEquals(500, $result);
    }

    public function test_collapse_thousands(): void
    {
        $result = Collapse::make(5000);
        
        $this->assertEquals('5k', $result);
    }

    public function test_collapse_millions(): void
    {
        $result = Collapse::make(2000000);
        
        $this->assertEquals('2kk', $result);
    }

    public function test_collapse_billions(): void
    {
        $result = Collapse::make(3000000000);
        
        $this->assertEquals('3kkk', $result);
    }

    public function test_subtract_percent(): void
    {
        $result = Collapse::subtractPercent(100, 10);
        
        $this->assertEquals(90, $result);
    }

    public function test_bytes_to_megabytes(): void
    {
        $result = Collapse::bytesToMegabytes(1048576); // 1 MB
        
        $this->assertEquals(1.0, $result);
    }
}
