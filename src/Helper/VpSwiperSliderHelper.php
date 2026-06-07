<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 */

namespace Joomla\Module\VpSwiperSlider\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_vp_swiper_slider.
 *
 * @since  3.0.0
 */
class VpSwiperSliderHelper
{
    /**
     * Retrieve published articles from the given categories.
     *
     * @param   mixed   $catid     A category id or an array of category ids.
     * @param   integer $count     Maximum number of articles to return.
     * @param   string  $ordering  'asc' or 'desc' by publish_up date.
     *
     * @return  array  List of article objects with an added `intro_image` property.
     *
     * @since   3.0.0
     */
    public static function getArticles($catid = null, int $count = 10, string $ordering = 'desc'): array
    {
        /** @var DatabaseInterface $db */
        $db  = Factory::getContainer()->get(DatabaseInterface::class);
        $app = Factory::getApplication();

        // Current date/time in the site timezone, for publish_up comparison.
        $timezone    = new \DateTimeZone($app->get('offset', 'UTC'));
        $now         = new \DateTime('now', $timezone);
        $nowFormatted = $now->format('Y-m-d H:i:s');

        $direction = strtolower($ordering) === 'asc' ? 'ASC' : 'DESC';

        $query = $db->getQuery(true);

        $query->select(
            $db->quoteName(
                [
                    'a.id',
                    'a.catid',
                    'a.title',
                    'a.introtext',
                    'a.images',
                    'a.fulltext',
                    'a.publish_up',
                ]
            )
        )
            ->select($db->quoteName('a.title', 'article_title'))
            ->select($db->quoteName('c.title', 'category_title'))
            ->from($db->quoteName('#__content', 'a'))
            ->join(
                'LEFT',
                $db->quoteName('#__categories', 'c')
                . ' ON ' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('c.id')
            )
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('a.publish_up') . ' <= :now')
            ->bind(':now', $nowFormatted);

        // Category filter — supports a single id or an array.
        if ($catid !== null && $catid !== '') {
            $ids = ArrayHelper::toInteger((array) $catid);
            $ids = array_values(array_filter($ids));

            if (!empty($ids)) {
                $query->whereIn($db->quoteName('a.catid'), $ids);
            }
        }

        $query->order($db->quoteName('a.publish_up') . ' ' . $direction);

        $db->setQuery($query, 0, max(1, $count));

        $results = $db->loadObjectList() ?: [];

        // Decode the intro image out of the JSON images field.
        foreach ($results as $result) {
            $result->intro_image = '';

            if (!empty($result->images)) {
                $images = json_decode($result->images);

                if (isset($images->image_intro) && $images->image_intro !== '') {
                    $result->intro_image = $images->image_intro;
                }
            }
        }

        return $results;
    }
}
