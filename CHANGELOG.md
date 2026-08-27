# Changelog

All notable changes to this project will be documented in this file.

## [1.8.0](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.8.0) (2026-08-27)

### Features

- **exporters:** add the Markdown exporter, so `--format=markdown` and `Atlas::export($type, 'markdown')` work as documented (#63)
- **mappers:** honour the documented `scan()` options — every `include_*` option now filters the scanned data instead of being ignored (#62)
- **mappers:** report model observers and factories (`include_observers`, `include_factories`)
- **mappers:** add `include_trait_methods` for jobs, to drop the methods inherited from `Dispatchable`, `InteractsWithQueue`, `Queueable` and `SerializesModels`
- **console:** `atlas:export --format=markdown` writes a `.md` file by default

### Bug Fixes

- **package:** remove the stray `src/Mappers/RuleMapper.php.backup` shipped in the dist tree, and add a `.gitattributes` with `export-ignore` rules (#62)

## [1.6.0](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.6.0) (2026-01-09)
## [1.5.0](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.5.0) (2026-01-08)

### Features

- **events:** add listener flow tracking and skip abstract events ([f5cbcf0](https://github.com/Grazulex/laravel-atlas/commit/f5cbcf0904a0f7b9a17ff97b5c81824535890b65))

### Bug Fixes

- **listeners:** handle file_get_contents returning false for PHPStan ([5916f3e](https://github.com/Grazulex/laravel-atlas/commit/5916f3e8729dbccd8524002dad1a9fb08492120f))
## [1.4.3](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.4.3) (2026-01-08)

### Bug Fixes

- UI improvements - hide empty sections and fix card heights (#43) (#47) ([6be52ad](https://github.com/Grazulex/laravel-atlas/commit/6be52ad93580d58a7d465df6e96a53b243b46d88))
## [1.4.2](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.4.2) (2026-01-08)

### Bug Fixes

- detect event listeners with union types and custom paths (#42) (#45) ([508ca47](https://github.com/Grazulex/laravel-atlas/commit/508ca47cf2320035ca688970c72eab237370e2d3))

### Tests

- add missing Mapper tests and cleanup abandoned files ([df3c7a7](https://github.com/Grazulex/laravel-atlas/commit/df3c7a7892c95ca40fcd0aa557f706f5ca9df951))
## [1.4.0](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.4.0) (2026-01-06)

### Bug Fixes

- skip abstract commands and add event-listener relationship overview ([cd03650](https://github.com/Grazulex/laravel-atlas/commit/cd03650b327b9aa99b1fd97f7c905cd8ce143f87))

### Chores

- remove backlog from tracking and add to gitignore ([e96b25b](https://github.com/Grazulex/laravel-atlas/commit/e96b25b9f852b9d3dc055a2fabe1f9204247a1f4))
## [1.3.1](https://github.com/Grazulex/laravel-atlas/releases/tag/v1.3.1) (2026-01-05)

### Bug Fixes

- resolve segfault in ModelMapper when scanning models with circular relations ([91354bc](https://github.com/Grazulex/laravel-atlas/commit/91354bca79f322e060adfb3180a05ae61bfa66ad))
