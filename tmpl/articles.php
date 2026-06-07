<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 *
 * @var   array                          $items   Article objects from the helper.
 * @var   \Joomla\Registry\Registry      $params  Module parameters.
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$showImage    = (bool) $params->get('article_image', 1);
$showTitle    = (bool) $params->get('article_title', 1);
$showIntro    = (bool) $params->get('article_introtext', 1);
$showReadmore = (bool) $params->get('readmore', 0);

foreach ($items as $article) :
    $url = Route::_(
        'index.php?option=com_content&view=article&id=' . (int) $article->id
        . '&catid=' . (int) $article->catid
    );

    $src = '';

    if ($showImage && !empty($article->intro_image)) {
        $cleanImage = HTMLHelper::cleanImageURL($article->intro_image);
        $src        = $cleanImage->url;
    }
    ?>
    <div class="swiper-slide">
        <a class="vp-article-slide" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($src !== '') : ?>
                <div class="vp-article-image">
                    <img src="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo htmlspecialchars((string) $article->article_title, ENT_QUOTES, 'UTF-8'); ?>"
                         loading="lazy" />
                </div>
            <?php endif; ?>

            <?php if ($showTitle) : ?>
                <div class="vp-article-title">
                    <?php echo htmlspecialchars((string) $article->article_title, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($showIntro) : ?>
                <div class="vp-article-text">
                    <?php echo HTMLHelper::_('content.prepare', $article->introtext); ?>
                </div>
            <?php endif; ?>

            <?php if ($showReadmore) : ?>
                <span class="vp-article-readmore">
                    <?php echo Text::_('MOD_VP_SWIPER_SLIDER_READMORE_LINK'); ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
<?php endforeach; ?>
