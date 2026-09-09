<?php

declare(strict_types=1);

namespace App\Platform\Install;

use CoolMS\Core\Install\ModuleNavigationRemoverInterface;

/**
 * STAND-IN. Not application code.
 *
 * `coolms/core-bundle`'s ModuleArtifactRemover requires this port by type when
 * a module is uninstalled, and the only implementation is `src/Navi` in the
 * CoolMS application, which does not ship.
 *
 * An installation with no navigation module has no navigation nodes to remove,
 * so zero is the honest count rather than a swallowed failure.
 *
 * !! This file was deleted on 2026-09-03 and restored the same day. The
 * contract is not in `coolms/core` 2.0.0-alpha1 at all, so against that
 * release the stand-in was itself unloadable; 2.0.0-alpha2 adds it, and
 * core-bundle 2.0.0-alpha2 then requires it. Which ports are load-bearing is a
 * property of the PUBLISHED set, and it moves when the set moves.
 *
 * DELETE THIS FILE the day `coolms/navi` exists.
 */
final readonly class NoNavigationToRemove implements ModuleNavigationRemoverInterface
{
    public function unseedModule(string $moduleName, bool $dryRun = false): int
    {
        return 0;
    }
}
