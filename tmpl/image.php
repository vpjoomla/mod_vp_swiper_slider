<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 *
 * @var   array  $items  Image entries.
 */

use Joomla\CMS\HTML\HTMLHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

foreach ($items as $item) :
    $image = isset($item->image_image) ? trim((string) $item->image_image) : '';
    $link  = isset($item->image_link) ? trim((string) $item->image_link) : '';
    $title = isset($item->image_title) ? (string) $item->image_title : '';
    $text  = isset($item->image_text) ? (string) $item->image_text : '';

    if ($image === '') {
        continue;
    }

    $cleanImage = HTMLHelper::cleanImageURL($image);
    $src        = $cleanImage->url;

    $hasLink = $link !== '';
    $tag     = $hasLink ? 'a' : 'div';
    ?>
    <div class="swiper-slide vp-image-block">
        <<?php echo $tag; ?> class="vp-image-content"<?php echo $hasLink ? ' href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
            <img src="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                 loading="lazy" />

            <?php if ($title !== '' || $text !== '') : ?>
                <div class="vp-image-block-text">
                    <?php if ($title !== '') : ?>
                        <h4><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <?php endif; ?>

                    <?php if ($text !== '') : ?>
                        <div class="vp-image-block-desc">
                            <?php echo HTMLHelper::_('content.prepare', $text); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </<?php echo $tag; ?>>
    </div>
<?php endforeach; ?>
