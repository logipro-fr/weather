<?php

namespace Weather\Tests\Infrastructure\Shared;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Weather\Infrastructure\Shared\FileSystemUtils;

use function Safe\scandir;

class FileSystemUtilsTest extends TestCase
{
    /** @var array<string> */
    private array $targetFiles;

    private function createVFS(): void
    {
        vfsStreamWrapper::register();
        $root = new vfsStreamDirectory("tester");
        vfsStreamWrapper::setRoot($root);
        $subA = new vfsStreamDirectory("a");
        $subAA = new vfsStreamDirectory("a");
        $root->addChild($subA);
        $subA->addChild($subAA);
        $subAA->addChild(new vfsStreamFile("a.txt"));
        $root->addChild(new vfsStreamDirectory("b"));
        $root->addChild(new vfsStreamDirectory("c"));
        $root->addChild(new vfsStreamFile("d.txt"));

        $this->targetFiles = [
            vfsStream::url("tester") . "/a/a/a.txt",
            vfsStream::url("tester") . "/d.txt",
        ];
    }

    public function setUp(): void
    {
        $this->createVFS();
    }

    public function testGetFilesRecursive(): void
    {
        $resA = FileSystemUtils::getFilesRecursive(vfsStream::url("tester"));

        $this->assertEquals($this->targetFiles, $resA);
    }

    public function testAddSeparator(): void
    {
        $reflector = new ReflectionClass(FileSystemUtils::class);
        $method = $reflector->getMethod("addSeparator");
        $this->assertEquals("a/", $method->invokeArgs(null, ["a"]));
        $this->assertEquals("a/", $method->invokeArgs(null, ["a/"]));
    }
}
