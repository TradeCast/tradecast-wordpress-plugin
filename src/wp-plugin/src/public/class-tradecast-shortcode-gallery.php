<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.kiener.nl
 * @since      1.0.0
 *
 * @package    Tradecast
 * @subpackage Tradecast/admin
 */

/**
 * Install a short code for galleries.
 *
 * @package    Tradecast
 * @subpackage Tradecast/public
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Shortcode_Gallery
{
    private const SHORTCODE_NAME = 'tradecast-gallery';

    /**
     * Registers the short code for galleries.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_shortcode()
    {
        add_shortcode(self::SHORTCODE_NAME, function ($atts) {
            return $this->tradecast_gallery_shortcode($atts);
        });
    }

    /**
     * Parses the shortcode and returns an HTML element for it.
     *
     * @since 1.0.0
     * @access public
     * @param array $attributes
     *
     * @return string
     */
    public function tradecast_gallery_shortcode(array $attributes)
    {
        $attributes = shortcode_atts(
            [
                'class' => '',
                'id' => '',
                'columns' => '3',
            ],
            $attributes,
            self::SHORTCODE_NAME
        );

        if (empty($attributes['id'])) {
            return '';
        }

        $type = get_registered_metadata('post', $attributes['id'], '_tradecast_gallery_type') ?? '';
        $ids = implode(',', get_registered_metadata('post', $attributes['id'], '_tradecast_gallery_ids') ?? []);
        $settings = get_option(Tradecast_Admin_Settings_Api_Extensions::OPTION_SETTINGS);
        $channelId = $settings[Tradecast_Admin_Settings_Api_Extensions::ARRAY_KEY_CHANNEL_ID] ?: '';

        return sprintf(
            '<div id="tradecast_gallery" class="%s" data-gallery-type="%s" data-gallery-ids="%s" data-channel-id="%s" data-columns="%s"></div>',
            esc_attr(trim('tradecast-gallery columns-' . $attributes['columns'] . ' ' . $attributes['class'])),
            esc_attr($type),
            esc_attr($ids),
            esc_attr($channelId),
            esc_attr($attributes['columns'])
        );
    }
}
