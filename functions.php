<?php
/**
 * Domio child theme functions and definitions.
 *
 * Child theme of Hello Elementor for the Domio site. Loads Domio styles
 * and wires custom blocks, block styles, patterns, and JSON-LD schema.
 *
 * Made by Tapio Kauranen — https://tapiokauranen.com
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOMIO_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'DOMIO_THEME_DIR', get_stylesheet_directory() );
define( 'DOMIO_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue parent and child theme styles.
 *
 * @return void
 */
function domio_enqueue_styles() {
	wp_enqueue_style(
		'domio-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(
			'hello-elementor-theme-style',
		),
		DOMIO_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'domio_enqueue_styles', 20 );

require_once DOMIO_THEME_DIR . '/inc/cpt-tyopaikka.php';
require_once DOMIO_THEME_DIR . '/inc/elementor.php';
require_once DOMIO_THEME_DIR . '/inc/blocks.php';
require_once DOMIO_THEME_DIR . '/inc/block-styles.php';
require_once DOMIO_THEME_DIR . '/inc/patterns.php';
require_once DOMIO_THEME_DIR . '/inc/schema.php';
require_once DOMIO_THEME_DIR . '/inc/icons.php';
require_once DOMIO_THEME_DIR . '/inc/typography.php';
require_once DOMIO_THEME_DIR . '/inc/settings.php';
require_once DOMIO_THEME_DIR . '/inc/header.php';
require_once DOMIO_THEME_DIR . '/inc/updater.php';
