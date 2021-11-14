<?php declare(strict_types=1);

require_once __DIR__ . '/../inc/kernel_utility.php';

use PHPUnit\Framework\TestCase;

final class KernelUtilityTest extends TestCase
{
    
    /**
     * Test the display of version as a string
     */
    public function testGetVersion(): void
    {
        $version = get_version();
        $this->assertIsString($version);
    }

    /**
     * Test the correct format of version as v{3-digit MAJOR}{3-digit MINOR}{3-digit PATCH}
     */
    public function testGetVersionNumber(): void 
    {
        $version = get_version_number();
        $this->assertIsString($version);
        $this->assertTrue(str_starts_with($version, 'v'));
        $this->assertSame(10, strlen($version));
    }

}
