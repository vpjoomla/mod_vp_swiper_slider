<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 *
 * @var   array  $items  Vendor entries.
 */

use Joomla\CMS\HTML\HTMLHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

foreach ($items as $item) :
    $image  = isset($item->vendor_image) ? trim((string) $item->vendor_image) : '';
    $alt    = isset($item->vendor_alt) ? (string) $item->vendor_alt : '';
    $width  = isset($item->vendor_image_width) ? (string) $item->vendor_image_width : '';
    $height = isset($item->vendor_image_height) ? (string) $item->vendor_image_height : '';

    if ($image === '') {
        continue;
    }

    // Clean Joomla media field value (may include "#joomlaImage://..." metadata).
    $cleanImage = HTMLHelper::cleanImageURL($image);
    $src        = $cleanImage->url;
    ?>
    <div class="swiper-slide">
        <div class="vp-vendor-slide">
            <div class="vp-vendor-image">
                <img src="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>"
                    <?php echo $width !== '' ? ' width="' . (int) $width . '"' : ''; ?>
                    <?php echo $height !== '' ? ' height="' . (int) $height . '"' : ''; ?>
                     loading="lazy" />
            </div>
        </div>
    </div>
<?php endforeach; ?>
