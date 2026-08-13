<?php
/**
 * Domio Hero block render.
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

$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$heading_level  = isset( $attributes['headingLevel'] ) ? (int) $attributes['headingLevel'] : 1;
$subheading     = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
$primary_text   = isset( $attributes['primaryCtaText'] ) ? $attributes['primaryCtaText'] : '';
$primary_url    = isset( $attributes['primaryCtaUrl'] ) ? $attributes['primaryCtaUrl'] : '';
$secondary_text = isset( $attributes['secondaryCtaText'] ) ? $attributes['secondaryCtaText'] : '';
$secondary_url  = isset( $attributes['secondaryCtaUrl'] ) ? $attributes['secondaryCtaUrl'] : '';
$image_id       = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$image_alt      = isset( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
$layout         = isset( $attributes['layout'] ) ? $attributes['layout'] : 'banner';

// Backward compatibility with earlier "overlay" layout name.
if ( 'overlay' === $layout ) {
	$layout = 'banner';
}

if ( ! in_array( $layout, array( 'banner', 'split', 'centered' ), true ) ) {
	$layout = 'banner';
}

if ( ! in_array( $heading_level, array( 1, 2 ), true ) ) {
	$heading_level = 1;
}

$heading_tag = 1 === $heading_level ? 'h1' : 'h2';
$is_banner   = ( 'banner' === $layout );
$show_meta   = is_singular( 'post' );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => trim(
			'domio-hero domio-hero--' . $layout
			. ( $is_banner ? ' alignfull' : '' )
			. ( $show_meta ? ' domio-hero--has-meta' : '' )
			. ' '
			. ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' )
		),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);

$image_html = '';
if ( $image_id > 0 ) {
	$image_html = wp_get_attachment_image(
		$image_id,
		$is_banner ? 'full' : 'large',
		false,
		array(
			'class'         => 'domio-hero__image',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'alt'           => $image_alt,
		)
	);
}

if ( ! $image_html ) {
	$image_url = isset( $attributes['imageUrl'] ) ? (string) $attributes['imageUrl'] : '';
	if ( '' === $image_url && function_exists( 'domio_get_default_hero_image_url' ) ) {
		$image_url = domio_get_default_hero_image_url();
	}
	if ( '' === $image_alt && function_exists( 'domio_get_default_hero_image_alt' ) ) {
		$image_alt = domio_get_default_hero_image_alt();
	}
	if ( $image_url ) {
		$image_html = sprintf(
			'<img src="%1$s" alt="%2$s" class="domio-hero__image" loading="eager" fetchpriority="high" decoding="async" />',
			esc_url( $image_url ),
			esc_attr( $image_alt )
		);
	}
}

$author_id   = $show_meta ? (int) get_the_author_meta( 'ID' ) : 0;
$author_name = $show_meta ? get_the_author() : '';
$author_url  = $author_id ? get_author_posts_url( $author_id ) : '';
$avatar      = $author_id
	? get_avatar(
		$author_id,
		48,
		'',
		$author_name,
		array(
			'class' => 'domio-hero__meta-avatar',
		)
	)
	: '';
$published     = $show_meta ? get_the_date( DATE_W3C ) : '';
$published_label = $show_meta ? get_the_date() : '';
$modified      = $show_meta ? get_the_modified_date( DATE_W3C ) : '';
$modified_label = $show_meta ? get_the_modified_date() : '';
$show_modified = $show_meta && $modified && $modified !== $published;
$categories    = $show_meta ? get_the_category() : array();

$word_count   = 0;
$reading_mins = 0;
if ( $show_meta ) {
	$raw_content = (string) get_post_field( 'post_content', get_the_ID() );
	$text        = trim( wp_strip_all_tags( $raw_content ) );
	$words       = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	$word_count  = is_array( $words ) ? count( $words ) : 0;
	$reading_mins = $word_count > 0 ? max( 1, (int) ceil( $word_count / 200 ) ) : 0;
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $image_html ) : ?>
		<div class="domio-hero__media" aria-hidden="<?php echo $is_banner ? 'true' : 'false'; ?>">
			<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
		</div>
	<?php endif; ?>

	<div class="domio-hero__inner">
		<div class="domio-hero__content">
			<?php if ( $heading ) : ?>
				<<?php echo esc_attr( $heading_tag ); ?> class="domio-hero__heading">
					<?php echo wp_kses_post( $heading ); ?>
				</<?php echo esc_attr( $heading_tag ); ?>>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="domio-hero__subheading"><?php echo wp_kses_post( $subheading ); ?></p>
			<?php endif; ?>

			<?php if ( ( $primary_text && $primary_url ) || ( $secondary_text && $secondary_url ) ) : ?>
				<div class="domio-hero__actions">
					<?php if ( $primary_text && $primary_url ) : ?>
						<a class="domio-hero__cta domio-hero__cta--primary" href="<?php echo esc_url( $primary_url ); ?>">
							<?php echo esc_html( $primary_text ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $secondary_text && $secondary_url ) : ?>
						<a class="domio-hero__cta domio-hero__cta--secondary" href="<?php echo esc_url( $secondary_url ); ?>">
							<?php echo esc_html( $secondary_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_meta ) : ?>
				<div class="domio-hero__meta">
					<div class="domio-hero__meta-details">
						<?php if ( $published_label ) : ?>
							<span class="domio-hero__meta-item">
								<span class="domio-hero__meta-label"><?php echo esc_html__( 'Julkaistu', 'domio' ); ?></span>
								<time class="domio-hero__meta-value" datetime="<?php echo esc_attr( $published ); ?>">
									<?php echo esc_html( $published_label ); ?>
								</time>
							</span>
						<?php endif; ?>

						<?php if ( $show_modified ) : ?>
							<span class="domio-hero__meta-item">
								<span class="domio-hero__meta-label"><?php echo esc_html__( 'Päivitetty', 'domio' ); ?></span>
								<time class="domio-hero__meta-value" datetime="<?php echo esc_attr( $modified ); ?>">
									<?php echo esc_html( $modified_label ); ?>
								</time>
							</span>
						<?php endif; ?>

						<?php if ( $reading_mins > 0 ) : ?>
							<span class="domio-hero__meta-item">
								<span class="domio-hero__meta-label"><?php echo esc_html__( 'Lukuaika', 'domio' ); ?></span>
								<span class="domio-hero__meta-value">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %d: estimated reading time in minutes */
											_n( '%d min', '%d min', $reading_mins, 'domio' ),
											$reading_mins
										)
									);
									?>
								</span>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $categories ) ) : ?>
						<ul class="domio-hero__meta-categories">
							<?php foreach ( $categories as $category ) : ?>
								<li>
									<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
										<?php echo esc_html( $category->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $author_name ) : ?>
						<div class="domio-hero__meta-author">
							<?php if ( $avatar ) : ?>
								<span class="domio-hero__meta-avatar-wrap" aria-hidden="true">
									<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar. ?>
								</span>
							<?php endif; ?>
							<div class="domio-hero__meta-author-text">
								<span class="domio-hero__meta-label"><?php echo esc_html__( 'Kirjoittaja', 'domio' ); ?></span>
								<?php if ( $author_url ) : ?>
									<a class="domio-hero__meta-author-name" href="<?php echo esc_url( $author_url ); ?>">
										<?php echo esc_html( $author_name ); ?>
									</a>
								<?php else : ?>
									<span class="domio-hero__meta-author-name"><?php echo esc_html( $author_name ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
