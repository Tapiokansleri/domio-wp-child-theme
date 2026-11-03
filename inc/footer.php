<?php
/**
 * Domio footer (replaces the Elementor Theme Builder footer).
 *
 * Link columns come from the menus at "Footer: Pikalinkit" and "Footer: Pääpalvelut" (Ulkoasu → Valikot),
 * with Domio's current links as the fallback. Tagline, description, offices and social links are edited in
 * Teeman asetukset → Footer; phone and email come from the Header tab.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register footer menu locations.
 *
 * @return void
 */
function domio_register_footer_menus() {
	register_nav_menus(
		array(
			'domio-footer-quick'    => __( 'Footer: Pikalinkit', 'domio' ),
			'domio-footer-services' => __( 'Footer: Pääpalvelut', 'domio' ),
		)
	);
}
add_action( 'after_setup_theme', 'domio_register_footer_menus' );

/**
 * Default footer settings.
 *
 * @return array<string, string>
 */
function domio_get_default_footer_settings() {
	return array(
		'footer_tagline'   => 'Aina askelman edellä',
		'footer_text'      => 'Domio Kiinteistöpalvelut tarjoaa kattavat kiinteistöhuolto- ja siivouspalvelut pääkaupunkiseudulla.',
		'footer_offices'   => "Espoon toimipiste | Tiistinniityntie 8 C, halli 8, 02230 Espoo | https://maps.app.goo.gl/GbxfcJ2NqDmqZCqC6\nHelsinki & Vantaa toimipiste | Keimolanmäentie 11 D, halli 21, 01700 Vantaa | https://www.google.com/maps/search/?api=1&query=Keimolanm%C3%A4entie%2011%20D%2C%20halli%2021%2C%2001700%20Vantaa",
		'footer_instagram' => 'https://www.instagram.com/domio_kiinteistopalvelut/',
		'footer_facebook'  => 'https://www.facebook.com/share/1GAEY4Xgsa/?mibextid=wwXIfr',
	);
}

/**
 * Footer links used when no menu is assigned to a footer location.
 *
 * @return array<string, array<int, array{0: string, 1: string}>>
 */
function domio_get_default_footer_links() {
	return array(
		'domio-footer-quick'    => array(
			array( 'Etusivu', '/' ),
			array( 'Meistä', '/tietoja-meista/' ),
			array( 'Palvelut', '/kiinteistopalvelut/' ),
			array( 'Ota yhteyttä', '/ota-yhteytta/' ),
		),
		'domio-footer-services' => array(
			array( 'Kiinteistöhuolto', '/kiinteistohuolto/' ),
			array( 'Siivouspalvelut', '/siivouspalvelut/' ),
			array( 'Piha- ja vihertyöt', '/piha-ja-vihertyot/' ),
			array( 'Remontti- ja korjauspalvelut', '/remontti-ja-korjauspalvelut/' ),
			array( 'Kone- ja kuljetuspalvelut', '/kone-ja-kuljetuspalvelut/' ),
			array( 'LVI-palvelut', '/lvi-palvelut/' ),
		),
	);
}

/**
 * Offices parsed from the settings textarea ("Otsikko | Osoite | Karttalinkki" per line).
 *
 * @param string $raw Setting value.
 * @return array<int, array{title: string, address: string, url: string}>
 */
function domio_parse_footer_offices( $raw ) {
	$offices = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( '' === $parts[0] ) {
			continue;
		}
		$offices[] = array(
			'title'   => $parts[0],
			'address' => isset( $parts[1] ) ? $parts[1] : '',
			'url'     => isset( $parts[2] ) ? $parts[2] : '',
		);
	}
	return $offices;
}

/**
 * Enqueue the footer stylesheet.
 *
 * @return void
 */
