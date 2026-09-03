<?php

declare(strict_types=1);

namespace App\Platform\FieldSchema;

use CoolMS\Entity\Contract\FieldMetadataSourceInterface;
use CoolMS\Entity\Contract\FieldSchemaSourceInterface;

/**
 * STAND-IN. Not application code.
 *
 * `coolms/entity` and `coolms/entity-doctrine` both REQUIRE these two
 * ports as constructor arguments, and the only implementation of either
 * ships in the CoolMS application as `src/Field` -- a module that is not
 * extracted and cannot be installed. Without this class the container
 * does not compile.
 *
 * Both contracts document the empty result as meaningful ("never null,
 * so callers can merge unconditionally"), so an installation with no
 * field-definition module is a coherent state, not a broken one: there
 * are no record-defined fields because nothing defines them.
 *
 * DELETE THIS FILE the day `coolms/field` exists.
 */
final readonly class NoRuntimeFields implements FieldSchemaSourceInterface, FieldMetadataSourceInterface
{
    /** @return array<string, array<string, mixed>> */
    public function getRuntimeFields(string $entityAlias): array
    {
        return [];
    }

    /** @return array<string, array{type: string, label: string}> */
    public function getRuntimeFieldSummaries(string $entityAlias): array
    {
        return [];
    }

    /**
     * @param class-string $className
     *
     * @return array<string, array{private: bool, hasMeta: bool, label: string, formType: string|null, sortOrder: int|null}>
     */
    public function getResolvedFieldMetadata(string $className, string $entityAlias): array
    {
        return [];
    }

    public function hasFileOverride(string $entityAlias, string $fieldName): bool
    {
        return false;
    }
}
