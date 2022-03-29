<?php

/**
 * The gutenberg blocks class. Registers the Gutenberg blocks.
 *
 * @package Tradecast
 * @subpackage Tradecast/admin
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Gutenberg_Blocks
{
    /**
     * Registers the Gutenberg block for videos.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_video_block()
    {
        register_block_type(__DIR__ . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'video');
    }

    /**
     * Registers the Gutenberg block for galleries.
     *
     * @since 1.0.0
     * @access public
     */
    public function register_gallery_block()
    {
        register_block_type(__DIR__ . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'gallery');
    }
}
