<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\WeatherStack\Domain\Model\Exceptions\HistoricalDayNotFoundException;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use JsonException;

use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\json_decode;
use function Safe\mkdir;
use function Safe\scandir;
use function Safe\unlink;

class HistoricalDayRepositoryStore implements HistoricalDayRepositoryInterface
{
    private const DIR_MOD = 0755;

    /** @var array<string,HistoricalDay> */
    private array $cachedHistoricalDays;

    public function add(HistoricalDay $weatherHistoricalFeatures): void
    {
        $fullDir = StoreFileName::getPath($weatherHistoricalFeatures->getId());
        if (!is_dir($fullDir)) {
            mkdir($fullDir, self::DIR_MOD, true);
        }
        $filename = StoreFileName::getFullFileName($weatherHistoricalFeatures->getId());
        $content = $weatherHistoricalFeatures->getBrutContent();
        file_put_contents($filename, $content);
    }

    public function existById(HistoricalDayId $id): bool
    {
        if (isset($this->cachedHistoricalDays[$id->__toString()])) {
            return true;
        }

        $filename = StoreFileName::getFullFileName($id);
        return is_file($filename);
    }

    public function findById(HistoricalDayId $id): HistoricalDay
    {
        if (isset($this->cachedHistoricalDays[$id->__toString()])) {
            return $this->cachedHistoricalDays[$id->__toString()];
        }

        $filename = StoreFileName::getFullFileName($id);
        if (is_file($filename)) {
            $content = file_get_contents($filename);
            $this->cachedHistoricalDays[$id->__toString()] = new HistoricalDay($id, $content);
            return $this->cachedHistoricalDays[$id->__toString()];
        }
        throw new HistoricalDayNotFoundException();
    }

    /**
     * @throws HistoricalDayNotFoundException
     */
    public function findByHistoricalHourId(HistoricalHourId $hourId): HistoricalDay
    {
        $dayId = new HistoricalDayId($hourId->getLocationTime()->getPoint(), $hourId->getHistoricalDate());

        return $this->findById($dayId);
    }

    public function existdByHistoricalHourId(HistoricalHourId $hourId): bool
    {
        $dayId = new HistoricalDayId($hourId->getLocationTime()->getPoint(), $hourId->getHistoricalDate());

        return $this->existById($dayId);
    }

    public function clean(): void
    {
        $days = $this->listJsonFilesRecursive(StoreFileName::getRoot());
        for ($i = 0; $i < count($days); $i++) {
            $dayFileName = $days[$i];
            try {
                $content = json_decode(file_get_contents($dayFileName));
                if (is_object($content) && (isset($content->success) || (!isset($content->historical)))) {
                    unlink($dayFileName);
                }
            } catch (JsonException $e) {
                unlink($dayFileName);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function listJsonFilesRecursive(string $directoryPath): array
    {
        $jsonFiles = array();
        $this->traverseDirectory($directoryPath, $jsonFiles);
        return $jsonFiles;
    }

    /**
     * @param array<string> $jsonFiles
     */
    private function traverseDirectory(string $directory, array &$jsonFiles): void
    {
        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                $jsonFiles[] = $filePath;
            } elseif (is_dir($filePath)) {
                $this->traverseDirectory($filePath, $jsonFiles);
            }
        }
    }
}
