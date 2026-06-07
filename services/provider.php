<?php

/**
 * @package    mod_vp_swiper_slider
 *
 * @author     Volodymyr Pershyn <support@vpjoomla.com>
 * @copyright  Copyright (C) 2024 - 2026 VPJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://vpjoomla.com
 */

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * The VP Swiper Slider module service provider.
 *
 * @since  3.0.0
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   3.0.0
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(
            new ModuleDispatcherFactory('\\Joomla\\Module\\VpSwiperSlider')
        );
        $container->registerServiceProvider(
            new HelperFactory('\\Joomla\\Module\\VpSwiperSlider\\Site\\Helper')
        );
        $container->registerServiceProvider(new Module());
    }
};
