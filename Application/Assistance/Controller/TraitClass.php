<?php

/**
 * Controller trait with common functionality.
 *
 * This trait provides utility methods used by multiple controller types
 * (Web, API, CLI, Restful). It includes:
 * - File upload handling
 * - User activity logging with change tracking
 * - User preference synchronization (language, voice)
 * - Path resolution utilities
 *
 * The trait is applied to:
 * - Controller (web controllers)
 * - ApiController (JSON APIs)
 * - CliController (command-line)
 * - RestfulController (REST APIs)
 *
 * @package   Application\Assistance\Controller
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Controller;

use \Modules\Base\Models\DbTables as dbB;
use Application\Assistance\Controller\Controller as CC;
use \Application\Assistance\Database as db;

trait TraitClass
{
    /**
     * Check if the current controller is a RESTful endpoint.
     *
     * Override in child controllers to enable REST-specific behavior.
     *
     * @return bool False by default (overridden in RestfulController)
     */
    public function isRestFul()
    {
        return false;
    }

    /**
     * Get the base destination directory for file storage.
     *
     * Returns the absolute path to the project root.
     * Used for file uploads and storage operations.
     *
     * @return string Absolute path to the destination directory
     */
    public function getDesctination()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }

    /**
     * Previous state of the entity before modification.
     * Used for change tracking and logging.
     *
     * @var array
     */
    public $_before;

    /**
     * Log user activity with change tracking.
     *
     * Compares the before and after states of an entity to determine
     * what changes were made. This provides an audit trail for
     * user actions.
     *
     * The method:
     * 1. Retrieves the previous state from $_before
     * 2. Compares with the current state ($object)
     * 3. Builds a human-readable change description
     * 4. Saves the log entry via the Person helper
     *
     * @param \Application\Assistance\View\View|\Application\Assistance\View\ApiView $view      View object for context
     * @param \Application\Assistance\Request                                      $request   Request object
     * @param int                                                                 $module    Module code (for logging)
     * @param int                                                                 $controller Controller code (for logging)
     * @param array|\Application\Assistance\Model|bool                            $object    Current state of the entity
     */
    public function setPersonLog($view, $request, $module, $controller, $object)
    {
        $class = false;

        /**
         * Convert Model object to array if needed.
         * Extract the database class for field name resolution.
         */
        if (!is_array($object)) {
            $class = $object->_db;
            /** @var false|db $class */
            $class::setFieldsToLog();
            $object = $object->toArray();
        }

        /**
         * Compare before and after states to detect changes.
         * Build a list of fields that were modified.
         */
        $change = [];
        foreach ($this->_before as $field => $value) {
            if ($value != $object[$field]) {
                $change[$field] = [$value, $object[$field]];
            }
        }

        /**
         * Format each change as: FieldName: "old" => "new"
         * Use the field's friendly name if available.
         */
        if (dfCount($change) > 0) {
            foreach ($change as $k => &$v) {
                $v = (false === $class ? $k : $class::getFieldToLog($k))
                    . ': "' . $v[0] . '" => "' . $v[1] . '"';
            }
        }

        /**
         * Save the log entry via the Person helper.
         * The helper handles the actual database insertion.
         */
        $helper = PERSON_HELPER;
        /** @var \Application\Helpers\Person\InterfacePerson $helper */
        $helper::saveLog(
            $view,
            $request,
            $module,
            $controller,
            implode("; ", $change)
        );
    }

    /**
     * Synchronize user preferences from cookies to the database.
     *
     * Updates the user's language and voice preferences when they have
     * been changed via the frontend (cookie-based selection).
     *
     * This ensures consistency between frontend preferences and
     * the user's stored profile data.
     *
     * @param \Modules\Base\Models\Person          $person    User model (core data)
     * @param \Modules\Base\Models\PersonProtected $protected User protected data model (voice preferences)
     */
    public function checkPersonUpdates(&$person, &$protected)
    {
        /**
         * Synchronize language preference.
         * Updates the user's language if the cookie has changed.
         */
        if (isset($_COOKIE[CC::FREE_LANGUAGE_COOKIE])
            && $person->getLanguageId() != $_COOKIE[CC::FREE_LANGUAGE_COOKIE]) {
            $person->setLanguageId($_COOKIE[CC::FREE_LANGUAGE_COOKIE])->save();
        }

        /**
         * Synchronize voice preference.
         * Updates the user's voice setting if the cookie has changed.
         */
        if (isset($_COOKIE[CC::FREE_VOICE_ENABLE_COOKIE])
            && $protected->getData(dbB\PersonProtected::VOICE) != $_COOKIE[CC::FREE_VOICE_ENABLE_COOKIE]) {
            $protected->setProtected(dbB\PersonProtected::VOICE, $_COOKIE[CC::FREE_VOICE_ENABLE_COOKIE])->save();
        }
    }
}