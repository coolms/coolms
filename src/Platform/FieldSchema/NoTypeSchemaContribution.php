<?php

declare(strict_types=1);

namespace App\Platform\FieldSchema;

use CoolMS\Entity\ValueObject\EntityTypeSchemaContribution;
use CoolMS\Entity\Contract\EntityTypeSchemaContributorInterface;

/**
 * STAND-IN. Not application code.
 *
 * The contributor port `coolms/entity` requires. Its only implementation
 * is `src/DynamicEntity` in the CoolMS application, which does not ship.
 *
 * `null` is the contract's own answer for "this module knows nothing
 * about that alias", which is exactly true here.
 *
 * DELETE THIS FILE the day `coolms/dynamic-entity` exists.
 */
final readonly class NoTypeSchemaContribution implements EntityTypeSchemaContributorInterface
{
    public function getSchemaContribution(string $entityAlias): ?EntityTypeSchemaContribution
    {
        return null;
    }
}
