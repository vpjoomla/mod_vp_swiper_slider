<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 *
 * @var   array  $items  Slide entries.
 */

use Joomla\CMS\HTML\HTMLHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

foreach ($items as $item) :
    $image  = isset($item->slider_image) ? trim((string) $item->slider_image) : '';
    $title  = isset($item->slider_title) ? (string) $item->slider_title : '';
    $text   = isset($item->slider_text) ? (string) $item->slider_text : '';
    $button = isset($item->slider_button) ? (string) $item->slider_button : '';
    $link   = isset($item->slider_link) ? trim((string) $item->slider_link) : '';

    $src = '';

    if ($image !== '') {
        $cleanImage = HTMLHelper::cleanImageURL($image);
        $src        = $cleanImage->url;
    }

    $hasLink = $link !== '';
    $tag     = $hasLink ? 'a' : 'div';
    ?>
    <div class="swiper-slide">
        <<?php echo $tag; ?> class="vp-slide"<?php echo $hasLink ? ' href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
            <?php if ($src !== '') : ?>
                <div class="vp-slide-image">
                    <img src="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                         loading="lazy" />
                    <span class="vp-slide-overlay"></span>
                </div>
            <?php endif; ?>

            <?php if ($title !== '' || $text !== '' || $button !== '') : ?>
                <div class="vp-slide-content">
                    <?php if ($title !== '') : ?>
                        <h3 class="vp-slide-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php endif; ?>

                    <?php if ($text !== '') : ?>
                        <p class="vp-slide-text"><?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>

                    <?php if ($button !== '') : ?>
                        <span class="vp-slide-button"><?php echo htmlspecialchars($button, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </<?php echo $tag; ?>>
    </div>
<?php endforeach; ?>
