<?php

declare(strict_types=1);

namespace Weather\Tests\Share\Infrastructure;

use Weather\Share\Infrastructure\Symfony\CommonKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

trait RunCommand
{
    private const ENVIRONMENT = "test";
    private const IS_DEBUG = false;

    public function runCommand(string $command): void
    {
        if (!is_file((string)getcwd() . '/vendor/autoload_runtime.php')) {
            throw new \LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
        }
        require_once (string)getcwd() . '/vendor/autoload_runtime.php';

        $kernel = new CommonKernel(self::ENVIRONMENT, self::IS_DEBUG);
        $app = new Application($kernel);
        $app->setAutoExit(false);
        $command = $command . ' --quiet';
        $app->run(new StringInput($command));
    }
}
