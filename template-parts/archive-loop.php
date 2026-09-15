<?php
/**
 * Shared archive / blog / search loop.
 *
 * Expects $args['empty'] optional empty-state string.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$header = function_exists( 'domio_get_archive_header' )
	? domio_get_archive_header()
	: array(
		'eyebrow'     => '',
		'title'       => get_the_archive_title(),
		'description' => '',
	);

$empty = isset( $args['empty'] )
	? (string) $args['empty']
	: __( 'Artikkeleita ei löytynyt.', 'domio' );
?>
<main id="content" class="site-main domio-archive">
	<div class="domio-archive__inner">
		<header class="domio-archive__head">
			<?php if ( ! empty( $header['eyebrow'] ) ) : ?>
				<p class="domio-archive__eyebrow"><?php echo esc_html( $header['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="domio-archive__title"><?php echo esc_html( $header['title'] ); ?></h1>
			<?php if ( ! empty( $header['description'] ) ) : ?>
				<div class="domio-archive__description">
					<?php echo wp_kses_post( $header['description'] ); ?>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( is_search() ) : ?>
			<div class="domio-archive__search">
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="domio-archive__grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post', 'card' );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Edellinen', 'domio' ),
					'next_text' => __( 'Seuraava', 'domio' ),
				)
			);
			?>
		<?php else : ?>
			<p class="domio-archive__empty"><?php echo esc_html( $empty ); ?></p>
		<?php endif; ?>
	</div>
</main>
