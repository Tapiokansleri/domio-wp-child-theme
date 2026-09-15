<?php
/**
 * 404 — page not found.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="site-main domio-archive">
	<div class="domio-archive__inner">
		<header class="domio-archive__head">
			<p class="domio-archive__eyebrow"><?php esc_html_e( '404', 'domio' ); ?></p>
			<h1 class="domio-archive__title"><?php esc_html_e( 'Sivua ei löytynyt', 'domio' ); ?></h1>
			<p class="domio-archive__lead">
				<?php esc_html_e( 'Osoite on voinut muuttua tai sivu on poistettu. Palaa etusivulle tai etsi artikkeleista.', 'domio' ); ?>
			</p>
		</header>

		<div class="domio-archive__search">
			<?php get_search_form(); ?>
		</div>

		<p class="domio-archive__home">
			<a class="domio-archive__home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Etusivulle', 'domio' ); ?>
			</a>
		</p>
	</div>
</main>
<?php
get_footer();
