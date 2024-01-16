<?php

namespace Weather\WeatherStack;

use Weather\Application\Share\PresenterInterface;
use Weather\Application\Share\Response;
use Safe\DateTimeImmutable;

use function Safe\mkdir;

class PresenterFile implements PresenterInterface
{
    private Response $response;

    private const STORAGE_FILE_PREFIX = "/documents";
    private const DIR_MOD = 0755;
    private const FOLDER_SEPRATOR = "/";

    private string $filesystemRoot = self::STORAGE_FILE_PREFIX;

    public function __construct(private DateTimeImmutable $createdAt = new DateTimeImmutable())
    {
        $this->filesystemRoot = getenv("DOC_ROOT_DIR") ?: self::STORAGE_FILE_PREFIX;
    }

    public function write(Response $response): void
    {
        $this->response = $response;

        $content = json_encode($this->response, JSON_PRETTY_PRINT);

        $filename = $this->getFilenameFullPath();
        file_put_contents($filename, $content);
    }

    public function read(): Response
    {
        return $this->response;
    }

    public function getFilenameFullPath(): string
    {
        $fullPath = $this->buildSubDirPath();
        $filename = $this->createdAt->format("Y-m-d-H-i") . ".json";
        return $fullPath . self::FOLDER_SEPRATOR . $filename;
    }

    private function buildSubDirPath(): string
    {
        $fullDir = $this->getFullPath();
        if (!is_dir($fullDir)) {
            mkdir($fullDir, self::DIR_MOD, true);
        }
        return $fullDir;
    }

    private function getFullPath(): string
    {
        return $this->filesystemRoot . self::FOLDER_SEPRATOR . $this->getSubdir();
    }

    private function getSubdir(): string
    {
        return $this->createdAt->format("Y/m/d");
    }
}
