<?php

declare(strict_types=1);

use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;

return [
    FrameworkBundle::class => ['all' => true],
    DoctrineMongoDBBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
];
