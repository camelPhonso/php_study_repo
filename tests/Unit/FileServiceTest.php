<?php

declare(strict_types= 1);

namespace Tests\Unit;

use App\Services\FileService;
use PHPUnit\Framework\TestCase;

class FileServiceTest extends TestCase
{
    private FileService $fileService;

    protected function setUp(): void
    {
        // Arrange
        parent::setUp();

        $this->fileService = new FileService();
    }

    /** @test */
    public function it_successfully_processes_an_upload(): void
    {
        // Arrange
        $_POST = true;
        $_FILES["invoice-upload"] = [
            "name"=> "test file",
            "tmp_name" => "tests/resources/test-file.csv",
            "type" => "file",
            "size" => "500mb",
            "error" => 0
        ];

        // Act
        $result = $this->fileService->receiveUpload();

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_successfully_parses_an_upload(): void
    {
        $result = $this->fileService->parseInvoice("/var/www/tests/resources/test-file.csv");

        $this->assertEquals('7777', $result->getInvoiceNumber());
    }
}