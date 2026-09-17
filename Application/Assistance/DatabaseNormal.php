<?php

/**
 * Standard database class without soft delete or project filtering.
 *
 * This class extends the base Database class without adding any
 * additional filtering or automatic fields. It represents a plain
 * database table with no special behavior.
 *
 * Use this for:
 * - Lookup/reference tables (static data)
 * - Log tables that should never be deleted
 * - Tables where soft delete is not needed
 * - System tables that are not project-specific
 *
 * For tables that need:
 * - Soft delete: use DatabaseExtend
 * - Project isolation: use DatabaseNormalProject
 * - Both: use DatabaseExtendProject
 * - Status filtering: use DatabaseNormalStatus or DatabaseExtendStatus
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

class DatabaseNormal extends Database
{
    /**
     * Current class name for model resolution.
     *
     * @var string
     */
    public static $_class = '';
}