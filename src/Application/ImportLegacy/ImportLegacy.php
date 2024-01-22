<?php

namespace Weather\Application\ImportLegacy;

use Safe\DateTimeImmutable;
use Weather\Application\Presenter\PresenterInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\scandir;

class ImportLegacy
{
    private const CURRENT_DIRECTORY_ACCESS = ".";
    private const PARENT_DIRECTORY_ACCESS = "..";
    private const FILESYSTEM_SEPARATOR = "/";

    public function __construct(
        private readonly PresenterInterface $presenter,
        private readonly WeatherInfoRepositoryInterface $repository
    ) {
    }

    public function execute(ImportLegacyRequest $request): void
    {
        $totalFiles = 0;
        if (is_dir($request->getPath())) {
            foreach ($this->getSubFilesRecursively($request->getPath()) as $file) {
                $totalFiles += $this->saveFile($file);
            }
        } else {
            $totalFiles = $this->saveFile($request->getPath());
        }
        $this->presenter->write(new ImportLegacyResponse($totalFiles));
    }

    /**
     * @return array<string>
     */
    private function getSubFilesRecursively(string $directoryPath): array
    {
        $files = [];
        $entries = $this->getDirectoryContents($directoryPath);
        foreach ($entries as $entry) {
            $fullPath = $directoryPath . $entry;
            if (is_dir($fullPath)) {
                $files = array_merge($files, $this->getSubFilesRecursively($fullPath . self::FILESYSTEM_SEPARATOR));
            } else {
                array_push($files, $fullPath);
            }
        }
        return $files;
    }

    /**
     * @return array<string>
     */
    private function getDirectoryContents(string $directoryPath): array
    {
        $entries = scandir($directoryPath);
        return array_diff(
            $entries,
            [self::CURRENT_DIRECTORY_ACCESS, self::PARENT_DIRECTORY_ACCESS]
        );
    }

    private function saveFile(string $filePath): int
    {
        /**
         * @var \stdClass $json
         * @property \stdClass $weatherHotPoints
         * @property string $report->requestedAt->date
         */
        $json = json_decode(file_get_contents($filePath));

        $date = new DateTimeImmutable($json->report->requestedAt->date);

        /** @var array<string,string> $weatherDataPoints */
        $weatherDataPoints = get_object_vars($json->weatherHotPoints);

        foreach ($weatherDataPoints as $coordinatesString => $data) {
            $coordinates = explode(",", $coordinatesString);
            $lattitude = floatval($coordinates[0]);
            $longitude = floatval($coordinates[1]);
            $point = new Point($lattitude, $longitude);

            /**
             * @var \stdClass $jsonData
             * @property string $location->localtime
             */
            $jsonData = json_decode($data);
            $date = new DateTimeImmutable($jsonData->location->localtime);

            $this->repository->save(new WeatherInfo($point, $date, $data, false));
        }
        return sizeof($weatherDataPoints);
    }
}
