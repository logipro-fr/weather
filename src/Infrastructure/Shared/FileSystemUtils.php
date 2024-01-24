<?php

namespace Weather\Infrastructure\Shared;

use function Safe\scandir;

class FileSystemUtils
{
    private const CURRENT_DIRECTORY_ACCESS = ".";
    private const PARENT_DIRECTORY_ACCESS = "..";
    private const FILESYSTEM_SEPARATOR = "/";

    private function __construct()
    {
    }

    /**
     * @return array<string>
     */
    public static function getFilesRecursive(string $directoryPath): array
    {
        $directoryPath = self::addSeparator($directoryPath);
        $files = [];
        $entries = self::getDirectoryContents($directoryPath);
        foreach ($entries as $entry) {
            $fullPath = $directoryPath . $entry;
            if (is_dir($fullPath)) {
                $files = array_merge(
                    $files,
                    self::getFilesRecursive(self::addSeparator($fullPath))
                );
            } else {
                array_push($files, $fullPath);
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
        return $path . self::FILESYSTEM_SEPARATOR;
    }

    /**
     * @return array<string>
     */
    private static function getDirectoryContents(string $directoryPath): array
    {
        $entries = scandir($directoryPath);
        return array_diff(
            $entries,
            [self::CURRENT_DIRECTORY_ACCESS, self::PARENT_DIRECTORY_ACCESS]
        );
    }
}
