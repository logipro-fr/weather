<?php

namespace Weather\Tests\Share\Infrastructure\Doctrine;

use Weather\Share\Infrastructure\Symfony\CommonKernel;
use Weather\Tests\Share\Infrastructure\RunCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Kernel;

trait DoctrineRepositoryTesterTrait
{
    use RunCommand;

    private Kernel $myKernel;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $env = "test";
        $isDebug = false;
        $this->myKernel = new CommonKernel($env, $isDebug);
        $this->myKernel->boot();
    }

    /** @param array<string> $tablesToReset */
    private function resetDatabase(
        array $tablesToReset = [
            "currentweathers",
        ]
    ): void {
        /** @var string $tableToReset */
        foreach ($tablesToReset as $tableToReset) {
            $this->runCommand(sprintf('doctrine:query:sql "DROP TABLE %s"', $tableToReset));
        }
        $this->runCommand('doctrine:schema:update --complete --force');
    }

    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->myKernel->getContainer()->get("doctrine.orm.entity_manager");
        return $em;
    }
}
