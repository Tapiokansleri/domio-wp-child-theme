<?php
/**
 * Domio theme updates from GitHub Releases.
 *
 * Sites check https://github.com/Tapiokansleri/domio-wp-child-theme/releases
 * and offer updates in WP Admin → Appearance → Themes when a newer version
 * tag is published (with a domio.zip release asset).
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$domio_puc = DOMIO_THEME_DIR . '/inc/lib/plugin-update-checker/plugin-update-checker.php';

if ( ! file_exists( $domio_puc ) ) {
	return;
}

require_once $domio_puc;

$domio_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/Tapiokansleri/domio-wp-child-theme/',
	DOMIO_THEME_DIR . '/style.css',
	'domio'
);

$domio_update_checker->getVcsApi()->enableReleaseAssets( '/domio\.zip($|[?#])/' );
