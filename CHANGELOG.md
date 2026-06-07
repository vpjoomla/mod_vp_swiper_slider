# Changelog

All notable changes to **VP Swiper Slider (Free)** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [3.0.0] — 2026-06-07

A full rewrite on the latest Swiper 12 engine, with a clean split into Free
and Pro editions sharing a single codebase.

### Added
- Rebuilt on Swiper 12.1.4 with custom, tree-shaken bundles for faster page loads.
- Free and Pro editions ship as separate packages with isolated globals — both can coexist on the same page.
- Clean `data-config` architecture: every slider self-contains its options, so multiple sliders per page just work.
- New SVG navigation arrows and refined dynamic-bullet pagination.
- Full RTL and multilingual support; English and Ukrainian language packs included.
- Joomla update server registered in the manifest for automatic update notifications.

### Changed
- Reorganised the configuration UI: Joomla 5/6 form layouts, switcher toggles, per-effect option groups.
- Modernised PHP namespace to `Joomla\Module\VpSwiperSlider` with PSR-4 autoloading and a service provider.
- Asset loading moved to WebAssetManager — CSP-friendly, no inline scripts.
- Minimum requirements bumped to Joomla 5.0+ / 6.1+ and PHP 8.2+.

### Removed
- jQuery dependency — the module is now zero-dependency vanilla JS.

---

[3.0.0]: https://github.com/vpjoomla/mod_vp_swiper_slider/releases/tag/v3.0.0
