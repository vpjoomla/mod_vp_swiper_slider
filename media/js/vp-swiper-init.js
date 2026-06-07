/**
 * VP Swiper Slider — initializer
 *
 * @package    mod_vp_swiper_slider
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 *
 * One script for every slider on the page. Each .vp-swiper element carries
 * its own configuration in a data-vp-config JSON attribute, so multiple
 * modules coexist without colliding. No inline scripts, no global state.
 */
(function () {
    'use strict';

    // The Swiper global this build talks to. The package builder rewrites the
    // token below so the Free and Pro editions use DISTINCT globals
    // (VPSwiperFree / VPSwiperPro) and never overwrite each other when both
    // modules appear on the same page. The standalone core defaults to Swiper.
    var SWIPER_GLOBAL = 'VPSwiperFree';

    function getSwiper() {
        return window[SWIPER_GLOBAL] || window.Swiper;
    }

    /**
     * Build the Swiper options object from a slider element's dataset.
     *
     * @param {HTMLElement} el The .vp-swiper container.
     * @return {Object} Swiper options.
     */
    function buildOptions(el) {
        var cfg = {};

        try {
            cfg = JSON.parse(el.getAttribute('data-vp-config') || '{}');
        } catch (e) {
            // Malformed config: fall back to a harmless default rather than
            // throwing and blocking every other slider on the page.
            if (window.console && window.console.warn) {
                window.console.warn('[VP Swiper] Invalid config on', el, e);
            }
            cfg = {};
        }

        var uid = el.getAttribute('data-vp-id') || '';
        var options = {};

        // ---- Base options -------------------------------------------------
        options.slidesPerView = cfg.slidesPerView != null ? cfg.slidesPerView : 1;
        options.spaceBetween  = cfg.spaceBetween  != null ? cfg.spaceBetween  : 0;
        options.loop          = !!cfg.loop;
        options.grabCursor    = !!cfg.grabCursor;
        options.speed         = cfg.speed != null ? cfg.speed : 300;

        // Pass the requested effect straight through. If a build doesn't
        // include the effect's module (e.g. the Free build has no cube),
        // Swiper itself falls back to a plain slide — no detection needed.
        options.effect = cfg.effect || 'slide';

        if (cfg.rewind) {
            options.rewind = true;
        }

        if (cfg.loopPreventsSliding != null) {
            options.loopPreventsSliding = !!cfg.loopPreventsSliding;
        }

        if (cfg.autoHeight) {
            options.autoHeight = true;
        }

        // ---- Navigation ---------------------------------------------------
        if (cfg.navigation) {
            options.navigation = {
                nextEl: '.vp-swiper-next-' + uid,
                prevEl: '.vp-swiper-prev-' + uid
            };
        }

        // ---- Pagination ---------------------------------------------------
        if (cfg.pagination) {
            options.pagination = {
                el: '.vp-swiper-pagination-' + uid,
                clickable: true,
                type: cfg.paginationType || 'bullets',
                dynamicBullets: cfg.paginationType === 'bullets'
            };
        }

        // ---- Autoplay -----------------------------------------------------
        if (cfg.autoplay) {
            options.autoplay = {
                delay: cfg.autoplayDelay != null ? cfg.autoplayDelay : 5000,
                disableOnInteraction: !!cfg.autoplayDisableOnInteraction,
                pauseOnMouseEnter: !!cfg.autoplayPauseOnMouseEnter
            };
        }

        // ---- Effect-specific params --------------------------------------
        if (cfg.effect === 'fade') {
            options.fadeEffect = { crossFade: true };
        }

        if (cfg.effect === 'cube' && cfg.cubeEffect) {
            options.cubeEffect = cfg.cubeEffect;
        }

        if (cfg.effect === 'coverflow' && cfg.coverflowEffect) {
            options.coverflowEffect = cfg.coverflowEffect;
        }

        if (cfg.effect === 'flip' && cfg.flipEffect) {
            options.flipEffect = cfg.flipEffect;
        }

        if (cfg.effect === 'creative' && cfg.creativeEffect) {
            options.creativeEffect = cfg.creativeEffect;
        }

        // ---- Grid (multiple rows) ----------------------------------------
        if (cfg.grid && cfg.grid.rows > 1) {
            options.grid = { rows: cfg.grid.rows, fill: cfg.grid.fill || 'row' };
        }

        // ---- Advanced (Pro) ----------------------------------------------
        if (cfg.keyboard) {
            options.keyboard = { enabled: true };
        }

        if (cfg.mousewheel) {
            options.mousewheel = { invert: false };
        }

        if (cfg.zoom) {
            options.zoom = true;
        }

        if (cfg.parallax) {
            options.parallax = true;
        }

        if (cfg.hashNavigation) {
            options.hashNavigation = { watchState: true };
        }

        if (cfg.history && cfg.history.key) {
            options.history = { key: cfg.history.key };
        }

        // ---- Thumbnails (Pro) --------------------------------------------
        // The Swiper instance for the thumbnail strip is attached separately
        // in initAll() after the strip itself is initialized, because Swiper
        // needs a *reference* to the thumbs Swiper, not a CSS selector.
        // Nothing to do here.

        // ---- Responsive breakpoints --------------------------------------
        if (cfg.breakpoints && typeof cfg.breakpoints === 'object') {
            options.breakpoints = cfg.breakpoints;
        }

        return options;
    }

    /**
     * Initialize the thumbnail strip Swiper for a main slider.
     *
     * @param {Object}      Swiper  The Swiper constructor (edition-specific).
     * @param {HTMLElement} mainEl  The main .vp-swiper element.
     * @param {Object}      cfg     The parsed data-vp-config of the main slider.
     * @return {Object|null} The thumbs Swiper instance, or null on failure.
     */
    function initThumbs(Swiper, mainEl, cfg) {
        if (!cfg.thumbs || !cfg.thumbs.el) {
            return null;
        }

        var thumbsEl = document.querySelector(cfg.thumbs.el);

        if (!thumbsEl) {
            return null;
        }

        // Keep the thumbs strip out of initAll's main loop.
        thumbsEl.classList.add('vp-swiper-ready');

        // eslint-disable-next-line no-new
        return new Swiper(thumbsEl, {
            slidesPerView:    cfg.thumbs.perView || 5,
            spaceBetween:     8,
            watchSlidesProgress: true,
            slideToClickedSlide: cfg.thumbs.clickEnabled !== false,
            freeMode:         false
        });
    }

    /**
     * Initialize every slider that hasn't been initialized yet.
     */
    function initAll() {
        var Swiper = getSwiper();

        if (typeof Swiper === 'undefined' || !Swiper) {
            if (window.console && window.console.warn) {
                window.console.warn('[VP Swiper] Swiper library not loaded.');
            }
            return;
        }

        // Only main containers: thumbs containers (.vp-swiper-thumbs) and the
        // main container both have .swiper, but only the main has .vp-swiper.
        var nodes = document.querySelectorAll('.vp-swiper:not(.vp-swiper-ready):not(.vp-swiper-thumbs)');

        Array.prototype.forEach.call(nodes, function (el) {
            // Only claim sliders that belong to this edition. The builder
            // stamps each slider with data-vp-global matching its bundle, so
            // a Free init never grabs a Pro slider (which needs Pro effects)
            // and vice versa. If the marker is absent or still the raw token
            // (standalone/demo use), claim it.
            var marker = el.getAttribute('data-vp-global');
            var mine = !marker
                || marker === SWIPER_GLOBAL
                || marker.indexOf('__VP_SWIPER') === 0
                || SWIPER_GLOBAL.indexOf('__VP_SWIPER') === 0;

            if (!mine) {
                return;
            }

            el.classList.add('vp-swiper-ready');

            // Tag the outer wrapper if the slider contains article cards, so
            // theme CSS can reposition nav arrows from the geometric centre
            // (which lands on body text in tall cards) to image level.
            if (el.querySelector('.vp-article-slide')) {
                var outer = el.closest('.vp-swiper-wrapper-outer');
                if (outer) {
                    outer.classList.add('vp-has-article-cards');
                }
            }

            var options = buildOptions(el);
            var cfg = {};
            try {
                cfg = JSON.parse(el.getAttribute('data-vp-config') || '{}');
            } catch (e) { /* already warned in buildOptions */ }

            // Two-stage init when thumbs are enabled: thumbs first so we have
            // an instance to hand to the main slider's options.
            if (cfg.thumbs) {
                var thumbsInstance = initThumbs(Swiper, el, cfg);
                if (thumbsInstance) {
                    options.thumbs = { swiper: thumbsInstance };
                }
            }

            // eslint-disable-next-line no-new
            new Swiper(el, options);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Expose for manual re-init (e.g. after AJAX content injection). The
    // builder suffixes these per edition so two inits never clash.
    window.vpSwiperInit = window.vpSwiperInit || initAll;
    window.vpBuildOptions = window.vpBuildOptions || buildOptions;
})();
