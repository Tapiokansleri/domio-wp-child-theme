<?php
/**
 * Domio trust bar block render.
 *
 * @package Domio
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items     = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$image_id  = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$image_alt = isset( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';

$items = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return is_array( $item ) && ! empty( $item['text'] );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$image_html = '';
if ( $image_id > 0 ) {
	$image_html = wp_get_attachment_image(
		$image_id,
		'full',
		false,
		array(
			'class'    => 'domio-trust-bar__image',
			'alt'      => $image_alt,
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
}

if ( ! $image_html ) {
	$image_url = isset( $attributes['imageUrl'] ) ? (string) $attributes['imageUrl'] : '';
	if ( $image_url ) {
		$image_html = sprintf(
			'<img src="%1$s" alt="%2$s" class="domio-trust-bar__image" loading="lazy" decoding="async" />',
			esc_url( $image_url ),
			esc_attr( $image_alt )
		);
	}
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-trust-bar alignfull ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes, 'green' ) : 'domio-bg--green' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes, 100 ) : '',
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $image_html ) : ?>
		<div class="domio-trust-bar__media" aria-hidden="true">
			<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
		</div>
	<?php endif; ?>

	<div class="domio-trust-bar__inner">
		<ul class="domio-trust-bar__list">
			<?php foreach ( $items as $item ) : ?>
				<li class="domio-trust-bar__item">
					<span class="domio-trust-bar__icon" aria-hidden="true">
						<?php
						echo domio_get_icon_svg( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
							isset( $item['icon'] ) ? $item['icon'] : 'check'
						);
						?>
					</span>
					<span class="domio-trust-bar__text"><?php echo esc_html( $item['text'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
