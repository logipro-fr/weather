<?php

namespace Weather\Tests\Infrastructure\Shared;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Weather\Infrastructure\Shared\FileSystemUtils;

class FileSystemUtilsTest extends TestCase
{
    /** @var array<string> */
    private array $targetFiles;
    /** @var array<string> */
    private array $targetContents;

    public function setUp(): void
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
        $this->targetContents = [
            vfsStream::url("tester") . "/a",
            vfsStream::url("tester") . "/b",
            vfsStream::url("tester") . "/c",
            vfsStream::url("tester") . "/d.txt",
        ];
    }

    public function testAddSeparator(): void
    {
        $reflector = new ReflectionClass(FileSystemUtils::class);
        $method = $reflector->getMethod("addSeparator");
        $this->assertEquals("test/a/", $method->invokeArgs(null, ["test/a"]));
        $this->assertEquals("a/", $method->invokeArgs(null, ["a/"]));
        $this->assertNotEquals("test/a", $method->invokeArgs(null, ["test/a"]));
        $this->assertNotEquals("/", $method->invokeArgs(null, ["test/a"]));
    }

    /** @depends testAddSeparator */
    public function testGetDirectoryContents(): void
    {
        $reflector = new ReflectionClass(FileSystemUtils::class);
        $method = $reflector->getMethod("getDirectoryContents");

        /** @var array<string> */
        $result =  $method->invokeArgs(null, [vfsStream::url("tester")]);

        $this->assertEquals($this->targetContents, $result);
        $this->assertFalse(in_array(".", $result));
        $this->assertFalse(in_array("..", $result));
    }

    /** @depends testGetDirectoryContents */
    public function testGetFilesRecursive(): void
    {
        $resA = FileSystemUtils::getFilesRecursive(vfsStream::url("tester"));

        $this->assertEquals($this->targetFiles, $resA);
    }
}
