<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CustomEncrypt;
use Tests\TestCase;

class CustomEncryptTest extends TestCase
{
    // No database needed for helper tests
    public function test_can_generate_url_hash(): void
    {
        $data = ['id' => 123, 'created_at' => '2025-01-01'];
        $hash = CustomEncrypt::generateUrlHash($data);
        
        $this->assertNotEmpty($hash);
        $this->assertIsString($hash);
    }

    public function test_can_decode_url_hash(): void
    {
        $data = ['id' => 123, 'created_at' => '2025-01-01'];
        $hash = CustomEncrypt::generateUrlHash($data);
        $decoded = CustomEncrypt::decodeUrlHash($hash);
        
        $this->assertEquals($data['id'], $decoded['id']);
        $this->assertEquals($data['created_at'], $decoded['created_at']);
    }

    public function test_can_get_id_from_hash(): void
    {
        $data = ['id' => 456];
        $hash = CustomEncrypt::generateUrlHash($data);
        $id = CustomEncrypt::getId($hash);
        
        $this->assertEquals(456, $id);
    }

    public function test_hash_without_salt(): void
    {
        $data = ['id' => 789];
        $hash = CustomEncrypt::generateUrlHash($data, false);
        $decoded = CustomEncrypt::decodeUrlHash($hash, false);
        
        $this->assertEquals($data['id'], $decoded['id']);
    }

    public function test_generate_static_url_hash(): void
    {
        $data = ['id' => 999];
        $hash = CustomEncrypt::generateStaticUrlHas($data);
        
        $this->assertNotEmpty($hash);
        $this->assertIsString($hash);
    }
}
