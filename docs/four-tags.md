# The four tags

**Resolved. All four are tagged and serving on Packagist**, and you can settle
that yourself in one request -- `https://repo.packagist.org/p2/coolms/theme-default.json`,
or `composer show coolms/theme-default --all` -- which is why no version numbers
are repeated here.

What follows is the record of how that was reached, written while it was still
open. **The table below is the state on 2026-09-03, not today**, and it is kept
because the reasoning is the point: what tagging needed first, and why tagging
alone was not enough for three of the four.

| package | Packagist | git tags | changelog | release set |
| --- | --- | --- | --- | --- |
| `coolms/ooxml` | registered, **0 versions** | 0 | no | independent |
| `coolms/theme-admin` | **404** | 0 | no | lockstep |
| `coolms/theme-bootstrap` | **404** | 0 | no | lockstep |
| `coolms/theme-default` | **404** | 0 | no | lockstep |

All four are real public GitHub repositories on `develop`, fully pushed.
The problem is not that the code is missing; it is that nothing names a
version of it.

!! **It is worse than "no tags" for three of them.** `coolms/ooxml` is at
least registered on Packagist and would serve a tag the moment one exists.
The three themes are **not registered at all**, so tagging them is
necessary and not sufficient -- each also needs submitting.

## Why the skeleton installs anyway

`theme-admin`, `theme-bootstrap` and `theme-default` each carry
`extra.branch-alias: {"dev-develop": "1.0.x-dev"}`, so a `vcs` repository
entry plus `minimum-stability: dev` made `^1.0` resolve. That was the only
reason this skeleton worked at the time, and it is why `composer.json` then
carried three repository entries a released product would not have.

**Those entries are gone.** `composer.json` in this repository now has an empty
`repositories` list -- read it and see -- which is step 4 below, and the
acceptance test the whole exercise existed to pass.

!! `coolms/ooxml` has **no branch-alias and no tag**, so `^1.0` resolves to
nothing at all. Any consumer must write `dev-develop` explicitly. The
skeleton sidesteps it by not requiring ooxml -- nothing in the published set
depends on it, and the modules that use it (Word, Spreadsheet) are not
extracted.

## `theme-coolms-site` stays out -- confirmed, not assumed

Checked two ways before anything was cut:

- it does not appear in the skeleton's `composer.lock`;
- no file in the skeleton outside `vendor/` mentions `coolms-site` or
  `CoolmsSite`.

The dependency the temporary-passengers note warned about is real but
confined to the CoolMS application, where
`InstallCoolmsSiteCommand` + the `bundles.php` registration + the
`composer.json` requirement keep it. A skeleton built fresh, rather than
copied from that tree, never inherits it.

## What tagging needs first

The release policy is "no changelog, no release", and
`release-tools/release-packages.sh` enforces it by reading the version out
of `CHANGELOG.md`. **None of the five has one.** So the order is:

1. write a `CHANGELOG.md` for each of the four (`theme-coolms-site` is not a
   release target);
2. tag -- and per the cadence, an **alpha** matching the generation rather
   than a stable tag. (The generation has moved on since; `composer.lock` in
   this repository pins the exact set, which is the only version list here
   that cannot go stale.)
3. submit the three themes to Packagist, which `ooxml` does not need;
4. delete the three `vcs` entries from this skeleton's `composer.json` and
   re-run the clean-container install to prove it.

Step 4 is the acceptance test, and it is the reason to do this at all: until
it passes, "CoolMS is installable" is a statement about one laptop.

!! Note the release set puts the four themes **lockstep** with
`coolms/core` and `coolms/ooxml` **independent**, so they do not get the same
version number. The set is computed from the dependency graph -- a package is in
it if and only if it requires `coolms/core`, directly or transitively -- and
never hand-maintained, because a hand-kept list goes stale the first time a
package is added. (The tool that computes it lives in a private repository, so
this is one of the few statements on this page you cannot check from here.)

## Outcome, 2026-09-03

All four are done, and the count of packages `coolms/*` serving at least one
version on Packagist is **16 of 16**.

| package | version |
| --- | --- |
| `coolms/ooxml` | `v1.0.0-alpha1` (independent numbering) |
| `coolms/theme-bootstrap` | `v2.0.0-alpha3` |
| `coolms/theme-default` | `v2.0.0-alpha3` |
| `coolms/theme-admin` | `v2.0.0-alpha3` |

!! **alpha3 rather than alpha2, because alpha2 was broken.** Each theme's
bundle class reaches `Config\Definition\ConfigurableInterface` through
`AbstractBundle`, and `symfony/dependency-injection` carries `symfony/config`
in `require-dev`. None of the three declared it, so all three installed
perfectly and could not load their own bundle. Found by resolving each from
its tag into an empty tree and checking every `use` in its own `src/` against
the result -- before submission, which is why nothing had to be retracted.

`theme-coolms-site` stayed out, as planned.

