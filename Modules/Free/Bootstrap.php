<?php

namespace Modules\Free;

/**
 * Bootstrap class for the Free module.
 *
 * This module handles public-facing pages that do not require
 * authentication, such as landing pages, about us, privacy policy,
 * and terms of use.
 *
 * @package   Modules\Free
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
    public static $_module = \Application\Crud::MODULE_FREE;

    /**
     * Guest access flag.
     *
     * Free module allows unauthenticated access to all its pages.
     *
     * @var bool
     */
    public static $_allow_guest = true;
}