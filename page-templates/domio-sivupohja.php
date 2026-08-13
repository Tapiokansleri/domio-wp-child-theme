<?php
/**
 * Template Name: Domio sivupohja
 * Template Post Type: page, post
 *
 * Full-width landing template without the default page/post title.
 * Designed for Domio Gutenberg landing blocks (hero provides the H1).
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>

<main id="content" <?php post_class( 'site-main domio-landing-template domio-landing-template--fullwidth' ); ?>>
	<div class="page-content domio-landing-template__content is-layout-flow">
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</div>
</main>

	<?php
endwhile;

get_footer();
