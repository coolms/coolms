<?php

// Only what a minimal CoolMS application needs to boot.
//
// This list is SHORT because most of CoolMS is not in a package yet. The
// application tree carries 64 modules in `src/` that belong here as
// `coolms/*` bundles and cannot be required today -- see
// docs/what-the-skeleton-cannot-ship.md. A skeleton cannot register what
// does not ship.
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    CoolMS\Rql\Doctrine\RqlDoctrineBundle::class => ['all' => true],
    CoolMS\CoreBundle\CoreBundle::class => ['all' => true],
    CoolMS\Core\Doctrine\CoreDoctrineBundle::class => ['all' => true],
    CoolMS\EntityBundle\EntityBundle::class => ['all' => true],
    CoolMS\DtmplBundle\DtmplBundle::class => ['all' => true],
    CoolMS\ThemeBootstrap\ThemeBootstrapBundle::class => ['all' => true],
    CoolMS\ThemeDefault\ThemeDefaultBundle::class => ['all' => true],
];
