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
 * Install a short code for videos.
 *
 * @package    Tradecast
 * @subpackage Tradecast/public
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Shortcode_Video
{
    private const SHORTCODE_NAME = 'tradecast-video';

    /**
     * Registers the short code for videos.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_shortcode()
    {
        add_shortcode(self::SHORTCODE_NAME, function ($atts) {
            return $this->tradecast_video_shortcode($atts);
        });
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function tradecast_video_shortcode(array $attributes)
    {
        $attributes = shortcode_atts(
            [
                'class' => '',
                'id' => '',
                'width' => '960',
                'height' => '540',
                'autoplay' => 'false',
            ],
            $attributes,
            self::SHORTCODE_NAME
        );

        $metadata = get_post_meta($attributes['id'], '_tradecast_video_embed_url');

        if (empty($metadata)) {
            return '';
        }

        $embed_url = reset($metadata);
        $embed_url .=
            (strrpos($embed_url, '?') ? '&' : '?') .
            'autoplay=' .
            (strtolower($attributes['autoplay']) === 'true' ? '1' : '0');

        return sprintf(
            '<div class="tradecast-video"><iframe class="%s" src="%s" width="%s" height="%s" frameborder="0" allowfullscreen></iframe></div>',
            esc_attr($attributes['class']),
            esc_attr($embed_url),
            esc_attr($attributes['width']),
            esc_attr($attributes['height'])
        );
    }
}
