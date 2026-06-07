# VP Swiper Slider

A modern, lightweight slider module for **Joomla 5 and Joomla 6**, built on the
latest [Swiper](https://swiperjs.com) 12 engine. Hero banners, image carousels,
logo strips, responsive galleries — one module, no jQuery, no bloat.

This is the **Free** edition. A [Pro edition](https://vpjoomla.com/extensions/ui-frontend/vp-swiper-slider)
adds premium 3D effects, automatic Joomla article and vendor sources, image
zoom, thumbnail navigation and advanced controls — see [Free vs Pro](#free-vs-pro)
below.

[![Joomla 5 & 6](https://img.shields.io/badge/Joomla-5%20%7C%206-orange)](https://www.joomla.org)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg)](LICENSE.txt)
[![Live Demo](https://img.shields.io/badge/Live-Demo-blue)](https://demo.vpjoomla.com/vp-swiper-slider-live-demo)

---

## Features

- **Five content sources** — Custom HTML, Image, Slider (image + text + button),
  Articles (Pro), Vendors (Pro). One module covers most real-world cases.
- **Smooth effects** — slide and fade in Free; cube, coverflow, flip, cards and
  creative in Pro.
- **Responsive breakpoints** — set slides-per-view and spacing per breakpoint;
  the slider adapts cleanly from mobile to desktop.
- **Autoplay, loop, grab cursor** — the usual controls, all configurable from
  the module options.
- **Built on Swiper 12** — the industry-standard slider engine, tree-shaken into
  per-edition bundles so pages stay light.
- **CSP-friendly** — no inline scripts, no jQuery, all assets loaded through
  Joomla's WebAssetManager.
- **RTL and multilingual** — full right-to-left support, English and Ukrainian
  language packs included.
- **Multiple sliders per page** — every instance is self-contained; both Free
  and Pro modules can live on the same page without clashing.

## Requirements

- **Joomla** 5.0+ or **Joomla** 6.1+
- **PHP** 8.2 or higher
- A template that loads Bootstrap Icons (most modern Joomla templates do)
- No third-party libraries required

## Installation

### From a release

1. Download the latest `mod_vp_swiper_slider_v*.zip` from the
   [Releases](https://github.com/vpjoomla/mod_vp_swiper_slider/releases) page.
2. In Joomla admin, go to **System → Install → Extensions**.
3. Upload the zip file.
4. Go to **System → Site Modules → New** and choose **VP Swiper Slider**.

### Automatic updates

The module ships with a Joomla update server registered in its manifest.
After installing v3.0.0+, Joomla checks for new releases automatically.
You'll see updates under **System → Update → Extensions → Find Updates**.

## Quick start

1. Create a new **VP Swiper Slider** module.
2. Pick a **content source** in the *Source* tab:
   - **Custom HTML** — paste any markup; the module wraps each `<div>` as a slide.
   - **Image** — upload images and optional captions through the subform.
   - **Slider** — image + heading + body + button per slide (good for hero banners).
3. Pick an **effect** (Slide or Fade in Free) and tune speed, autoplay, loop.
4. Set **slides per view** and **space between**. For responsive layouts, add
   breakpoints (e.g. `768: 2, 1024: 4`).
5. Assign the module to a position and publish.

Live examples are at **[demo.vpjoomla.com](https://demo.vpjoomla.com/vp-swiper-slider-live-demo)** — 11 working modules covering every effect and source.

## Free vs Pro

| Feature | Free | Pro |
|---|:---:|:---:|
| Custom HTML, Image & Slider sources | ✓ | ✓ |
| Articles source (pulls Joomla content automatically) | — | ✓ |
| Vendors / logo source | — | ✓ |
| Slide & Fade transitions | ✓ | ✓ |
| 3D effects: Cube, Coverflow, Flip, Cards, Creative | — | ✓ |
| Navigation arrows & basic pagination | ✓ | ✓ |
| Advanced pagination (progressbar, custom) | — | ✓ |
| Autoplay, loop & responsive breakpoints | ✓ | ✓ |
| RTL & multilingual support | ✓ | ✓ |
| Image zoom & parallax | — | ✓ |
| Thumbnail navigation | — | ✓ |
| Grid layout (multiple rows) | — | ✓ |
| Keyboard, mousewheel, hash & history | — | ✓ |
| Priority support | — | ✓ |

The Pro edition is **$19** for a single site, one year of updates and priority
support. [Get Pro →](https://vpjoomla.com/extensions/ui-frontend/vp-swiper-slider)

## Project structure

```
mod_vp_swiper_slider/
├── mod_vp_swiper_slider.xml       # Joomla manifest (updateservers, params)
├── services/
│   └── provider.php               # DI container registration
├── src/
│   ├── Dispatcher/
│   │   └── Dispatcher.php         # Routes the module render
│   └── Helper/
│       └── VpSwiperSliderHelper.php
├── tmpl/
│   ├── default.php                # Main layout: collects items, builds config
│   ├── custom.php                 # Custom HTML source
│   ├── image.php                  # Image source
│   └── slider.php                 # Slider source (image + text + button)
├── media/
│   ├── css/
│   │   ├── vp-swiper.min.css      # Swiper 12 core styles
│   │   └── vp-swiper-theme.css    # VPJoomla theme on top of Swiper
│   └── js/
│       ├── vp-swiper.min.js       # Swiper 12 bundle (Free build)
│       └── vp-swiper-init.js      # Reads data-vp-config, boots each slider
└── language/
    ├── en-GB/mod_vp_swiper_slider.ini
    └── ru-RU/mod_vp_swiper_slider.ini
```

### Architecture notes

- **Per-instance config via data attribute.** Each slider's options are
  serialized into JSON on `data-vp-config`. `init.js` reads the attribute and
  initializes Swiper — no inline scripts, multiple sliders per page just work.
- **Edition-scoped global.** Free uses `window.VPSwiperFree`; Pro uses
  `window.VPSwiperPro`. Each `init.js` only claims sliders stamped with its own
  `data-vp-global`. This is why both editions can coexist on the same page.
- **WebAssetManager.** All assets are registered through Joomla's WAM with
  proper dependencies; nothing is dropped via inline `<script>` or `<link>`
  tags.

## Contributing

Bug reports and feature ideas welcome — open an [issue](https://github.com/vpjoomla/mod_vp_swiper_slider/issues).
Pull requests are accepted for the Free edition; please keep changes focused
and add a brief note to `CHANGELOG.md` describing what changed.

For Pro feature requests, mention it in the issue and we'll consider it for
the next release.

## Support

- **Documentation:** [vpjoomla.com/documentation](https://vpjoomla.com/documentation)
- **Issues (Free):** [github.com/vpjoomla/mod_vp_swiper_slider/issues](https://github.com/vpjoomla/mod_vp_swiper_slider/issues)
- **Pro support & sales:** support@vpjoomla.com
- **Author:** [Volodymyr Pershyn](https://vpjoomla.com) / [VPJoomla](https://vpjoomla.com)

## License

GNU General Public License v2.0 or later — see [LICENSE.txt](LICENSE.txt).

Built on top of [Swiper](https://swiperjs.com) by Vladimir Kharlampidi, also
licensed under MIT.

---

<p align="center">
  Made with ☕ in Odesa · <a href="https://vpjoomla.com">vpjoomla.com</a>
</p>
