<?php

/**
 * Bootstrap class for the Base module.
 *
 * This module provides core system functionality including user management,
 * authentication, queues, logging, and base data structures.
 * It is a required module for all projects.
 *
 * @package   Modules\Base
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Modules\Base;

class Bootstrap extends \Application\Bootstrap
{
    /**
     * Module name.
     *
     * @var string
     */
    public static $_module = \Application\Crud::MODULE_BASE;

    /**
     * Guest access flag.
     *
     * Base module requires authentication; guests are not allowed.
     *
     * @var bool
     */
    public static $_allow_guest = false;
}