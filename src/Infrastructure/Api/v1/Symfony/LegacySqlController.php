<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\ImportLegacy\ImportLegacyFile;
use Weather\Application\ImportLegacy\ImportLegacyFileRequest;
use Weather\Application\ImportLegacy\ImportLegacySQL;
use Weather\Application\ImportLegacy\ImportLegacySQLRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class LegacySqlController extends RequestController
{
    private const DATABASE_ARGUMENT = "db";
    private const TABLE_ARGUMENT = "table";
    private const USER_ARGUMENT = "user";
    private const PASSWORD_ARGUMENT = "pwd";

    public function __construct(private WeatherInfoRepositoryInterface $repository)
    {
    }

    protected function createService(): ServiceInterface
    {
        return new ImportLegacySQL(new PresenterJson(), $this->repository);
    }

    protected function createRequest(InputBag $query): RequestInterface
    {
        if (null === $query->get(self::DATABASE_ARGUMENT)) {
            throw new InvalidArgumentException("no database \"" . self::DATABASE_ARGUMENT .
            "\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $database */
        $database = $query->get(self::DATABASE_ARGUMENT);

        if (null === $query->get(self::TABLE_ARGUMENT)) {
            throw new InvalidArgumentException("no \"" . self::TABLE_ARGUMENT .
            "\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $table */
        $table = $query->get(self::TABLE_ARGUMENT);

        if (null === $query->get(self::USER_ARGUMENT)) {
            throw new InvalidArgumentException("no \"" . self::USER_ARGUMENT .
            "\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $user */
        $user = $query->get(self::USER_ARGUMENT);

        /** @var string $password */
        $password = $query->get(self::PASSWORD_ARGUMENT, "");

        return new ImportLegacySQLRequest($database, $table, $user, $password);
    }
}
