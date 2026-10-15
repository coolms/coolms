# Changelog

All notable changes to `coolms/coolms`, the CoolMS skeleton, are recorded here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
The skeleton is a repository, not a release: there is no tag, so everything it
provides today is listed under Unreleased.

## Unreleased

### Added
- A minimal Symfony 8 application that installs the published `coolms/*`
  packages -- core, core-bundle, core-doctrine, entity, entity-bundle,
  entity-doctrine, rql, rql-doctrine, dtmpl, dtmpl-bundle and theme-default --
  from Packagist, compiles its container and creates its schema.
- Docker Compose services for the application, nginx and PostgreSQL 16, and the
  install steps in the README, run in CI on every change: the steps themselves,
  that `composer install` rewrites no tracked file, that the lock still describes
  the manifest, that the site answers and does not answer everything identically,
  and that a production install has no profiler.
- A second CI job that resolves `composer.json` against the registry and reports
  how far the resolved set is from the pinned one.
- Documents on what the skeleton does not provide and why:
  `docs/measured-against-the-baseline.md` (what an installation produces, item by
  item, and what the skeleton reproduces), `docs/what-the-skeleton-cannot-ship.md`,
  `docs/only-works-here.md`, `docs/four-tags.md` and `docs/how-the-admin-ships.md`.
- LICENSE (MIT, matching `composer.json`) and this changelog.
