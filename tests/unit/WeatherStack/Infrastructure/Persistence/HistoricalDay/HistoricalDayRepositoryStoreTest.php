<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryStore;
use Weather\Tests\WeatherStack\Domain\Model\HistoricalDayBuilder;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class HistoricalDayRepositoryStoreTest extends HistoricalDayRepositoryInMemoryTestBase
{
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('VFSDir'));
        $this->repository = new HistoricalDayRepositoryStore();
    }

    public function testCache(): void
    {
        $historicalDay = HistoricalDayBuilder::aHistoricalDay()
            ->build();
        $id = $historicalDay->getId();

        $this->repository->add($historicalDay);

        $result = $this->repository->findById($id);
        $this->assertEquals($historicalDay, $result);

        $result = $this->repository->findById($id);
        $this->assertEquals($historicalDay, $result);
        $this->assertTrue($this->repository->existById($id));
    }
}
