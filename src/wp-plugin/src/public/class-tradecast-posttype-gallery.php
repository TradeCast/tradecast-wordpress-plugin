<?php

/**
 * The gallery post type class. Installs a post type for galleries.
 *
 * @package    Tradecast
 * @subpackage Tradecast/public
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_PostType_Gallery
{
    /**
     * Registers the post type for galleries.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_post_type()
    {
        register_post_type('tradecast-galleries', $this->get_options());
    }

    /**
     * Registers the post type's meta fields.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_post_type_meta_fields()
    {
        // registers a meta field for the gallery type
        register_meta('post', '_tradecast_gallery_type', [
            'object_subtype' => 'tradecast-galleries',
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ]);

        // registers a meta field for the gallery ids
        register_meta('post', '_tradecast_gallery_ids', [
            'object_subtype' => 'tradecast-galleries',
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ]);

        // registers a meta field for the gallery titles
        register_meta('post', '_tradecast_gallery_titles', [
            'object_subtype' => 'tradecast-galleries',
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ]);

        // registers a meta field for whether the gallery is inaccessible
        register_meta('post', '_tradecast_gallery_is_inaccessible', [
            'object_subtype' => 'tradecast-galleries',
            'type' => 'boolean',
            'single' => true,
            'show_in_rest' => true,
            'default' => false,
        ]);
    }

    /**
     * Returns an array of options for the post type.
     *
     * @since 1.0.0
     * @access private
     *
     * @return array
     */
    private function get_options()
    {
        return [
            'label' => __('Gallery', TRADECAST_TEXT_DOMAIN),
            'description' => __('Gallery description', TRADECAST_TEXT_DOMAIN),
            'labels' => $this->get_labels(),
            'taxonomies' => ['category', 'post_tag'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rest_base' => 'tradecast-galleries',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => ['title', 'editor', 'author', 'custom-fields'],
        ];
    }

    /**
     * Returns an array of labels for the post type.
     *
     * @since 1.0.0
     * @access private
     *
     * @return array
     */
    private function get_labels()
    {
        return [
            'name' => _x('Galleries', 'Galleries', TRADECAST_TEXT_DOMAIN),
            'singular_name' => _x('Gallery', 'Gallery', TRADECAST_TEXT_DOMAIN),
            'menu_name' => __('Galleries', TRADECAST_TEXT_DOMAIN),
            'name_admin_bar' => __('Gallery', TRADECAST_TEXT_DOMAIN),
            'archives' => __('Item Archives', TRADECAST_TEXT_DOMAIN),
            'attributes' => __('Item Attributes', TRADECAST_TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Item:', TRADECAST_TEXT_DOMAIN),
            'all_items' => __('All Galleries', TRADECAST_TEXT_DOMAIN),
            'add_new_item' => __('Add New', TRADECAST_TEXT_DOMAIN),
            'add_new' => __('New Gallery', TRADECAST_TEXT_DOMAIN),
            'new_item' => __('New Item', TRADECAST_TEXT_DOMAIN),
            'edit_item' => __('Edit Item', TRADECAST_TEXT_DOMAIN),
            'update_item' => __('New Item', TRADECAST_TEXT_DOMAIN),
            'view_item' => __('New Item', TRADECAST_TEXT_DOMAIN),
            'view_items' => __('New Items', TRADECAST_TEXT_DOMAIN),
            'search_items' => __('New Items', TRADECAST_TEXT_DOMAIN),
            'not_found' => __('Not found', TRADECAST_TEXT_DOMAIN),
            'not_found_in_trash' => __('Not found in Trash', TRADECAST_TEXT_DOMAIN),
            'featured_image' => __('Set featured image', TRADECAST_TEXT_DOMAIN),
            'remove_featured_image' => __('Remove featured image', TRADECAST_TEXT_DOMAIN),
            'insert_into_item' => __('Insert into item', TRADECAST_TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this item', TRADECAST_TEXT_DOMAIN),
            'items_list' => __('Items list', TRADECAST_TEXT_DOMAIN),
            'items_list_navigation' => __('Items list navigation', TRADECAST_TEXT_DOMAIN),
            'filter_items_list' => __('Filter items list', TRADECAST_TEXT_DOMAIN),
        ];
    }
}
