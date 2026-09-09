<?php

// Only what a minimal CoolMS application needs to boot.
//
// This list is SHORT because most of CoolMS is not in a package yet. The
// application tree carries 64 modules in `src/` that belong here as
// `coolms/*` bundles and cannot be required today -- see
// docs/what-the-skeleton-cannot-ship.md. A skeleton cannot register what
// does not ship.
//
// !! ADDED BY A FLEX RECIPE ONCE, THEN COMMITTED. SecurityBundle and
// ApiPlatformBundle below arrived when `composer install` ran the recipes on a
// clean clone. `symfony.lock` is committed now, so the recipes no longer run
// and this file is not rewritten again -- which is the point: a recipe deleted
// this comment the first time it touched the file.
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    CoolMS\Rql\Doctrine\RqlDoctrineBundle::class => ['all' => true],
    CoolMS\CoreBundle\CoreBundle::class => ['all' => true],
    CoolMS\Core\Doctrine\CoreDoctrineBundle::class => ['all' => true],
    CoolMS\EntityBundle\EntityBundle::class => ['all' => true],
    CoolMS\Dtmpl\Bundle\DtmplBundle::class => ['all' => true],
    CoolMS\ThemeBootstrap\ThemeBootstrapBundle::class => ['all' => true],
    CoolMS\ThemeDefault\ThemeDefaultBundle::class => ['all' => true],
];
