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
    private function getSubFilesRecursively(string $dirPath): array
    {
        $files = [];
        $entries = scandir($dirPath);
        $entries = array_diff($entries, [".", ".."]);
        foreach ($entries as $entry) {
            if (is_dir($dirPath . $entry)) {
                $files = array_merge($files, $this->getSubFilesRecursively($dirPath . $entry . "/"));
            } else {
                array_push($files, $dirPath . $entry);
            }
        }
        return $files;
    }

    private function saveFile(string $filePath): int
    {
        $json = json_decode(file_get_contents($filePath));

        $date = new DateTimeImmutable($json->report->requestedAt->date);

        $weatherDataPoints = get_object_vars($json->weatherHotPoints);

        foreach ($weatherDataPoints as $coordinatesString => $data) {
            $coordinates = explode(",", $coordinatesString);
            $lattitude = floatval($coordinates[0]);
            $longitude = floatval($coordinates[1]);
            $point = new Point($lattitude, $longitude);
            $date = new DateTimeImmutable(json_decode($data)->location->localtime);

            $this->repository->save(new WeatherInfo($point, $date, $data, false));
        }
        return sizeof($weatherDataPoints);
    }
}
