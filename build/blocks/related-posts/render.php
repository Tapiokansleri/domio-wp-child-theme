<?php
/**
 * Domio related-posts (Lue myös) render.
 *
 * @package Domio
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$columns = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
if ( $columns < 2 || $columns > 3 ) {
	$columns = 3;
}

$post_ids = array();
if ( isset( $attributes['postIds'] ) && is_array( $attributes['postIds'] ) ) {
	foreach ( $attributes['postIds'] as $id ) {
		$id = (int) $id;
		if ( $id > 0 ) {
			$post_ids[] = $id;
		}
	}
}
$post_ids = array_values( array_unique( $post_ids ) );
$current  = get_queried_object_id();
if ( $current ) {
	$post_ids = array_values(
		array_filter(
			$post_ids,
			static function ( $id ) use ( $current ) {
				return (int) $id !== (int) $current;
			}
		)
	);
}

$posts = array();
if ( $post_ids ) {
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post__in'            => $post_ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => count( $post_ids ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$posts = $query->posts;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => trim(
			'domio-related-posts ' .
			( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' )
		),
		'style' => trim(
			'--domio-related-columns:' . $columns . ';' .
			( function_exists( 'domio_get_section_style' ) ? (string) domio_get_section_style( $attributes ) : '' )
		),
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-related-posts__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-related-posts__heading"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( empty( $posts ) ) : ?>
			<?php if ( is_admin() ) : ?>
				<p class="domio-related-posts__empty"><?php esc_html_e( 'Valitse artikkelit sivupalkista.', 'domio' ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<div class="domio-related-posts__grid">
				<?php foreach ( $posts as $related ) : ?>
					<?php
					$permalink = get_permalink( $related );
					$excerpt   = $related->post_excerpt
						? $related->post_excerpt
						: $related->post_content;
					$excerpt   = wp_trim_words( wp_strip_all_tags( $excerpt ), 22, ' …' );
					$thumb     = get_the_post_thumbnail(
						$related,
						'large',
						array(
							'class'    => 'domio-related-posts__image',
							'loading'  => 'lazy',
							'decoding' => 'async',
							'alt'      => '',
						)
					);
					?>
					<article class="domio-related-posts__card">
						<?php if ( $thumb ) : ?>
							<a class="domio-related-posts__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
								<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail. ?>
							</a>
						<?php endif; ?>
						<time class="domio-related-posts__date" datetime="<?php echo esc_attr( get_the_date( 'c', $related ) ); ?>">
							<?php echo esc_html( get_the_date( 'j.n.Y', $related ) ); ?>
						</time>
						<h3 class="domio-related-posts__title">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<?php echo esc_html( get_the_title( $related ) ); ?>
							</a>
						</h3>
						<?php if ( $excerpt ) : ?>
							<p class="domio-related-posts__excerpt"><?php echo esc_html( $excerpt ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
