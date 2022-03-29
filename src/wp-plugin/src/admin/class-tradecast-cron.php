<?php

/**
 * The cronjob class for the admin-specific functionality of this plugin.
 *
 * @package Tradecast
 * @subpackage Tradecast/admin
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Cron
{
    public const CRON_HOOK = 'tradecast_run_cron';
    public const CRON_SCHEDULE = 'hourly';

    private const ARRAY_KEY_ERRORS = 'errors';
    private const ARRAY_KEY_GALLERIES = 'galleries';
    private const ARRAY_KEY_VIDEOS = 'videos';
    private const ARRAY_KEY_NEXT_PAGE = 'next_page';

    private const OPTION_CRON = 'tradecast_cron';

    private const TRADECAST_API_URL = 'https://api.tradecast.eu/v3/graphql';

    /**
     * Registers the scheduled event.
     *
     * @since 1.0.0
     * @access public
     */
    public function schedule_event()
    {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time(), self::CRON_SCHEDULE, self::CRON_HOOK);
        }
    }

    /**
     * Removes the scheduled event.
     *
     * @since 1.0.0
     * @access public
     */
    public function unschedule_event()
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        wp_unschedule_event($timestamp, self::CRON_HOOK);
    }

    /**
     * Checks the videos against Tradecast's API.
     *
     * @since 1.0.0
     * @access public
     */
    public function check_videos(): void
    {
        // get data from the cron
        // everytime the cron runs, it checks a limited number (page) from the API, to limit the amount of resources used
        $cron_data = $this->get_cron();
        $page = $cron_data[self::ARRAY_KEY_NEXT_PAGE][self::ARRAY_KEY_VIDEOS] ?? 1;
        $next_page = $page + 1;
        $video_errors = $cron_data[self::ARRAY_KEY_ERRORS][self::ARRAY_KEY_VIDEOS] ?? [];

        // fetch the video posts
        $query = new WP_Query([
            'paged' => $page,
            'posts_per_page' => 15,
            'post_type' => 'tradecast-videos',
            'order' => 'asc',
            'orderby' => 'title',
        ]);

        // go back to the first page if we reached the last page
        if ($next_page > $query->max_num_pages) {
            $next_page = 1;
        }

        // iterate over posts and check its video against Tradecasts API
        foreach ($query->posts as $post) {
            $tradecast_video_id = get_registered_metadata('post', $post->ID, '_tradecast_video_id');

            $result = !empty($tradecast_video_id)
                ? $this->run_query('query { media (id: ' . $tradecast_video_id . ') { id } }')
                : [];

            $is_inaccessible = !empty($result['errors']);

            wp_update_post([
                'ID' => $post->ID,
                'meta_input' => ['_tradecast_video_is_inaccessible' => $is_inaccessible],
            ]);

            if ($is_inaccessible) {
                $video_errors[] = $post->ID;
            }

            if (!$is_inaccessible) {
                $video_errors = array_filter($video_errors, static function ($id) use ($post) {
                    return $id !== $post->ID;
                });
            }
        }

        // set the cron data
        $cron_data[self::ARRAY_KEY_ERRORS][self::ARRAY_KEY_VIDEOS] = array_unique($video_errors);
        $cron_data[self::ARRAY_KEY_NEXT_PAGE][self::ARRAY_KEY_VIDEOS] = $next_page;

        $this->set_cron($cron_data);
    }

    /**
     * Checks the galleries against Tradecast's API.
     *
     * @since 1.0.0
     * @access public
     */
    public function check_galleries(): void
    {
        // get data from the cron
        // everytime the cron runs, it checks a limited number (page) from the API, to limit the amount of resources used
        $cron_data = $this->get_cron();
        $page = $cron_data[self::ARRAY_KEY_NEXT_PAGE][self::ARRAY_KEY_GALLERIES] ?? 1;
        $next_page = $page + 1;
        $gallery_errors = $cron_data[self::ARRAY_KEY_ERRORS][self::ARRAY_KEY_GALLERIES] ?? [];

        // fetch the gallery posts
        $query = new WP_Query([
            'paged' => $page,
            'posts_per_page' => 5,
            'post_type' => 'tradecast-galleries',
            'order' => 'asc',
            'orderby' => 'title',
        ]);

        // go back to the first page if we reached the last page
        if ($next_page > $query->max_num_pages) {
            $next_page = 1;
        }

        // iterate over posts and check its video against Tradecasts API
        foreach ($query->posts as $post) {
            $tradecast_gallery_ids = get_registered_metadata('post', $post->ID, '_tradecast_gallery_ids');
            $tradecast_gallery_type = get_registered_metadata('post', $post->ID, '_tradecast_gallery_type');

            if ($tradecast_gallery_type !== 'categories' && $tradecast_gallery_type !== 'interests') {
                continue;
            }

            $entity = 'category';

            if ($tradecast_gallery_type === 'interests') {
                $entity = 'interest';
            }

            $is_inaccessible = false;

            if (!is_array($tradecast_gallery_ids) || empty($tradecast_gallery_ids)) {
                continue;
            }

            foreach ($tradecast_gallery_ids as $gallery_id) {
                $result = $this->run_query('query { ' . $entity . ' (id: ' . $gallery_id . ') { id } }');

                if (!empty($result['errors'])) {
                    $is_inaccessible = true;
                    break;
                }
            }

            wp_update_post([
                'ID' => $post->ID,
                'meta_input' => ['_tradecast_gallery_is_inaccessible' => $is_inaccessible],
            ]);

            if ($is_inaccessible) {
                $gallery_errors[] = $post->ID;
            }

            if (!$is_inaccessible) {
                $gallery_errors = array_filter($gallery_errors, static function ($id) use ($post) {
                    return $id !== $post->ID;
                });
            }
        }

        // set the cron data
        $cron_data[self::ARRAY_KEY_ERRORS][self::ARRAY_KEY_GALLERIES] = array_unique($gallery_errors);
        $cron_data[self::ARRAY_KEY_NEXT_PAGE][self::ARRAY_KEY_GALLERIES] = $next_page;

        $this->set_cron($cron_data);
    }

    /**
     * Returns the cron data. If empty, it returns default cron data.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_cron(): array
    {
        $cron_data = get_option(self::OPTION_CRON);

        if (empty($cron_data)) {
            return [
                self::ARRAY_KEY_ERRORS => [
                    self::ARRAY_KEY_GALLERIES => [],
                    self::ARRAY_KEY_VIDEOS => [],
                ],
                self::ARRAY_KEY_NEXT_PAGE => [
                    self::ARRAY_KEY_GALLERIES => 1,
                    self::ARRAY_KEY_VIDEOS => 1,
                ],
            ];
        }

        return $cron_data;
    }

    /**
     * Adds the cron data to the WordPress options.
     *
     * @since 1.0.0
     * @access private
     * @param array $data
     *
     * @return array
     */
    private function set_cron(array $data): array
    {
        $cron_data = $this->get_cron();

        if ($cron_data) {
            update_option(self::OPTION_CRON, $data);
        }

        if (!$cron_data) {
            add_option(self::OPTION_CRON, $data);
        }

        return $this->get_cron();
    }

    /**
     * Returns the channel id from the settings.
     *
     * @since 1.0.0
     * @access private
     *
     * @return mixed|string
     */
    private function get_channel_id()
    {
        $settings = get_option(Tradecast_Admin_Settings_Api_Extensions::OPTION_SETTINGS);

        return $settings[Tradecast_Admin_Settings_Api_Extensions::ARRAY_KEY_CHANNEL_ID] ?: '';
    }

    /**
     * Runs a query against Tradecasts API.
     *
     * @since 1.0.0
     * @access private
     *
     * @param string $query
     * @return array
     */
    private function run_query(string $query): array
    {
        $response = wp_remote_post(self::TRADECAST_API_URL, [
            'body' => wp_json_encode(['query' => $query]),
            'method' => 'POST',
            'timeout' => 30,
            'headers' => [
                'content-type' => 'application/json',
                'channelid' => $this->get_channel_id(),
            ],
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        return is_array($data) ? $data : [];
    }
}
