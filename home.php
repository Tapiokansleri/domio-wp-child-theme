<?php
/**
 * Blog posts index.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/archive', 'loop' );
get_footer();
