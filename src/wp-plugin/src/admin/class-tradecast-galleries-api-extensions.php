<?php

/**
 * The galleries API extension class. Extends the WordPress Admin API with gallery routes.
 *
 * @package Tradecast
 * @subpackage Tradecast/admin
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Galleries_Api_Extensions
{
    public const ADMIN_API_NAMESPACE = 'tradecast/v1';
    public const ADMIN_API_ROUTE_GALLERY = '/gallery';
    public const ADMIN_API_ROUTE_GALLERIES = '/galleries';

    private const ARRAY_KEY_CALLBACK = 'callback';
    private const ARRAY_KEY_DATA = 'data';
    private const ARRAY_KEY_ID = 'id';
    private const ARRAY_KEY_METHODS = 'methods';
    private const ARRAY_KEY_PERMISSION_CALLBACK = 'permission_callback';
    private const ARRAY_KEY_SUCCESS = 'success';

    /**
     * Register a gallery route in the REST api.
     */
    public function register_gallery_rest_route()
    {
        // register a listing route for the galleries
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_GALLERIES, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::READABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'list_galleries'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);

        // register a detail route for the galleries
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_GALLERY . '/(?P<id>\d+)', [
            self::ARRAY_KEY_METHODS => WP_REST_Server::READABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'get_gallery'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
            self::ARRAY_KEY_ID,
        ]);

        // register a post route for the galleries
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_GALLERY, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::EDITABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'add_gallery'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);

        // register a delete route for the galleries
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_GALLERY . '/(?P<id>\d+)', [
            self::ARRAY_KEY_METHODS => WP_REST_Server::DELETABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'delete_gallery'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
            self::ARRAY_KEY_ID,
        ]);
    }

    /**
     * Lists galleries. Returns an array of gallery data, if any galleries exist.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array|null
     */
    public function list_galleries(WP_REST_Request $request)
    {
        $galleries = [];
        $query = new WP_Query([
            'paged' => $request->get_param('page') ?? 1,
            'posts_per_page' => $request->get_param('posts_per_page') ?? 10,
            'post_type' => 'tradecast-galleries',
            'order' => $request->get_param('order') ?? 'asc',
            'orderby' => $request->get_param('order_by') ?? 'title',
        ]);

        if (!$query->post_count) {
            return $galleries;
        }

        $galleries = [
            'data' => [],
            'pagination' => [
                'page' => $request->get_param('page') ?? 1,
                'total_pages' => $query->max_num_pages,
            ],
        ];

        foreach ($query->posts as $post) {
            $galleries['data'][] = $this->get_gallery_data($post);
        }

        return $galleries;
    }

    /**
     * Adds a gallery. Returns an array with the gallery's data.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array
     */
    public function add_gallery(WP_REST_Request $request): array
    {
        $postFields = $this->get_post_fields($request);

        try {
            $postId = wp_insert_post([
                'post_title' => $postFields['title'],
                'post_type' => 'tradecast-galleries',
                'post_status' => 'publish',
                'meta_input' => [
                    '_tradecast_gallery_type' => $postFields['type'],
                    '_tradecast_gallery_ids' =>
                        isset($postFields['ids']) && is_array($postFields['ids']) ? $postFields['ids'] : [],
                    '_tradecast_gallery_titles' =>
                        isset($postFields['titles']) && is_array($postFields['titles']) ? $postFields['titles'] : [],
                ],
            ]);
        } catch (Throwable $exception) {
        }

        $post = isset($postId) ? get_post($postId) : null;

        return [
            self::ARRAY_KEY_DATA => $post,
            self::ARRAY_KEY_SUCCESS => isset($postId),
        ];
    }

    /**
     * Gets a gallery by id. Returns an array of the gallery's data if the id exists.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return false|mixed|void
     */
    public function get_gallery(WP_REST_Request $request)
    {
        $post_id = $request->get_param('id');

        if (!$post_id) {
            return null;
        }

        $query = new WP_Query([
            'nopaging' => true,
            'p' => $post_id,
            'paged' => $request->get_param('page') ?? 1,
            'posts_per_page' => $request->get_param('posts_per_page') ?? 10,
            'post_type' => 'tradecast-galleries',
            'order' => $request->get_param('order') ?? 'asc',
            'orderby' => $request->get_param('order_by') ?? 'title',
        ]);

        if (!$query->post_count) {
            return null;
        }

        return $this->get_gallery_data($query->post);
    }

    /**
     * Deletes a gallery by id. Returns a boolean for its success.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array|false|WP_Post|null
     */
    public function delete_gallery(WP_REST_Request $request)
    {
        return wp_delete_post($request->get_param('id'));
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

    /**
     * Gets the gallery's data for a post of type gallery.
     *
     * @since 1.0.0
     * @access private
     * @param WP_Post $post
     *
     * @return array
     */
    private function get_gallery_data(WP_Post $post)
    {
        $tradecast_gallery_type = get_registered_metadata('post', $post->ID, '_tradecast_gallery_type');
        $tradecast_gallery_ids = get_registered_metadata('post', $post->ID, '_tradecast_gallery_ids');
        $tradecast_gallery_titles = get_registered_metadata('post', $post->ID, '_tradecast_gallery_titles');
        $tradecast_gallery_is_inaccessible = get_registered_metadata(
            'post',
            $post->ID,
            '_tradecast_gallery_is_inaccessible'
        );

        return [
            'id' => $post->ID,
            'author' => [
                'id' => (int) $post->post_author,
                'name' => $this->get_author_full_name($post->post_author),
            ],
            'date' => get_post_datetime($post->ID)->format('c'),
            'date_gmt' => get_post_datetime($post->ID, 'date_gmt')->format('c'),
            'modified' => get_post_datetime($post->ID, 'modified')->format('c'),
            'modified_gmt' => get_post_datetime($post->ID, 'modified_gmt')->format('c'),
            'title' => $post->post_title,
            'content' => $post->post_content,
            'tradecast' => [
                'gallery_type' => $tradecast_gallery_type,
                'gallery_ids' => is_array($tradecast_gallery_ids) ? $tradecast_gallery_ids : [],
                'gallery_titles' => is_array($tradecast_gallery_titles) ? $tradecast_gallery_titles : [],
                'gallery_is_inaccessible' => (bool) $tradecast_gallery_is_inaccessible,
            ],
            'status' => $post->post_status,
        ];
    }

    /**
     * Gets the full name of an author by its id.
     *
     * @since 1.0.0
     * @access private
     * @param $authorId
     *
     * @return string
     */
    private function get_author_full_name($authorId): string
    {
        $displayName = get_the_author_meta('display_name', $authorId);
        $firstName = get_the_author_meta('first_name', $authorId);
        $lastName = get_the_author_meta('last_name', $authorId);
        $fullName = trim($firstName . ' ' . $lastName);

        return !empty($fullName) ? $fullName : $displayName;
    }
}
