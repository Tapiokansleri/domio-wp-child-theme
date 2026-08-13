<?php
/**
 * Domio Trustmary block render.
 *
 * @package Domio
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$widget_src = isset( $attributes['widgetSrc'] ) ? (string) $attributes['widgetSrc'] : '';

if ( '' === $widget_src ) {
	$widget_src = 'https://widget.trustmary.com/n3eIUlhS5';
}

$allowed_host = 'widget.trustmary.com';
$parsed       = wp_parse_url( $widget_src );
$is_valid     = is_array( $parsed )
	&& ! empty( $parsed['scheme'] )
	&& 'https' === strtolower( $parsed['scheme'] )
	&& ! empty( $parsed['host'] )
	&& 0 === strcasecmp( $parsed['host'], $allowed_host );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-trustmary alignfull ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-trustmary__inner">
		<?php if ( $is_valid ) : ?>
			<div class="domio-trustmary__widget">
				<script src="<?php echo esc_url( $widget_src ); ?>"></script>
			</div>
		<?php endif; ?>
	</div>
</section>
