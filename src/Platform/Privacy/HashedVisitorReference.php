<?php

declare(strict_types=1);

namespace App\Platform\Privacy;

use CoolMS\Core\Analytics\VisitorReferenceGeneratorInterface;
use DateTimeImmutable;

/**
 * STAND-IN. Not application code.
 *
 * `coolms/core-bundle`'s RequestVisitorReference requires this port. The
 * only implementation is `src/Analytics` in the CoolMS application
 * (DailyRotatingVisitorReference), which does not ship.
 *
 * ⚠️ Unlike the other four stand-ins there is no neutral answer here: an
 * empty reference would collapse every visitor into one. So this does the
 * job the contract describes -- an opaque, fixed-length reference that
 * rotates daily -- and nothing more. The IP and user-agent are consumed
 * and dropped; neither is stored.
 *
 * Replace it with the real implementation the day `coolms/analytics`
 * exists; until then, review it against your own privacy policy.
 */
final readonly class HashedVisitorReference implements VisitorReferenceGeneratorInterface
{
    public function __construct(private string $secret) {}

    public function forVisitor(string $ipAddress, string $userAgent, DateTimeImmutable $at): string
    {
        return hash_hmac('sha256', $ipAddress . '|' . $userAgent . '|' . $at->format('Y-m-d'), $this->secret);
    }
}
