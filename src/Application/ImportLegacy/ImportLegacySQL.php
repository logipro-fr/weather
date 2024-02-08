<?php

namespace Weather\Application\ImportLegacy;

use PDO;
use PDOStatement;
use Safe\DateTimeImmutable;
use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class ImportLegacySQL implements ServiceInterface
{
    private PDO $pdo;
    public function __construct(
        private readonly AbstractPresenter $presenter,
        private readonly WeatherInfoRepositoryInterface $repository
    ) {
    }

    /**
     * @param ImportLegacySQLRequest $request
     */
    public function execute(RequestInterface $request): void
    {
        $this->pdo = new PDO($request->getDB(), $request->getUser(), $request->getPwd());
        /** @infection-ignore-all */
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $query = $this->pdo->query("SELECT * FROM " . $request->getTable());

        $size = 0;
        /** @var PDOStatement $query */
        while ($row = $query->fetch()) {
            /** @var array<string,float|string> $row */
            $this->addItem($row);
            $size += 1;
            unset($row);
        }
        $this->presenter->write(new ImportLegacyResponse($size));
    }

    /** @param array<string,float|string> $row */
    private function addItem(array $row): void
    {
        /** @var float $lat */
        $lat = $row["requested_latitude"];
        /** @var float $lon */
        $lon = $row["requested_longitude"];
        /** @var string $date */
        $date = $row["requested_date"];
        /** @var string $data */
        $data = $row["json_current_weather"];
        /** @var string $id */
        $id = $row["current_weather_id"];
        $info = new WeatherInfo(
            new Point($lat, $lon),
            DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $date),
            $data,
            Source::WEATHERSTACK,
            false,
            new WeatherInfoId($id)
        );
        $this->repository->save($info);
    }

    public function getPresenter(): AbstractPresenter
    {
        return $this->presenter;
    }
}
