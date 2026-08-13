<?php
/**
 * Domio child theme header.
 *
 * When “Käytä Domio headeriä” is enabled, Elementor Theme Builder header
 * is skipped and the Domio header is forced.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
$enable_skip_link = apply_filters( 'hello_elementor_enable_skip_link', true );
$skip_link_url    = apply_filters( 'hello_elementor_skip_link_url', '#content' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php if ( $enable_skip_link ) : ?>
	<a class="skip-link screen-reader-text" href="<?php echo esc_url( $skip_link_url ); ?>">
		<?php echo esc_html__( 'Siirry sisältöön', 'domio' ); ?>
	</a>
<?php endif; ?>

<?php
if ( function_exists( 'domio_use_custom_header' ) && domio_use_custom_header() ) {
	get_template_part( 'template-parts/domio-header' );
} elseif ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	if ( function_exists( 'hello_elementor_display_header_footer' ) && hello_elementor_display_header_footer() ) {
		if ( did_action( 'elementor/loaded' ) && function_exists( 'hello_header_footer_experiment_active' ) && hello_header_footer_experiment_active() ) {
			get_template_part( 'template-parts/dynamic-header' );
		} else {
			get_template_part( 'template-parts/header' );
		}
	}
}
