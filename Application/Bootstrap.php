<?php

/**
 * Abstract module bootstrap class.
 *
 * Each module must define a Bootstrap class that extends this base.
 * The Bootstrap class serves as the module's metadata container and
 * determines its accessibility characteristics.
 *
 * This is used during the routing process to determine whether
 * unauthenticated users can access the module.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

class Bootstrap
{
    /**
     * Module name.
     *
     * Should be set in child classes to match the module's
     * directory name. Used for identification and routing.
     *
     * @var string
     */
    public static $_module;

    /**
     * Guest access flag.
     *
     * When set to true, the module is accessible to
     * unauthenticated (guest) users.
     *
     * When false, users must be logged in to access
     * any controller within this module.
     *
     * Module-specific Bootstrap classes override this
     * to define their access policy:
     * - Free::Bootstrap: true (public pages)
     * - Main::Bootstrap: false (admin only)
     * - Base::Bootstrap: false (requires auth)
     *
     * @var bool
     */
    public static $_allow_guest = false;
}