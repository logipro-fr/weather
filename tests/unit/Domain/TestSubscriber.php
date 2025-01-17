<?php

namespace Weather\Tests\Domain;

use Phariscope\Event\EventSubscriber;
use Phariscope\Event\Psr14\Event;

class TestSubscriber implements EventSubscriber
{
    public Event $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<Event> */
    public array $traces = [];

    public function handle(Event $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        array_push($this->traces, $aDomainEvent);
        $this->handleCallCount++;
        return true;
    }

    public function isSubscribedTo(Event $aDomainEvent): bool
    {
        return true;
    }
}
