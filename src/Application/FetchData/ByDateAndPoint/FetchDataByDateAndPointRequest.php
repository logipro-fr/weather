<?php

namespace Weather\Application\FetchData\ByDateAndPoint;

use Safe\DateTimeImmutable;
use Weather\Domain\Model\Weather\Point;

class FetchDataByDateAndPointRequest
{
    public function __construct(
        private readonly Point $point,
        private readonly DateTimeImmutable $date,
        private readonly ?bool $historical = null,
        private readonly bool $isExact = false,
    ) {
    }

    public function getRequestedPoint(): Point
    {
        return $this->point;
    }

    public function getRequestedDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function historical(): bool|null
    {
        return $this->historical;
    }

    public function isExact(): bool
    {
        return $this->isExact;
    }
}
