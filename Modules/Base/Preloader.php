<?php

/**
 * Preloader for the Base module.
 *
 * This class performs module-specific initialization and permission checks
 * before controller execution. Currently the Base module has no special
 * preloading requirements, but the class is preserved for future expansion.
 *
 * @package   Modules\Base
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Modules\Base;

class Preloader
{
    /**
     * Initialize the preloader.
     *
     * The Base module does not require additional initialization
     * at this stage. This method is intentionally empty but can be
     * extended in the future.
     *
     * @param \Application\Assistance\View\View $view    View object
     * @param \Application\Assistance\Request   $request Request object
     */
    public function __construct($view, $request)
    {
    }
}