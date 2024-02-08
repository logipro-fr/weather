<?php

namespace Weather\Application\ImportLegacy;

use PDO;
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
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $query = $this->pdo->query("SELECT * FROM " . $request->getTable());

        $size = 0;
        while($row = $query->fetch()){
            $this->addItem($row);
            $size += 1;
            unset($row);
        }
        $this->presenter->write(new ImportLegacyResponse($size));
    }

    private function addItem(array $row): void{
        $info = new WeatherInfo(
            new Point(
                $row["requested_latitude"],
                $row["requested_longitude"]
            ),
            DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $row["requested_date"]),
            $row["json_current_weather"],
            Source::WEATHERSTACK,
            false,
            new WeatherInfoId($row["current_weather_id"])
        );
    }

    public function getPresenter(): AbstractPresenter
    {
        return $this->presenter;
    }
}
