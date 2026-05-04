<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Persistence\File;

use Dba\DddSkeleton\Shared\Infrastructure\Persistence\File\FileRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileRepositoryTest extends TestCase
{
    private string $tmpDir;

    private FileRepository $jsonRepo;

    private FileRepository $csvRepo;

    private FileRepository $xmlRepo;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().'/file_repo_test_'.uniqid();
        mkdir($this->tmpDir);

        $tmpDir = $this->tmpDir;

        $this->jsonRepo = new class($tmpDir) extends FileRepository
        {
            protected function extension(): string
            {
                return 'json';
            }
        };
        $this->csvRepo = new class($tmpDir) extends FileRepository
        {
            protected function extension(): string
            {
                return 'csv';
            }
        };
        $this->xmlRepo = new class($tmpDir) extends FileRepository
        {
            protected function extension(): string
            {
                return 'xml';
            }
        };
    }

    protected function tearDown(): void
    {
        $files = glob($this->tmpDir.'/*') ?: [];
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->tmpDir);
    }

    #[Test]
    public function it_should_create_and_find_a_json_record(): void
    {
        $this->jsonRepo->create(['id' => 'abc', 'name' => 'Test']);

        $found = $this->jsonRepo->find('abc');

        $this->assertNotNull($found);
        $this->assertSame('Test', $found['name']);
    }

    #[Test]
    public function it_should_return_null_when_json_record_not_found(): void
    {
        $this->assertNull($this->jsonRepo->find('nonexistent'));
    }

    #[Test]
    public function it_should_update_a_json_record(): void
    {
        $this->jsonRepo->create(['id' => 'abc', 'name' => 'Old']);
        $this->jsonRepo->update('abc', ['id' => 'abc', 'name' => 'New']);

        $found = $this->jsonRepo->find('abc');

        $this->assertNotNull($found);
        $this->assertSame('New', $found['name']);
    }

    #[Test]
    public function it_should_delete_a_json_record(): void
    {
        $this->jsonRepo->create(['id' => 'abc', 'name' => 'Test']);
        $this->jsonRepo->delete('abc');

        $this->assertNull($this->jsonRepo->find('abc'));
    }

    #[Test]
    public function it_should_return_all_json_records_with_matching(): void
    {
        $this->jsonRepo->create(['id' => 'a', 'name' => 'Alice']);
        $this->jsonRepo->create(['id' => 'b', 'name' => 'Bob']);

        $results = $this->jsonRepo->matching([]);

        $this->assertCount(2, $results);
    }

    #[Test]
    public function it_should_create_and_find_a_csv_record(): void
    {
        $this->csvRepo->create(['id' => 'xyz', 'name' => 'CSV Test']);

        $found = $this->csvRepo->find('xyz');

        $this->assertNotNull($found);
        $this->assertSame('CSV Test', $found['name']);
    }

    #[Test]
    public function it_should_create_and_find_an_xml_record(): void
    {
        $this->xmlRepo->create(['id' => 'xml1', 'name' => 'XML Test']);

        $found = $this->xmlRepo->find('xml1');

        $this->assertNotNull($found);
        $this->assertSame('XML Test', $found['name']);
    }

    #[Test]
    public function it_should_delete_xml_record(): void
    {
        $this->xmlRepo->create(['id' => 'xml2', 'name' => 'Delete Me']);
        $this->xmlRepo->delete('xml2');

        $this->assertNull($this->xmlRepo->find('xml2'));
    }
}
