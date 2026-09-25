<?php
/**
 * Author archive: profile, expertise, credentials and articles.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$author    = get_queried_object();
$author_id = ( $author instanceof WP_User ) ? (int) $author->ID : (int) get_query_var( 'author' );
$profile   = domio_get_author_profile( $author_id );
$name      = $profile ? $profile['name'] : '';
$photo     = $profile
	? get_avatar(
		$author_id,
		480,
		'',
		$name,
		array(
			'class'   => 'domio-author__photo-img',
			'loading' => 'eager',
		)
	)
	: '';
$paged     = max( 1, (int) get_query_var( 'paged' ) );
?>

<main id="content" class="site-main domio-author">
	<section class="domio-author__hero domio-bg--muted domio-pattern--1" style="--domio-pattern-opacity:0.03;">
		<div class="domio-author__inner domio-author__profile">
			<?php if ( $photo ) : ?>
				<div class="domio-author__photo">
					<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar. ?>
				</div>
			<?php endif; ?>

			<div class="domio-author__intro">
				<p class="domio-author__eyebrow"><?php esc_html_e( 'Kirjoittaja', 'domio' ); ?></p>
				<h1 class="domio-author__title"><?php echo esc_html( $name ); ?></h1>

				<?php if ( $profile && ( '' !== $profile['job_title'] || '' !== $profile['company'] ) ) : ?>
					<p class="domio-author__role">
						<?php if ( '' !== $profile['job_title'] ) : ?>
							<span><?php echo esc_html( $profile['job_title'] ); ?></span><?php echo '' !== $profile['company'] ? ',' : ''; ?>
						<?php endif; ?>
						<?php if ( '' !== $profile['company'] ) : ?>
							<a href="<?php echo esc_url( $profile['company_url'] ); ?>"><?php echo esc_html( $profile['company'] ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>

				<?php if ( $profile && '' !== $profile['bio'] ) : ?>
					<div class="domio-author__bio">
						<?php echo wp_kses_post( wpautop( $profile['bio'] ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $profile && $profile['post_count'] > 0 ) : ?>
					<ul class="domio-author__facts">
						<li>
							<span class="domio-author__fact-value"><?php echo esc_html( (string) $profile['post_count'] ); ?></span>
							<span class="domio-author__fact-label"><?php echo esc_html( _n( 'artikkeli', 'artikkelia', $profile['post_count'], 'domio' ) ); ?></span>
						</li>
						<?php if ( $profile['updated'] ) : ?>
							<li>
								<span class="domio-author__fact-label"><?php esc_html_e( 'Viimeksi päivitetty', 'domio' ); ?></span>
								<time class="domio-author__fact-value" datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C, $profile['updated'] ) ); ?>">
									<?php echo esc_html( get_the_modified_date( 'j.n.Y', $profile['updated'] ) ); ?>
								</time>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<div class="domio-author__actions">
					<?php if ( $profile && $profile['post_count'] > 0 ) : ?>
						<a class="domio-author__button domio-author__button--primary" href="#artikkelit"><?php esc_html_e( 'Lue artikkelit', 'domio' ); ?></a>
					<?php endif; ?>
					<?php if ( $profile && '' !== $profile['linkedin'] ) : ?>
						<a class="domio-author__button" href="<?php echo esc_url( $profile['linkedin'] ); ?>" rel="me noopener" target="_blank"><?php esc_html_e( 'LinkedIn', 'domio' ); ?></a>
					<?php endif; ?>
					<?php if ( $profile && '' !== $profile['contact_url'] ) : ?>
						<a class="domio-author__button" href="<?php echo esc_url( $profile['contact_url'] ); ?>"><?php esc_html_e( 'Ota yhteyttä', 'domio' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $profile && ( $profile['expertise'] || $profile['credentials'] ) && 1 === $paged ) : ?>
		<section class="domio-author__section domio-bg--surface">
			<div class="domio-author__inner domio-author__details">
				<?php if ( $profile['expertise'] ) : ?>
					<div class="domio-author__detail">
						<h2 class="domio-author__heading"><?php esc_html_e( 'Asiantuntemus', 'domio' ); ?></h2>
						<ul class="domio-author__chips">
							<?php foreach ( $profile['expertise'] as $topic ) : ?>
								<li><?php echo esc_html( $topic ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $profile['credentials'] ) : ?>
					<div class="domio-author__detail">
						<h2 class="domio-author__heading"><?php esc_html_e( 'Koulutus ja pätevyydet', 'domio' ); ?></h2>
						<ul class="domio-author__list">
							<?php foreach ( $profile['credentials'] as $credential ) : ?>
								<li><?php echo esc_html( $credential ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<section id="artikkelit" class="domio-author__section domio-author__articles domio-bg--surface">
		<div class="domio-author__inner">
			<h2 class="domio-author__heading"><?php esc_html_e( 'Artikkelit', 'domio' ); ?></h2>

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
				<p class="domio-author__empty"><?php esc_html_e( 'Tällä kirjoittajalla ei ole vielä julkaistuja artikkeleita.', 'domio' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php echo do_blocks( '<!-- wp:domio/cta {"variant":"band","heading":"Haluatko tietää, mitä kiinteistösi huolto maksaisi?","pattern":"2","patternOpacity":10} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered block. ?>
</main>

<?php
get_footer();
