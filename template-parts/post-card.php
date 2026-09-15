<?php
/**
 * Blog card used on archives, search, and the blog index.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$permalink = get_permalink( $post_id );
$excerpt   = get_post_field( 'post_excerpt', $post_id );
if ( ! $excerpt ) {
	$excerpt = get_post_field( 'post_content', $post_id );
}
$excerpt = preg_replace( '/<!--.*?-->/s', ' ', (string) $excerpt );
$excerpt = preg_replace( '/<\/(p|h[1-6]|li|div|blockquote)>/i', ' ', $excerpt );
$excerpt = wp_trim_words( wp_strip_all_tags( $excerpt ), 22, ' …' );
$thumb   = get_the_post_thumbnail(
	$post_id,
	'large',
	array(
		'class'    => 'domio-archive__image',
		'loading'  => 'lazy',
		'decoding' => 'async',
		'alt'      => '',
	)
);
?>
<article <?php post_class( 'domio-archive__card' ); ?>>
	<a class="domio-archive__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $thumb ) : ?>
			<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail. ?>
		<?php endif; ?>
	</a>
	<time class="domio-archive__date" datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
		<?php echo esc_html( get_the_date( 'j.n.Y', $post_id ) ); ?>
	</time>
	<h2 class="domio-archive__card-title">
		<a href="<?php echo esc_url( $permalink ); ?>">
			<?php echo esc_html( get_the_title( $post_id ) ); ?>
		</a>
	</h2>
	<?php if ( $excerpt ) : ?>
		<p class="domio-archive__excerpt"><?php echo esc_html( $excerpt ); ?></p>
	<?php endif; ?>
</article>
