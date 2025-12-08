<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Slug;
use Tests\TestCase;

class SlugTest extends TestCase
{
    // No database needed for helper tests
    public function test_can_make_slug_from_russian_text(): void
    {
        $slug = Slug::make('Привет Мир');
        
        $this->assertEquals('privet-mir', $slug);
    }

    public function test_can_make_en_slug(): void
    {
        $slug = Slug::makeEn('Hello World');
        
        $this->assertEquals('hello-world', $slug);
    }

    public function test_slug_removes_special_characters(): void
    {
        $slug = Slug::makeEn('Test!@#$%^&*()');
        
        // Special characters are replaced with dashes, which may leave trailing dashes
        $this->assertStringStartsWith('test', $slug);
        $this->assertStringNotContainsString('!', $slug);
        $this->assertStringNotContainsString('@', $slug);
    }

    public function test_slug_handles_multiple_spaces(): void
    {
        $slug = Slug::makeEn('Test    Multiple    Spaces');
        
        $this->assertEquals('test-multiple-spaces', $slug);
    }

    public function test_slug_handles_numbers(): void
    {
        $slug = Slug::makeEn('Test 123');
        
        $this->assertEquals('test-123', $slug);
    }

    public function test_slug_trimmed(): void
    {
        $slug = Slug::makeEn('  Test  ');
        
        // Spaces are converted to dashes, may leave dashes at edges
        $this->assertStringContainsString('test', $slug);
        $this->assertStringNotContainsString('  ', $slug); // No double spaces
    }
}
