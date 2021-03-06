<?php
/**
 * Plugin Name: Semantic-Linkbacks
 * Plugin URI: https://github.com/pfefferle/wordpress-semantic-linkbacks
 * Description: Semantic Linkbacks for WebMentions, Trackbacks and Pingbacks
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
 * Version: 3.6.0
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: semantic-linkbacks
 * Requires PHP: 5.3
 */

add_action( 'plugins_loaded', array( 'Semantic_Linkbacks_Plugin', 'init' ) );

/**
 * Semantic linkbacks class
 *
 * @author Matthias Pfefferle
 */
class Semantic_Linkbacks_Plugin {
	public static $version = '3.5.0';
	/**
	 * Initialize the plugin, registering WordPress hooks.
	 */
	public static function init() {
		require_once( dirname( __FILE__ ) . '/includes/functions.php' );

		require_once( dirname( __FILE__ ) . '/includes/class-linkbacks-handler.php' );
		add_action( 'init', array( 'Linkbacks_Handler', 'init' ) );

		require_once( dirname( __FILE__ ) . '/includes/class-linkbacks-mf2-handler.php' );
		add_action( 'init', array( 'Linkbacks_MF2_Handler', 'init' ) );

		add_action( 'wp_enqueue_scripts', array( 'Semantic_Linkbacks_Plugin', 'style_load' ) );

		remove_filter( 'webmention_comment_data', array( 'Webmention_Receiver', 'default_title_filter' ), 21 );
		remove_filter( 'webmention_comment_data', array( 'Webmention_Receiver', 'default_content_filter' ), 22 );
		self::plugin_textdomain();

		// initialize admin settings
		add_action( 'admin_init', array( 'Semantic_Linkbacks_Plugin', 'admin_init' ) );
	}

	public static function admin_init() {
		add_settings_field( 'semantic_linkbacks_discussion_settings', __( 'Semantic Linkbacks Settings', 'webmention' ), array( 'Semantic_Linkbacks_Plugin', 'discussion_settings' ), 'discussion', 'default' );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_mention', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Mentions', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_repost', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Reposts', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_like', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Likes', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_reaction', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Reactions (emoji)', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_favorite', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Favorite', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_tag', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Tags', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_bookmark', array(
			'type' => 'boolean',
			'description' => __( 'Facepile Bookmarks', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepile_rsvp', array(
			'type' => 'boolean',
			'description' => __( 'Facepile RSVPs', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 1,
		) );
		register_setting( 'discussion', 'semantic_linkbacks_facepiles_fold_limit', array(
			'type' => 'integer',
			'description' => __( 'Initial number of faces to show in facepiles', 'semantic-linkbacks' ),
			'show_in_rest' => true,
			'default' => 8,
		) );
	}

	/**
	 * Load language files
	 */
	public static function plugin_textdomain() {
		// Note to self, the third argument must not be hardcoded, to account for relocated folders.
		load_plugin_textdomain( 'semantic-linkbacks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add Semantic Linkbacks options to the WordPress discussion settings page.
	 */
	public static function discussion_settings() {
		load_template( plugin_dir_path( __FILE__ ) . 'templates/discussion-settings.php' );
	}


	public static function style_load() {
		wp_enqueue_style( 'semantic-linkbacks-css', plugin_dir_url( __FILE__ ) . 'css/semantic-linkbacks.css', array(), self::$version );
	}
}
