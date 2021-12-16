<?php

namespace App\Spiders;

use RoachPHP\Spider\AbstractSpider;
use RoachPHP\Spider\Configuration\ArrayLoader;
use function config;

abstract class Spider extends AbstractSpider
{
    protected string $name;

    public function __construct()
    {
        parent::__construct(
            new ArrayLoader(config('spider.' . $this->name))
        );
    }
}
