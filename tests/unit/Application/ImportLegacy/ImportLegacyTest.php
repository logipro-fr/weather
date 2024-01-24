<?php

namespace Weather\Tests\Application\ImportLegacy;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Application\ImportLegacy\ImportLegacy;
use Weather\Application\ImportLegacy\ImportLegacyRequest;
use Weather\Application\ImportLegacy\ImportLegacyResponse;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\scandir;

class ImportLegacyTest extends TestCase
{
    public function testExecuteOnFile(): void
    {
        $filePath = "tests/Features/data/2024/01/02/2024-01-02-11-08.json";
        $request = new ImportLegacyRequest($filePath);
        $presenter = new PresenterObject();
        $repository = new WeatherInfoRepositoryInMemory();
        $service = new ImportLegacy($presenter, $repository);

        /**
         * @var \stdClass $json
         * @property \stdClass $weatherHotPoints
         */
        $json = json_decode(file_get_contents($filePath));
        $service->execute($request);

        $lengthToTest = 5;
        /** @var array<string,string> $points */
        $points = get_object_vars($json->weatherHotPoints);

        foreach (array_slice($points, 512, $lengthToTest) as $coordinates => $data) {
            /**
             * @var \stdClass $dataObject
             * @property \stdClass $location
             * @property string $location->localtime
             */
            $dataObject = json_decode($data);
            $coordinates = explode(",", $coordinates);
            $lattitude = floatval($coordinates[0]);
            $longitude = floatval($coordinates[1]);
            $point = new Point($lattitude, $longitude);
            $date = new DateTimeImmutable($dataObject->location->localtime);
            $this->assertEquals($data, $repository->findByDateAndPoint($point, $date)->getData());
            $this->assertFalse($repository->findByDateAndPoint($point, $date)->isHistorical());
        }

        $expectedResponseString = new ImportLegacyResponse(sizeof($points));
        $this->assertEquals($expectedResponseString->getData(), $presenter->read()->getData());
    }

    public function testExecuteOnDirectory(): void
    {
        $filePath = "tests/Features/data/";

        $request = new ImportLegacyRequest($filePath);
        $presenter = new PresenterObject();
        $repository = new WeatherInfoRepositoryInMemory();
        $service = new ImportLegacy($presenter, $repository);

        $service->execute($request);

        $size = 0;
        foreach ($this->getSubFilesRecursively($filePath) as $file) {
            /**
             * @var \stdclass $json
             * @property \stdClass $weatherHotPoints
             */
            $json = json_decode(file_get_contents($file));
            $points = $json->weatherHotPoints;
            $size += sizeof(get_object_vars($points));
        }

        $expectedResponseString = new ImportLegacyResponse($size);
        $this->assertEquals($expectedResponseString->getData(), $presenter->read()->getData());
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
}
