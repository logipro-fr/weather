<?php

namespace Weather\Tests\Domain;

use Phariscope\Event\EventAbstract;
use Phariscope\Event\EventSubscriber;

class TestSubscriber implements EventSubscriber
{
    public EventAbstract $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<EventAbstract> */
    public array $traces;

    public function handle(EventAbstract $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        array_push($this->traces, $aDomainEvent);
        $this->handleCallCount++;
        return true;
    }

    public function isSubscribedTo(EventAbstract $aDomainEvent): bool
    {
        return true;
    }
}
