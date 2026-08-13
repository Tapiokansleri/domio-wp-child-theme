<?php
/**
 * Domio Related Links block render.
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

$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$groups  = function_exists( 'domio_get_related_link_groups' )
	? domio_get_related_link_groups()
	: ( isset( $attributes['groups'] ) && is_array( $attributes['groups'] ) ? $attributes['groups'] : array() );

$groups = array_values(
	array_filter(
		$groups,
		static function ( $group ) {
			if ( ! is_array( $group ) ) {
				return false;
			}
			$links = isset( $group['links'] ) && is_array( $group['links'] ) ? $group['links'] : array();
			foreach ( $links as $link ) {
				if ( ! is_array( $link ) ) {
					continue;
				}
				$label = isset( $link['label'] ) ? trim( wp_strip_all_tags( (string) $link['label'] ) ) : '';
				$url   = isset( $link['url'] ) ? trim( (string) $link['url'] ) : '';
				if ( '' !== $label && '' !== $url ) {
					return true;
				}
			}
			return false;
		}
	)
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-related-links ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-related-links__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-related-links__heading"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $groups ) ) : ?>
			<div class="domio-related-links__grid">
				<?php foreach ( $groups as $group ) : ?>
					<?php
					$title = isset( $group['title'] ) ? $group['title'] : '';
					$links = isset( $group['links'] ) && is_array( $group['links'] ) ? $group['links'] : array();
					$links = array_values(
						array_filter(
							$links,
							static function ( $link ) {
								if ( ! is_array( $link ) ) {
									return false;
								}
								$label = isset( $link['label'] ) ? trim( wp_strip_all_tags( (string) $link['label'] ) ) : '';
								$url   = isset( $link['url'] ) ? trim( (string) $link['url'] ) : '';
								return '' !== $label && '' !== $url;
							}
						)
					);
					if ( empty( $links ) ) {
						continue;
					}
					?>
					<div class="domio-related-links__group">
						<?php if ( $title ) : ?>
							<h3 class="domio-related-links__group-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<ul class="domio-related-links__list">
							<?php foreach ( $links as $link ) : ?>
								<li class="domio-related-links__item"><a class="domio-related-links__link" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?><span class="domio-related-links__arrow" aria-hidden="true">→</span></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
