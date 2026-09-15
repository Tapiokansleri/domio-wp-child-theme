<?php
/**
 * Search results.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part(
	'template-parts/archive',
	'loop',
	array(
		'empty' => __( 'Haku ei tuottanut tuloksia. Kokeile toista hakusanaa.', 'domio' ),
	)
);
get_footer();
