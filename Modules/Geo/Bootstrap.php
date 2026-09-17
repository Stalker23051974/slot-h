<?php

namespace Modules\Geo;

/**
 * Bootstrap class for the Geo module.
 *
 * This module handles geographic data including countries, cities,
 * states, languages, currencies, and timezones.
 *
 * @package   Modules\Geo
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Bootstrap extends \Application\Bootstrap
{
    /**
     * Module name.
     *
     * @var string
     */
    public static $_module = \Application\Crud::MODULE_GEO;

    /**
     * Guest access flag.
     *
     * Geo module allows unauthenticated access for public data.
     *
     * @var bool
     */
    public static $_allow_guest = true;
}