<?php

/**
 * The videos API extension class. Extends the WordPress Admin API with video routes.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tradecast
 * @subpackage Tradecast/admin
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Videos_Api_Extensions
{
    public const ADMIN_API_NAMESPACE = 'tradecast/v1';
    public const ADMIN_API_ROUTE_VIDEO = '/video';
    public const ADMIN_API_ROUTE_VIDEOS = '/videos';

    private const ARRAY_KEY_CALLBACK = 'callback';
    private const ARRAY_KEY_DATA = 'data';
    private const ARRAY_KEY_ID = 'id';
    private const ARRAY_KEY_METHODS = 'methods';
    private const ARRAY_KEY_PERMISSION_CALLBACK = 'permission_callback';
    private const ARRAY_KEY_SUCCESS = 'success';

    /**
     * Registers the video routes in the REST api.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_video_rest_route()
    {
        // registers a listing route for videos
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_VIDEOS, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::READABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'list_videos'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);

        // registers a details route for videos
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_VIDEO . '/(?P<id>\d+)', [
            self::ARRAY_KEY_METHODS => WP_REST_Server::READABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'get_video'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
            self::ARRAY_KEY_ID,
        ]);

        // registers a post route for videos
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_VIDEO, [
            self::ARRAY_KEY_METHODS => WP_REST_Server::EDITABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'add_video'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
        ]);

        // registers a delete route for videos
        register_rest_route(self::ADMIN_API_NAMESPACE, self::ADMIN_API_ROUTE_VIDEO . '/(?P<id>\d+)', [
            self::ARRAY_KEY_METHODS => WP_REST_Server::DELETABLE,
            self::ARRAY_KEY_CALLBACK => [$this, 'delete_video'],
            self::ARRAY_KEY_PERMISSION_CALLBACK => '__return_true',
            self::ARRAY_KEY_ID,
        ]);
    }

    /**
     * Lists videos. Returns an array of video data, if any videos exist.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array|null
     */
    public function list_videos(WP_REST_Request $request)
    {
        $videos = [];
        $query = new WP_Query([
            'paged' => $request->get_param('page') ?? 1,
            'posts_per_page' => $request->get_param('posts_per_page') ?? 10,
            'post_type' => 'tradecast-videos',
            'order' => $request->get_param('order') ?? 'asc',
            'orderby' => $request->get_param('order_by') ?? 'title',
        ]);

        if (!$query->post_count) {
            return $videos;
        }

        $videos = [
            'data' => [],
            'pagination' => [
                'page' => $request->get_param('page') ?? 1,
                'total_pages' => $query->max_num_pages,
            ],
        ];

        foreach ($query->posts as $post) {
            $videos['data'][] = $this->get_video_data($post);
        }

        return $videos;
    }

    /**
     * Adds a video. Returns an array with the video's data.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array
     */
    public function add_video(WP_REST_Request $request): array
    {
        $postFields = $this->get_post_fields($request);

        try {
            $postId = wp_insert_post([
                'post_title' => $postFields['title'],
                'post_content' => $postFields['description'],
                'post_type' => 'tradecast-videos',
                'post_status' => 'publish',
                'meta_input' => [
                    '_tradecast_video_id' => $postFields['id'],
                    '_tradecast_video_embed_url' => $postFields['embedURL'],
                    '_tradecast_video_thumbnail_url' => $postFields['thumb'],
                    '_tradecast_video_created_at' => $postFields['createdAt'],
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
     * Gets a video by id. Returns an array of the video's data if the id exists.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return false|mixed|void
     */
    public function get_video(WP_REST_Request $request)
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
            'post_type' => 'tradecast-videos',
            'order' => $request->get_param('order') ?? 'asc',
            'orderby' => $request->get_param('order_by') ?? 'title',
        ]);

        if (!$query->post_count) {
            return null;
        }

        return $this->get_video_data($query->post);
    }

    /**
     * Deletes a video by id. Returns a boolean for its success.
     *
     * @since 1.0.0
     * @access public
     * @param WP_REST_Request $request
     *
     * @return array|false|WP_Post|null
     */
    public function delete_video(WP_REST_Request $request)
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
     * Gets the video's data for a post of type gallery.
     *
     * @since 1.0.0
     * @access private
     * @param WP_Post $post
     *
     * @return array
     */
    private function get_video_data(WP_Post $post)
    {
        $tradecast_video_id = get_registered_metadata('post', $post->ID, '_tradecast_video_id');
        $tradecast_video_embed_url = get_registered_metadata('post', $post->ID, '_tradecast_video_embed_url');
        $tradecast_video_thumbnail_url = get_registered_metadata('post', $post->ID, '_tradecast_video_thumbnail_url');
        $tradecast_video_created_at = get_registered_metadata('post', $post->ID, '_tradecast_video_created_at');
        $tradecast_video_is_inaccessible = get_registered_metadata(
            'post',
            $post->ID,
            '_tradecast_video_is_inaccessible'
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
                'video_id' => $tradecast_video_id ? (int) $tradecast_video_id : null,
                'video_embed_url' => $tradecast_video_embed_url,
                'video_thumbnail_url' => $tradecast_video_thumbnail_url,
                'video_created_at' => $tradecast_video_created_at,
                'video_is_inaccessible' => (bool) $tradecast_video_is_inaccessible,
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
