# Changelog

## v6.0.2 - 2026-09-23
### Security
* **security:** declare the tool key on this module's controllers (audit item 7.0)

## v6.0.1 - 2026-08-11
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
* Fix audit findings
### Added
* **readme:** add module README (wizard, generated output, services, events)
### Fixed
* **security:** harden legacy file/dir creation & output escaping
### Dependencies & build
* **composer:** melis-core/melis-cms ^6.0, php ^8.3|^8.5, laminas keyword, docs & authors

## v5.3.0 - 2024-09-25
### Changed
* Save item result
* Attr update
* Service
* Parent service
* Related to jquery migration
* Tool creator caching service
* Table list translations for lang columns
* Check mobile responsive
### Dependencies & build
* Rebuilt the asset bundle
* Bundle

## v5.2.2 - 2024-08-14
### Changed
* Module name pascal case
* Clearing zone cache after module activation

## v5.2.1 - 2024-07-23
### Changed
* MelisEngine cache service removed and recreate

## v5.2.0 - 2024-06-06
* Maintenance release.

## v5.1.0 - 2024-02-13
### Changed
* Update cache config structure

## v5.0.1 - 2022-11-28
### Changed
* Tool input translations

## v5.0.0 - 2022-06-22
### Fixed
* Fix ticket
### Changed
* Set field as not required if it has default value and not nullable
* Changed deprecated ArraySerializable to ArraySerializableHydrator and set time limit to zero in getting the db tables
