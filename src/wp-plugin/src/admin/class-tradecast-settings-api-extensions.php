<?php

/**
 * The settings api extension class. Extends the WordPress API with settings routes.
 *
 * @package    Tradecast
 * @subpackage Tradecast/admin
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Settings_Api_Extensions
{
    public const ADMIN_API_NAMESPACE = 'tradecast/v1';
    public const ADMIN_API_ROUTE_SETTINGS = '/settings';

    public const OPTION_SETTINGS = 'tradecast_settings';

    public const ARRAY_KEY_CALLBACK = 'callback';
    public const ARRAY_KEY_CHANNEL_ID = 'channelId';
    public const ARRAY_KEY_METHODS = 'methods';
    public const ARRAY_KEY_PERMISSION_CALLBACK = 'permission_callback';
    public const ARRAY_KEY_SUCCESS = 'success';

    /**
     * Registers settings routes in the REST api.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_settings_rest_route()
    {
        // register a detail route for the settings
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_SETTINGS, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::READABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'get_settings'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);

        // register a post route for the settings
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_SETTINGS, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::EDITABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'set_settings'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);
    }

    /**
     * Manages settings through the REST api.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     * @return false|mixed|void
     */
    public function get_settings(WP_REST_Request $request)
    {
        return get_option(self::OPTION_SETTINGS);
    }

    /**
     * Manages settings through the REST api.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     * @return false|mixed|void
     */
    public function set_settings(WP_REST_Request $request)
    {
        $postFields = $this->get_post_fields($request);
        $settingsData = get_option(self::OPTION_SETTINGS);
        $success = false;

        if (isset($postFields[self::ARRAY_KEY_CHANNEL_ID]) && $settingsData) {
            update_option(self::OPTION_SETTINGS, [
                self::ARRAY_KEY_CHANNEL_ID => $postFields[self::ARRAY_KEY_CHANNEL_ID],
            ]);

            $success = true;
        }

        if (isset($postFields[self::ARRAY_KEY_CHANNEL_ID]) && !$settingsData) {
            add_option(self::OPTION_SETTINGS, [self::ARRAY_KEY_CHANNEL_ID => $postFields[self::ARRAY_KEY_CHANNEL_ID]]);

            $success = true;
        }

        $settingsData = get_option(self::OPTION_SETTINGS);

        if (is_array($settingsData)) {
            $settingsData[self::ARRAY_KEY_SUCCESS] = $success;
        }

        return $settingsData;
    }

    /**
     * Parses the posted json body and returns its data as associative array.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array|mixed
     */
    public function get_post_fields(WP_REST_Request $request)
    {
        if ($request->get_content_type()['value'] !== 'application/json') {
            return [];
        }

        return json_decode($request->get_body(), true);
    }
}
