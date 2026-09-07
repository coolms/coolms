# The four tags

Measured against Packagist and each repository on 2026-09-03.

| package | Packagist | git tags | changelog | release set |
| --- | --- | --- | --- | --- |
| `coolms/ooxml` | registered, **0 versions** | 0 | no | independent |
| `coolms/theme-admin` | **404** | 0 | no | lockstep |
| `coolms/theme-bootstrap` | **404** | 0 | no | lockstep |
| `coolms/theme-default` | **404** | 0 | no | lockstep |

All four are real public GitHub repositories on `develop`, fully pushed.
The problem is not that the code is missing; it is that nothing names a
version of it.

⚠️ **It is worse than "no tags" for three of them.** `coolms/ooxml` is at
least registered on Packagist and would serve a tag the moment one exists.
The three themes are **not registered at all**, so tagging them is
necessary and not sufficient -- each also needs submitting.

## Why the skeleton installs anyway

`theme-admin`, `theme-bootstrap` and `theme-default` each carry
`extra.branch-alias: {"dev-develop": "1.0.x-dev"}`, so a `vcs` repository
entry plus `minimum-stability: dev` makes `^1.0` resolve. That is the only
reason this skeleton works today, and it is why `composer.json` still
carries three repository entries a released product would not have.

⚠️ `coolms/ooxml` has **no branch-alias and no tag**, so `^1.0` resolves to
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
2. tag -- and per the cadence, an **alpha** matching the generation, not a
   stable tag, because the platform set is still on `v2.0.0-alpha1`;
3. submit the three themes to Packagist, which `ooxml` does not need;
4. delete the three `vcs` entries from this skeleton's `composer.json` and
   re-run the clean-container install to prove it.

Step 4 is the acceptance test, and it is the reason to do this at all: until
it passes, "CoolMS is installable" is a statement about one laptop.

⚠️ Note the release set puts the four themes **lockstep** with
`coolms/core` and `coolms/ooxml` **independent**, so they do not get the
same version number. `release-tools/release-set.py --report` computes it;
do not hand-maintain the list.

## Outcome, 2026-09-03

All four are done, and the count of packages `coolms/*` serving at least one
version on Packagist is **16 of 16**.

| package | version |
| --- | --- |
| `coolms/ooxml` | `v1.0.0-alpha1` (independent numbering) |
| `coolms/theme-bootstrap` | `v2.0.0-alpha3` |
| `coolms/theme-default` | `v2.0.0-alpha3` |
| `coolms/theme-admin` | `v2.0.0-alpha3` |

⚠️ **alpha3 rather than alpha2, because alpha2 was broken.** Each theme's
bundle class reaches `Config\Definition\ConfigurableInterface` through
`AbstractBundle`, and `symfony/dependency-injection` carries `symfony/config`
in `require-dev`. None of the three declared it, so all three installed
perfectly and could not load their own bundle. Found by resolving each from
its tag into an empty tree and checking every `use` in its own `src/` against
the result -- before submission, which is why nothing had to be retracted.

`theme-coolms-site` stayed out, as planned.

