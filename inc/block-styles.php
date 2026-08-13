<?php
/**
 * Domio block style variations.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register block styles for Domio blocks.
 *
 * @return void
 */
function domio_register_block_styles() {
	register_block_style(
		'domio/card-grid',
		array(
			'name'       => 'service',
			'label'      => __( 'Palvelut', 'domio' ),
			'is_default' => true,
		)
	);

	register_block_style(
		'domio/card-grid',
		array(
			'name'  => 'reason',
			'label' => __( 'Miksi Domio', 'domio' ),
		)
	);

	register_block_style(
		'domio/card-grid',
		array(
			'name'  => 'reference',
			'label' => __( 'Referenssit', 'domio' ),
		)
	);

	register_block_style(
		'domio/cta',
		array(
			'name'       => 'band',
			'label'      => __( 'Väli-CTA', 'domio' ),
			'is_default' => true,
		)
	);

	register_block_style(
		'domio/cta',
		array(
			'name'  => 'form',
			'label' => __( 'Lomake-CTA', 'domio' ),
		)
	);
}
add_action( 'init', 'domio_register_block_styles', 20 );
