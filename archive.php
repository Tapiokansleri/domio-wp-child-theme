<?php
/**
 * Category, tag, date and taxonomy archives.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/archive', 'loop' );
get_footer();