function domio_enqueue_footer_assets() {
	wp_enqueue_style( 'domio-footer', DOMIO_THEME_URI . '/assets/css/domio-footer.css', array( 'domio-style' ), DOMIO_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'domio_enqueue_footer_assets', 25 );

/**
 * One footer link list: the assigned menu, or the default links.
 *
 * @param string $location Menu location.
 * @return void
 */
function domio_render_footer_links( $location ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'domio-footer__links',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	$defaults = domio_get_default_footer_links();
	echo '<ul class="domio-footer__links">';
	foreach ( $defaults[ $location ] as $link ) {
		echo '<li><a href="' . esc_url( home_url( $link[1] ) ) . '">' . esc_html( $link[0] ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Small inline icons used only in the footer.
 *
 * @param string $name Icon key.
 * @return string SVG markup.
 */
function domio_footer_icon( $name ) {
	$icons = array(
		'navigation' => '<path d="M3 11l18-8-8 18-2-8-8-2z"/>',
		'phone'      => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.1-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.1a2 2 0 012.1-.4c.8.3 1.7.5 2.6.6a2 2 0 011.7 2z"/>',
		'mail'       => '<path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/><path d="M22 6l-10 7L2 6"/>',
		'instagram'  => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>',
		'facebook'   => '<path d="M14 8h3V4h-3a4 4 0 00-4 4v3H7v4h3v7h4v-7h3l1-4h-4V8a1 1 0 011-1z" fill="currentColor" stroke="none"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Render the footer.
 *
 * @return void
 */
function domio_render_footer() {
	$settings = function_exists( 'domio_get_settings' ) ? domio_get_settings() : array();
	$footer   = wp_parse_args( $settings, domio_get_default_footer_settings() );
	$offices  = domio_parse_footer_offices( $footer['footer_offices'] );
	$phone    = isset( $settings['phone'] ) ? $settings['phone'] : '';
	$email    = isset( $settings['email'] ) ? $settings['email'] : '';

	/** Filters the footer logo and badge attachment IDs (domio-full-logo, luotettava kumppani). */
	$images = apply_filters( 'domio_footer_images', array( 'logo' => 1215, 'badge' => 2047 ) );

	$social = array_filter(
		array(
			'instagram' => array( $footer['footer_instagram'], 'Instagram' ),
			'facebook'  => array( $footer['footer_facebook'], 'Facebook' ),
		),
		function ( $item ) {
			return '' !== $item[0];
		}
	);
	?>
	<footer class="domio-footer" role="contentinfo">
		<div class="domio-footer__inner">
			<div class="domio-footer__top">
				<div class="domio-footer__brand">
					<a class="domio-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php
						$logo = wp_get_attachment_image( (int) $images['logo'], 'full', false, array( 'alt' => get_bloginfo( 'name' ), 'loading' => 'lazy' ) );
						echo $logo ? $logo : esc_html( get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup.
						?>
					</a>
					<?php if ( $footer['footer_tagline'] ) : ?>
						<p class="domio-footer__tagline"><?php echo esc_html( $footer['footer_tagline'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="domio-footer__badge">
					<?php echo wp_get_attachment_image( (int) $images['badge'], 'medium', false, array( 'alt' => 'Luotettava Kumppani', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
				</div>
				<?php if ( $footer['footer_text'] ) : ?>
					<p class="domio-footer__text"><?php echo esc_html( $footer['footer_text'] ); ?></p>
				<?php endif; ?>
			</div>

			<div class="domio-footer__middle">
				<nav class="domio-footer__col" aria-label="<?php echo esc_attr__( 'Pikalinkit', 'domio' ); ?>">
					<h2 class="domio-footer__heading"><?php echo esc_html__( 'Pikalinkit', 'domio' ); ?></h2>
					<?php domio_render_footer_links( 'domio-footer-quick' ); ?>
				</nav>
				<nav class="domio-footer__col" aria-label="<?php echo esc_attr__( 'Pääpalvelut', 'domio' ); ?>">
					<h2 class="domio-footer__heading"><?php echo esc_html__( 'Pääpalvelut', 'domio' ); ?></h2>
					<?php domio_render_footer_links( 'domio-footer-services' ); ?>
				</nav>
				<div class="domio-footer__card">
					<?php foreach ( $offices as $office ) : ?>
						<h2 class="domio-footer__card-heading"><?php echo esc_html( $office['title'] ); ?></h2>
						<?php if ( $office['address'] ) : ?>
							<p class="domio-footer__item">
								<?php echo domio_footer_icon( 'navigation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<?php if ( $office['url'] ) : ?>
									<a href="<?php echo esc_url( $office['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $office['address'] ); ?></a>
								<?php else : ?>
									<span><?php echo esc_html( $office['address'] ); ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ( $phone || $email ) : ?>
						<h2 class="domio-footer__card-heading"><?php echo esc_html__( 'Ota yhteyttä', 'domio' ); ?></h2>
						<?php if ( $phone ) : ?>
							<p class="domio-footer__item">
								<?php echo domio_footer_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
							</p>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<p class="domio-footer__item">
								<?php echo domio_footer_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
							</p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="domio-footer__bottom">
				<p class="domio-footer__copy">
					<?php
					/* translators: %s: year */
					printf( esc_html__( '© %s Domio kiinteistöpalvelut. Kaikki oikeudet pidätetään.', 'domio' ), esc_html( wp_date( 'Y' ) ) );
					?>
				</p>
				<div class="domio-footer__meta">
					<a class="domio-footer__privacy" href="<?php echo esc_url( home_url( '/rekisteri-ja-tietosuojaseloste/' ) ); ?>"><?php echo esc_html__( 'Tietosuoja- ja rekisteriseloste', 'domio' ); ?></a>
					<?php if ( $social ) : ?>
						<ul class="domio-footer__social">
							<?php foreach ( $social as $key => $item ) : ?>
								<li>
									<a href="<?php echo esc_url( $item[0] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $item[1] ); ?>">
										<?php echo domio_footer_icon( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</footer>
	<?php
}

/**
 * Footer tab fields on the settings page.
 *
 * @param array<string, mixed> $settings Settings.
 * @return void
 */
function domio_render_settings_footer_fields( $settings ) {
	$footer = wp_parse_args( $settings, domio_get_default_footer_settings() );
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="domio_footer_tagline"><?php echo esc_html__( 'Iskulause logon alla', 'domio' ); ?></label></th>
			<td><input name="domio_theme_settings[footer_tagline]" type="text" id="domio_footer_tagline" value="<?php echo esc_attr( $footer['footer_tagline'] ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_footer_text"><?php echo esc_html__( 'Kuvausteksti', 'domio' ); ?></label></th>
			<td><textarea name="domio_theme_settings[footer_text]" id="domio_footer_text" rows="3" class="large-text"><?php echo esc_textarea( $footer['footer_text'] ); ?></textarea></td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_footer_offices"><?php echo esc_html__( 'Toimipisteet', 'domio' ); ?></label></th>
			<td>
				<textarea name="domio_theme_settings[footer_offices]" id="domio_footer_offices" rows="4" class="large-text code"><?php echo esc_textarea( $footer['footer_offices'] ); ?></textarea>
				<p class="description"><?php echo esc_html__( 'Yksi toimipiste per rivi: Otsikko | Osoite | Karttalinkki. Puhelin ja sähköposti tulevat Header-välilehdeltä.', 'domio' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_footer_instagram"><?php echo esc_html__( 'Instagram', 'domio' ); ?></label></th>
			<td><input name="domio_theme_settings[footer_instagram]" type="url" id="domio_footer_instagram" value="<?php echo esc_attr( $footer['footer_instagram'] ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_footer_facebook"><?php echo esc_html__( 'Facebook', 'domio' ); ?></label></th>
			<td><input name="domio_theme_settings[footer_facebook]" type="url" id="domio_footer_facebook" value="<?php echo esc_attr( $footer['footer_facebook'] ); ?>" class="regular-text" /></td>
		</tr>
	</table>
	<p>
		<?php
		echo esc_html__( 'Linkkisarakkeet muokataan kohdassa', 'domio' ) . ' ';
		echo '<a href="' . esc_url( admin_url( 'nav-menus.php?action=locations' ) ) . '">' . esc_html__( 'Ulkoasu → Valikot', 'domio' ) . '</a> ';
		echo esc_html__( '(sijainnit “Footer: Pikalinkit” ja “Footer: Pääpalvelut”). Ilman valikkoa näytetään nykyiset linkit.', 'domio' );
		?>
	</p>
	<?php
}

/**
 * Clean the footer fields.
 *
 * @param array $input Submitted settings.
 * @return array<string, string>
 */
function domio_sanitize_footer_settings( $input ) {
	$defaults = domio_get_default_footer_settings();
	return array(
		'footer_tagline'   => isset( $input['footer_tagline'] ) ? sanitize_text_field( $input['footer_tagline'] ) : $defaults['footer_tagline'],
		'footer_text'      => isset( $input['footer_text'] ) ? sanitize_textarea_field( $input['footer_text'] ) : $defaults['footer_text'],
		'footer_offices'   => isset( $input['footer_offices'] ) ? sanitize_textarea_field( $input['footer_offices'] ) : $defaults['footer_offices'],
		'footer_instagram' => isset( $input['footer_instagram'] ) ? esc_url_raw( $input['footer_instagram'] ) : $defaults['footer_instagram'],
		'footer_facebook'  => isset( $input['footer_facebook'] ) ? esc_url_raw( $input['footer_facebook'] ) : $defaults['footer_facebook'],
	);
}
