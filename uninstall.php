<?php
/**
 * Uninstall handler for PVTL Site Search.
 *
 * Runs when the plugin is deleted from the WordPress admin.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// No options or custom tables are registered by this plugin.
