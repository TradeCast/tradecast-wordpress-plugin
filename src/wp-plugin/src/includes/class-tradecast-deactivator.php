<?php

/**
 * Fired during wp-plugin deactivation.
 *
 * @since 1.0.0
 * @package Tradecast
 * @subpackage Tradecast/includes
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Deactivator
{
    /**
     * Runs when the plugin is deactivated.
     *
     * @since 1.0.0
     * @access public
     */
    public static function deactivate()
    {
        // requires the Tradecast cron class
        require_once __DIR__ .
            DIRECTORY_SEPARATOR .
            '..' .
            DIRECTORY_SEPARATOR .
            'admin' .
            DIRECTORY_SEPARATOR .
            'class-tradecast-cron.php';

        // registers the cron jobs
        (new Tradecast_Admin_Cron())->unschedule_event();
    }
}
