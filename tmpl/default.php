<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Layout variables provided by the dispatcher:
 *
 * @var   \Joomla\Registry\Registry                                    $params  Module parameters.
 * @var   \stdClass                                                    $module  Module object.
 * @var   \Joomla\Module\VpSwiperSlider\Site\Helper\VpSwiperSliderHelper $helper  Module helper.
 * @var   \Joomla\CMS\Application\SiteApplication                      $app     Application.
 */

$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('mod_vp_swiper_slider.lib', 'mod_vp_swiper_slider/vp-swiper.min.js', [], ['defer' => true]);
$wa->registerAndUseScript('mod_vp_swiper_slider.init', 'mod_vp_swiper_slider/vp-swiper-init.js', [], ['defer' => true]);
$wa->registerAndUseStyle('mod_vp_swiper_slider.lib', 'mod_vp_swiper_slider/vp-swiper.min.css');
$wa->registerAndUseStyle('mod_vp_swiper_slider.theme', 'mod_vp_swiper_slider/vp-swiper-theme.css');

$uid       = (int) $module->id;
$moduleSfx = htmlspecialchars(trim($params->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8');

// ---------------------------------------------------------------------------
// Resolve the content source.
// ---------------------------------------------------------------------------
$sourceType = (int) $params->get('source_type', 0);
$ordering   = $params->get('ordering', 'desc');
$items      = [];
$customCode = '';
$categoryUrl = '';

switch ($sourceType) {
    case 0: // Custom HTML
        $customCode = (string) $params->get('custom_code', '');
        break;

    case 1: // Vendors (Pro)
        $items = (array) $params->get('vendors', []);
        break;

    case 2: // Slider (image + text + button)
        $items = (array) $params->get('slider', []);
        break;

    case 3: // Articles (Pro)
        $catid = $params->get('catid');
        $count = (int) $params->get('count', 3);
        $items = $helper::getArticles($catid, $count, $ordering);

        if (!empty($catid)) {
            $firstCat    = (int) (is_array($catid) ? reset($catid) : $catid);
            $categoryUrl = Route::_('index.php?option=com_content&view=category&id=' . $firstCat);
        }
        break;

    case 4: // Image
        $items = (array) $params->get('roty', []);
        break;
}

$hasContent = ($sourceType === 0 && $customCode !== '') || (!empty($items));

if (!$hasContent) {
    return;
}

// ---------------------------------------------------------------------------
// Build the Swiper configuration handed to the JS initializer as JSON.
// Only options relevant to the current setup are emitted.
// ---------------------------------------------------------------------------
$effect = $params->get('slider_effects', 'slide');

$config = [
    'slidesPerView'                 => (int) $params->get('slides_per_view', 3),
    'spaceBetween'                  => (int) $params->get('space_between', 20),
    'loop'                          => (bool) $params->get('loop', 0),
    'loopPreventsSliding'           => (bool) $params->get('loop_prevent_slide', 0),
    'grabCursor'                    => (bool) $params->get('grab_cursor', 1),
    'effect'                        => $effect,
    'speed'                         => (int) $params->get('speed', 300),
    'navigation'                    => (bool) $params->get('navigation', 1),
    'pagination'                    => (bool) $params->get('pagination', 1),
    'paginationType'                => $params->get('type_pagination', 'bullets'),
    'autoplay'                      => (bool) $params->get('autoplay', 1),
    'autoplayDelay'                 => (int) $params->get('autoplay_delay', 5000),
    'autoplayDisableOnInteraction'  => (bool) $params->get('autoplay_disable_interaction', 0),
    'autoplayPauseOnMouseEnter'     => (bool) $params->get('autoplay_pause_mouseenter', 0),
    'autoHeight'                    => (bool) $params->get('autoheight', 0),
    'rewind'                        => (bool) $params->get('rewind', 0),
];

// Cube effect params.
if ($effect === 'cube') {
    $config['cubeEffect'] = [
        'shadow'       => (bool) $params->get('cube_shadow', 1),
        'slideShadows' => (bool) $params->get('cube_slide_shadows', 1),
        'shadowOffset' => (int) $params->get('cube_shadow_offset', 20),
        'shadowScale'  => (float) $params->get('cube_shadow_scale', 0.94),
    ];
}

// Coverflow effect params.
if ($effect === 'coverflow') {
    $config['coverflowEffect'] = [
        'rotate'       => (int) $params->get('coverflow_rotate', 50),
        'stretch'      => (int) $params->get('coverflow_stretch', 0),
        'depth'        => (int) $params->get('coverflow_depth', 100),
        'modifier'     => (int) $params->get('coverflow_modifier', 1),
        'slideShadows' => (bool) $params->get('coverflow_slide_shadows', 1),
    ];
}

// Grid (multiple rows).
if ((int) $params->get('grid', 0) === 1) {
    $config['grid'] = [
        'rows' => (int) $params->get('grid_rows', 2),
        'fill' => 'row',
    ];
}

// Advanced controls (present only when the corresponding param exists / is on).
if ((int) $params->get('keyboard_control', 0) === 1) {
    $config['keyboard'] = true;
}

if ((int) $params->get('mousewheel_control', 0) === 1) {
    $config['mousewheel'] = true;
}

if ((int) $params->get('zoom', 0) === 1) {
    $config['zoom'] = true;
}

if ((int) $params->get('parallax', 0) === 1) {
    $config['parallax'] = true;
}

if ((int) $params->get('hash_navigation', 0) === 1) {
    $config['hashNavigation'] = true;
}

if ((int) $params->get('history', 0) === 1) {
    $config['history'] = ['key' => 'slide-' . $uid];
}

// Responsive breakpoints.
$breakpoints = $params->get('breakpoints', []);

if (!empty($breakpoints)) {
    $bpOut = [];

    foreach ((array) $breakpoints as $point) {
        if (empty($point->bp_size)) {
            continue;
        }

        $bpOut[(int) $point->bp_size] = [
            'slidesPerView' => (int) ($point->bp_perview ?? 1),
            'spaceBetween'  => (int) ($point->bp_space ?? 0),
        ];
    }

    if (!empty($bpOut)) {
        $config['breakpoints'] = $bpOut;
    }
}

$rtl       = (int) $params->get('rtl_mode', 0) === 1;
$configJson = htmlspecialchars(
    json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    ENT_QUOTES,
    'UTF-8'
);

$wrapperClass = 'vp-swiper-wrapper-outer';

if ($moduleSfx !== '') {
    $wrapperClass .= ' ' . $moduleSfx;
}

$imageRow = $sourceType === 4 ? ' vp-swiper-image-row' : '';
?>
<div class="<?php echo $wrapperClass; ?>">
    <div class="swiper vp-swiper"
         data-vp-id="<?php echo $uid; ?>"
         data-vp-global="VPSwiperFree"
         data-vp-config="<?php echo $configJson; ?>"
        <?php echo $rtl ? ' dir="rtl"' : ''; ?>>
        <div class="swiper-wrapper<?php echo $imageRow; ?>">
            <?php
            switch ($sourceType) {
                case 0:
                    require __DIR__ . '/custom.php';
                    break;
                case 1:
                    require __DIR__ . '/vendors.php';
                    break;
                case 2:
                    require __DIR__ . '/slider.php';
                    break;
                case 3:
                    require __DIR__ . '/articles.php';
                    break;
                case 4:
                    require __DIR__ . '/image.php';
                    break;
            }
            ?>
        </div>

        <?php if ((bool) $params->get('pagination', 1)) : ?>
            <div class="swiper-pagination vp-swiper-pagination-<?php echo $uid; ?>"></div>
        <?php endif; ?>

        <?php if ((bool) $params->get('navigation', 1)) : ?>
            <div class="swiper-button-prev vp-swiper-prev-<?php echo $uid; ?>"></div>
            <div class="swiper-button-next vp-swiper-next-<?php echo $uid; ?>"></div>
        <?php endif; ?>
    </div>

    <?php if ($sourceType === 3 && $categoryUrl !== '') : ?>
        <a class="vp-swiper-all-news" href="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo Text::_('MOD_VP_SWIPER_SLIDER_VIEW_ALL_ARTICLES'); ?>
        </a>
    <?php endif; ?>
</div>
