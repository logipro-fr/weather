<?php

namespace Weather\Infrastructure\Shared;

use function Safe\scandir;

class FileSystemUtils
{
    private const CURRENT_DIRECTORY_ACCESS = ".";
    private const PARENT_DIRECTORY_ACCESS = "..";
    private const FILESYSTEM_SEPARATOR = "/";

    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    /**
     * @return array<string>
     */
    public static function getFilesRecursive(string $directoryPath): array
    {
        $files = [];
        foreach (self::getDirectoryContents($directoryPath) as $entry) {
            if (is_dir($entry)) {
                $files = array_merge(
                    $files,
                    self::getFilesRecursive(self::addSeparator($entry))
                );
            } else {
                array_push($files, $entry);
            }
        }
        return $files;
    }

    // because this is necessary for some reason
    private static function addSeparator(string $path): string
    {
        if (str_ends_with($path, self::FILESYSTEM_SEPARATOR)) {
            return $path;
        }
        return sprintf("%s%s", $path, self::FILESYSTEM_SEPARATOR);
    }

    /**
     * @return array<string>
     */
    private static function getDirectoryContents(string $directoryPath): array
    {
        $directoryPath = self::addSeparator($directoryPath);
        $entries = scandir($directoryPath);
        $entries = array_diff(
            $entries,
            [self::CURRENT_DIRECTORY_ACCESS, self::PARENT_DIRECTORY_ACCESS]
        );

        $res = [];

        foreach ($entries as $entry) {
            array_push($res, sprintf("%s%s", $directoryPath, $entry));
        }
        return $res;
    }
}
