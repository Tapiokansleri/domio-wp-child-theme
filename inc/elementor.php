<?php
/**
 * Domio ↔ Elementor integration.
 *
 * When “Domio sivupohja” is active, hide Elementor edit entry points so
 * editors stay in Gutenberg.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Domio page template slug.
 *
 * @return string
 */
function domio_get_page_template_slug() {
	return 'page-templates/domio-sivupohja.php';
}

/**
 * Whether a post uses Domio sivupohja.
 *
 * @param int|\WP_Post|null $post Post ID or object. Null = current post.
 * @return bool
 */
function domio_post_uses_sivupohja( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	return domio_get_page_template_slug() === (string) get_page_template_slug( $post );
}

/**
 * Remove Elementor “Edit with Elementor” from the admin bar on Domio pages.
 *
 * @param array $settings Admin bar config.
 * @return array
 */
function domio_filter_elementor_admin_bar( $settings ) {
	if ( function_exists( 'domio_is_landing_template' ) && domio_is_landing_template() ) {
		unset( $settings['elementor_edit_page'] );
	}

	return $settings;
}
add_filter( 'elementor/frontend/admin_bar/settings', 'domio_filter_elementor_admin_bar' );

/**
 * Remove “Edit with Elementor” from page/post list row actions.
 *
 * @param array    $actions Row actions.
 * @param \WP_Post $post    Post.
 * @return array
 */
function domio_filter_elementor_row_actions( $actions, $post ) {
	if ( domio_post_uses_sivupohja( $post ) ) {
		unset( $actions['edit_with_elementor'] );
	}

	return $actions;
}
add_filter( 'page_row_actions', 'domio_filter_elementor_row_actions', 20, 2 );
add_filter( 'post_row_actions', 'domio_filter_elementor_row_actions', 20, 2 );

/**
 * Hide classic-editor Elementor switch UI when Domio sivupohja is set.
 *
 * @return void
 */
function domio_hide_elementor_classic_switch() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->base ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || ! domio_post_uses_sivupohja( $post_id ) ) {
		return;
	}

	echo '<style id="domio-hide-elementor-classic">#elementor-switch-mode,#elementor-editor{display:none!important}</style>';
}
add_action( 'admin_head', 'domio_hide_elementor_classic_switch' );
