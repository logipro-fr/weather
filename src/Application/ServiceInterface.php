<?php

namespace Weather\Application;

use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\RequestInterface;

interface ServiceInterface
{
    public function execute(RequestInterface $request): void;
    public function getPresenter(): AbstractPresenter;
}
