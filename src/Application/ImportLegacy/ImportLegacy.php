<?php

namespace Weather\Application\ImportLegacy;

use Safe\DateTimeImmutable;
use Weather\Application\Presenter\PresenterInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Shared\FileSystemUtils;

use function Safe\file_get_contents;
use function Safe\json_decode;

class ImportLegacy
{
    public function __construct(
        private readonly PresenterInterface $presenter,
        private readonly WeatherInfoRepositoryInterface $repository
    ) {
    }

    public function execute(ImportLegacyRequest $request): void
    {
        $totalEntries = 0;
        if (is_dir($request->getPath())) {
            foreach (FileSystemUtils::getFilesRecursive($request->getPath()) as $file) {
                $totalEntries += $this->saveFile($file);
            }
        } else {
            $totalEntries = $this->saveFile($request->getPath());
        }
        $this->presenter->write(new ImportLegacyResponse($totalEntries));
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
