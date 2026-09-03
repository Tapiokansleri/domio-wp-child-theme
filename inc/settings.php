<?php
/**
 * Domio theme settings (Appearance → Teeman asetukset).
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default theme settings.
 *
 * @return array<string, mixed>
 */
function domio_get_default_settings() {
	return array(
		'use_header'          => false,
		'phone'               => '040 630 5536',
		'email'               => 'asiakaspalvelu@domio.fi',
		'cta_text'            => 'Ota yhteyttä',
		'cta_url'             => '/yhteystiedot/',
		'related_link_groups' => array(),
		'jobs_copy'           => domio_get_default_jobs_copy(),
	);
}

/**
 * Default static copy for job archive and single views.
 *
 * @return array<string, string>
 */
function domio_get_default_jobs_copy() {
	return array(
		'archive_title'      => __( 'Työpaikat', 'domio' ),
		'archive_lead'       => __( 'Tule mukaan pitämään taloyhtiöt ja toimitilat kunnossa. Selaa avoimia tehtäviä ja hae sinua kiinnostavaan paikkaan.', 'domio' ),
		'archive_empty'      => __( 'Tällä hetkellä ei ole avoimia työpaikkoja.', 'domio' ),
		'archive_count_one'  => __( '%d työpaikka', 'domio' ),
		'archive_count_many' => __( '%d työpaikkaa', 'domio' ),
		'card_cta'           => __( 'Lue työpaikkailmoitus', 'domio' ),
		'card_read_more'     => __( 'Lue lisää', 'domio' ),
		'single_eyebrow'     => __( 'Avoimet työpaikat', 'domio' ),
		'label_company'      => __( 'Yritys', 'domio' ),
		'label_location'     => __( 'Paikkakunta', 'domio' ),
		'label_salary'       => __( 'Palkka', 'domio' ),
		'label_type'         => __( 'Tyyppi', 'domio' ),
		'label_deadline'     => __( 'Viim. hakupäivä', 'domio' ),
		'badge_ended'        => __( 'Haku päättynyt', 'domio' ),
		'badge_last_day'     => __( 'Viimeinen päivä', 'domio' ),
		'badge_days_one'     => __( '%d päivä jäljellä', 'domio' ),
		'badge_days_many'    => __( '%d päivää jäljellä', 'domio' ),
	);
}

/**
 * Settings page tabs.
 *
 * @return array<string, string>
 */
function domio_get_settings_tabs() {
	return array(
		'header'  => __( 'Header', 'domio' ),
		'related' => __( 'Tutustu myös', 'domio' ),
		'jobs'    => __( 'Työpaikat', 'domio' ),
	);
}

/**
 * Current settings tab slug.
 *
 * @return string
 */
function domio_get_current_settings_tab() {
	$tabs = array_keys( domio_get_settings_tabs() );
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'header'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	return in_array( $tab, $tabs, true ) ? $tab : 'header';
}

/**
 * Public job listing string, with theme-setting override.
 *
 * @param string $key Copy key.
 * @return string
 */
function domio_jobs_copy( $key ) {
	$defaults = domio_get_default_jobs_copy();
	$settings = domio_get_settings();
	$copy     = isset( $settings['jobs_copy'] ) && is_array( $settings['jobs_copy'] ) ? $settings['jobs_copy'] : array();
	$value    = isset( $copy[ $key ] ) ? trim( (string) $copy[ $key ] ) : '';

	if ( '' !== $value ) {
		return $value;
	}

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Sanitize job copy fields.
 *
 * @param mixed $input Raw copy.
 * @return array<string, string>
 */
function domio_sanitize_jobs_copy( $input ) {
	$defaults = domio_get_default_jobs_copy();
	$clean    = array();

	if ( ! is_array( $input ) ) {
		return $defaults;
	}

	foreach ( $defaults as $key => $default ) {
		if ( ! isset( $input[ $key ] ) ) {
			$clean[ $key ] = $default;
			continue;
		}

		$value = 'archive_lead' === $key
			? sanitize_textarea_field( wp_unslash( $input[ $key ] ) )
			: sanitize_text_field( wp_unslash( $input[ $key ] ) );

		$clean[ $key ] = '' !== $value ? $value : $default;
	}

	return $clean;
}

/**
 * Get merged Domio theme settings.
 *
 * @return array<string, mixed>
 */
function domio_get_settings() {
	$defaults = domio_get_default_settings();
	$saved    = get_option( 'domio_theme_settings', array() );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$settings = wp_parse_args( $saved, $defaults );
	$settings['jobs_copy'] = wp_parse_args(
		isset( $saved['jobs_copy'] ) && is_array( $saved['jobs_copy'] ) ? $saved['jobs_copy'] : array(),
		$defaults['jobs_copy']
	);

	return $settings;
}

/**
 * Whether the Domio custom header is enabled.
 *
 * @return bool
 */
function domio_use_custom_header() {
	$settings = domio_get_settings();
	return ! empty( $settings['use_header'] );
}

/**
 * Default related-link groups for the Tutustu myös block.
 *
 * @return array<int, array<string, mixed>>
 */
function domio_get_default_related_link_groups() {
	return array(
		array(
			'id'    => 'group-areas',
			'title' => 'Muut toiminta-alueemme',
			'links' => array(
				array(
					'id'    => 'link-helsinki',
					'label' => 'Kiinteistöhuolto Helsinki',
					'url'   => '/kiinteistohuolto/helsinki/',
				),
				array(
					'id'    => 'link-espoo',
					'label' => 'Kiinteistöhuolto Espoo',
					'url'   => '/kiinteistohuolto/espoo/',
				),
				array(
					'id'    => 'link-vantaa',
					'label' => 'Kiinteistöhuolto Vantaa',
					'url'   => '/kiinteistohuolto/vantaa/',
				),
			),
		),
		array(
			'id'    => 'group-services',
			'title' => 'Palvelut',
			'links' => array(
				array(
					'id'    => 'link-siivous',
					'label' => 'Siivouspalvelut',
					'url'   => '/siivouspalvelut/',
				),
				array(
					'id'    => 'link-piha',
					'label' => 'Piha- ja vihertyöt',
					'url'   => '/piha-ja-vihertyot/',
				),
				array(
					'id'    => 'link-remontti',
					'label' => 'Remontti- ja korjauspalvelut',
					'url'   => '/remontti-ja-korjauspalvelut/',
				),
				array(
					'id'    => 'link-kone',
					'label' => 'Kone- ja kuljetuspalvelut',
					'url'   => '/kone-ja-kuljetuspalvelut/',
				),
				array(
					'id'    => 'link-lvi',
					'label' => 'LVI-palvelut',
					'url'   => '/lvi-palvelut/',
				),
			),
		),
	);
}

/**
 * Sanitize related-link groups.
 *
 * @param mixed $groups Raw groups.
 * @return array<int, array<string, mixed>>
 */
function domio_sanitize_related_link_groups( $groups ) {
	if ( ! is_array( $groups ) ) {
		return array();
	}

	$clean = array();

	foreach ( $groups as $group ) {
		if ( ! is_array( $group ) ) {
			continue;
		}

		$title = isset( $group['title'] ) ? sanitize_text_field( $group['title'] ) : '';
		$links = isset( $group['links'] ) && is_array( $group['links'] ) ? $group['links'] : array();
		$items = array();

		foreach ( $links as $link ) {
			if ( ! is_array( $link ) ) {
				continue;
			}

			$label = isset( $link['label'] ) ? sanitize_text_field( $link['label'] ) : '';
			$url   = isset( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';

			if ( '' === $label && '' === $url ) {
				continue;
			}

			$items[] = array(
				'id'    => isset( $link['id'] ) && is_string( $link['id'] ) && '' !== $link['id']
					? sanitize_key( $link['id'] )
					: uniqid( 'link-' ),
				'label' => $label,
				'url'   => $url,
			);
		}

		if ( '' === $title && empty( $items ) ) {
			continue;
		}

		$clean[] = array(
			'id'    => isset( $group['id'] ) && is_string( $group['id'] ) && '' !== $group['id']
				? sanitize_key( $group['id'] )
				: uniqid( 'group-' ),
			'title' => $title,
			'links' => $items,
		);
	}

	return $clean;
}

/**
 * Get related-link groups used by every Tutustu myös block.
 *
 * @return array<int, array<string, mixed>>
 */
function domio_get_related_link_groups() {
	$saved = get_option( 'domio_theme_settings', array() );

	if ( is_array( $saved ) && ! empty( $saved['related_link_groups'] ) && is_array( $saved['related_link_groups'] ) ) {
		return array_values( $saved['related_link_groups'] );
	}

	return domio_get_default_related_link_groups();
}

/**
 * Register settings page under Appearance.
 *
 * @return void
 */
function domio_register_settings_page() {
	add_theme_page(
		__( 'Teeman asetukset', 'domio' ),
		__( 'Teeman asetukset', 'domio' ),
		'edit_theme_options',
		'domio-settings',
		'domio_render_settings_page'
	);
}
add_action( 'admin_menu', 'domio_register_settings_page' );

/**
 * Register setting.
 *
 * @return void
 */
function domio_register_settings() {
	register_setting(
		'domio_theme_settings_group',
		'domio_theme_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'domio_sanitize_settings',
			'default'           => domio_get_default_settings(),
		)
	);
}
add_action( 'admin_init', 'domio_register_settings' );

/**
 * Keep the active settings tab after save.
 *
 * @param string $location Redirect URL.
 * @return string
 */
function domio_settings_preserve_tab( $location ) {
	if ( ! isset( $_POST['option_page'] ) || 'domio_theme_settings_group' !== $_POST['option_page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $location;
	}

	if ( empty( $_POST['domio_settings_tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $location;
	}

	return add_query_arg( 'tab', sanitize_key( wp_unslash( $_POST['domio_settings_tab'] ) ), $location );
}
add_filter( 'wp_redirect', 'domio_settings_preserve_tab' );

/**
 * Sanitize settings array.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function domio_sanitize_settings( $input ) {
	$defaults = domio_get_default_settings();

	if ( ! is_array( $input ) ) {
		return $defaults;
	}

	$existing = get_option( 'domio_theme_settings', array() );
	if ( ! is_array( $existing ) ) {
		$existing = array();
	}

	$existing = wp_parse_args( $existing, $defaults );
	$existing['jobs_copy'] = wp_parse_args(
		isset( $existing['jobs_copy'] ) && is_array( $existing['jobs_copy'] ) ? $existing['jobs_copy'] : array(),
		$defaults['jobs_copy']
	);

	$tab = isset( $_POST['domio_settings_tab'] ) ? sanitize_key( wp_unslash( $_POST['domio_settings_tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified by options.php.

	if ( 'header' === $tab ) {
		$existing['use_header'] = ! empty( $input['use_header'] );
		$existing['phone']      = isset( $input['phone'] ) ? sanitize_text_field( $input['phone'] ) : $defaults['phone'];
		$existing['email']      = isset( $input['email'] ) ? sanitize_email( $input['email'] ) : $defaults['email'];
		$existing['cta_text']   = isset( $input['cta_text'] ) ? sanitize_text_field( $input['cta_text'] ) : $defaults['cta_text'];
		$existing['cta_url']    = isset( $input['cta_url'] ) ? esc_url_raw( $input['cta_url'] ) : $defaults['cta_url'];
	}

	if ( 'related' === $tab || isset( $_POST['domio_related_links_saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$existing['related_link_groups'] = isset( $input['related_link_groups'] )
			? domio_sanitize_related_link_groups( $input['related_link_groups'] )
			: array();
	}

	if ( 'jobs' === $tab ) {
		$existing['jobs_copy'] = domio_sanitize_jobs_copy( isset( $input['jobs_copy'] ) ? $input['jobs_copy'] : array() );
	}

	return $existing;
}

/**
 * Render the settings page.
 *
 * @return void
 */
function domio_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$settings = domio_get_settings();
	$tabs     = domio_get_settings_tabs();
	$current  = domio_get_current_settings_tab();
	$base     = admin_url( 'themes.php?page=domio-settings' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Teeman asetukset', 'domio' ); ?></h1>
		<?php settings_errors(); ?>

		<nav class="nav-tab-wrapper" aria-label="<?php echo esc_attr__( 'Asetusvälilehdet', 'domio' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a
					href="<?php echo esc_url( add_query_arg( 'tab', $slug, $base ) ); ?>"
					class="nav-tab<?php echo $current === $slug ? ' nav-tab-active' : ''; ?>"
				><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php">
			<?php settings_fields( 'domio_theme_settings_group' ); ?>
			<input type="hidden" name="domio_settings_tab" value="<?php echo esc_attr( $current ); ?>" />

			<?php if ( 'header' === $current ) : ?>
				<p><?php echo esc_html__( 'Domio-header ohittaa Elementorin Theme Builder -headerin.', 'domio' ); ?></p>
				<p class="description">
					<?php echo esc_html__( 'Logo haetaan sivuston asetuksista:', 'domio' ); ?>
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>">
						<?php echo esc_html__( 'Ulkoasu → Muokkaa → Sivuston tunnus', 'domio' ); ?>
					</a>
				</p>
				<?php domio_render_settings_header_fields( $settings ); ?>
				<p>
					<?php
					echo esc_html__( 'Valitse valikko sijainnille “Domio päävalikko” kohdassa', 'domio' );
					echo ' ';
					echo '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Ulkoasu → Valikot', 'domio' ) . '</a>.';
					?>
				</p>
			<?php elseif ( 'related' === $current ) : ?>
				<p>
					<?php echo esc_html__( 'Nämä linkit näkyvät kaikilla sivuilla, joilla on Domio: Tutustu myös -palikka. Muutos päivittyy kaikkiin ländäreihin kerralla.', 'domio' ); ?>
				</p>
				<input type="hidden" name="domio_related_links_saved" value="1" />
				<?php
				domio_render_related_links_fields(
					function_exists( 'domio_get_related_link_groups' ) ? domio_get_related_link_groups() : array()
				);
				?>
			<?php else : ?>
				<p>
					<?php echo esc_html__( 'Nämä tekstit näkyvät työpaikkalistauksessa ja yksittäisessä ilmoituksessa. Tyhjä kenttä palauttaa oletustekstin.', 'domio' ); ?>
				</p>
				<?php domio_render_settings_jobs_fields( $settings ); ?>
			<?php endif; ?>

			<?php submit_button( __( 'Tallenna asetukset', 'domio' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Header tab fields.
 *
 * @param array<string, mixed> $settings Settings.
 * @return void
 */
function domio_render_settings_header_fields( $settings ) {
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php echo esc_html__( 'Header', 'domio' ); ?></th>
			<td>
				<label for="domio_use_header">
					<input
						name="domio_theme_settings[use_header]"
						type="checkbox"
						id="domio_use_header"
						value="1"
						<?php checked( ! empty( $settings['use_header'] ) ); ?>
					/>
					<?php echo esc_html__( 'Käytä Domio headeriä', 'domio' ); ?>
				</label>
				<p class="description">
					<?php echo esc_html__( 'Kun käytössä, teema pakottaa custom-headerin ja Elementorin header jätetään pois.', 'domio' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_phone"><?php echo esc_html__( 'Puhelin', 'domio' ); ?></label></th>
			<td>
				<input name="domio_theme_settings[phone]" type="text" id="domio_phone" value="<?php echo esc_attr( $settings['phone'] ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_email"><?php echo esc_html__( 'Sähköposti', 'domio' ); ?></label></th>
			<td>
				<input name="domio_theme_settings[email]" type="email" id="domio_email" value="<?php echo esc_attr( $settings['email'] ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_cta_text"><?php echo esc_html__( 'CTA-teksti', 'domio' ); ?></label></th>
			<td>
				<input name="domio_theme_settings[cta_text]" type="text" id="domio_cta_text" value="<?php echo esc_attr( $settings['cta_text'] ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="domio_cta_url"><?php echo esc_html__( 'CTA-linkki', 'domio' ); ?></label></th>
			<td>
				<input name="domio_theme_settings[cta_url]" type="url" id="domio_cta_url" value="<?php echo esc_attr( $settings['cta_url'] ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Jobs copy tab fields.
 *
 * @param array<string, mixed> $settings Settings.
 * @return void
 */
function domio_render_settings_jobs_fields( $settings ) {
	$copy   = isset( $settings['jobs_copy'] ) && is_array( $settings['jobs_copy'] ) ? $settings['jobs_copy'] : domio_get_default_jobs_copy();
	$fields = array(
		'archive_title'      => array(
			'label'       => __( 'Listauksen otsikko', 'domio' ),
			'type'        => 'text',
			'description' => __( 'H1 työpaikkalistauksessa.', 'domio' ),
		),
		'archive_lead'       => array(
			'label'       => __( 'Johdanto', 'domio' ),
			'type'        => 'textarea',
			'description' => __( 'Kappale otsikon alla.', 'domio' ),
		),
		'archive_empty'      => array(
			'label' => __( 'Tyhjä lista', 'domio' ),
			'type'  => 'text',
		),
		'archive_count_one'  => array(
			'label'       => __( 'Määrä, yksikkö', 'domio' ),
			'type'        => 'text',
			'description' => __( 'Käytä %d paikkana, esim. %d työpaikka.', 'domio' ),
		),
		'archive_count_many' => array(
			'label'       => __( 'Määrä, monikko', 'domio' ),
			'type'        => 'text',
			'description' => __( 'Käytä %d paikkana, esim. %d työpaikkaa.', 'domio' ),
		),
		'card_cta'           => array(
			'label' => __( '“Lue ilmoitus” -linkki', 'domio' ),
			'type'  => 'text',
		),
		'card_read_more'     => array(
			'label' => __( '“Lue lisää” -linkki', 'domio' ),
			'type'  => 'text',
		),
		'single_eyebrow'     => array(
			'label'       => __( 'Yksittäisen ilmoituksen yläotsikko', 'domio' ),
			'type'        => 'text',
			'description' => __( 'Linkki takaisin listaukseen.', 'domio' ),
		),
		'label_company'      => array(
			'label' => __( 'Kenttä: Yritys', 'domio' ),
			'type'  => 'text',
		),
		'label_location'     => array(
			'label' => __( 'Kenttä: Paikkakunta', 'domio' ),
			'type'  => 'text',
		),
		'label_salary'       => array(
			'label' => __( 'Kenttä: Palkka', 'domio' ),
			'type'  => 'text',
		),
		'label_type'         => array(
			'label' => __( 'Kenttä: Tyyppi', 'domio' ),
			'type'  => 'text',
		),
		'label_deadline'     => array(
			'label' => __( 'Kenttä: Viim. hakupäivä', 'domio' ),
			'type'  => 'text',
		),
		'badge_ended'        => array(
			'label' => __( 'Merkki: haku päättynyt', 'domio' ),
			'type'  => 'text',
		),
		'badge_last_day'     => array(
			'label' => __( 'Merkki: viimeinen päivä', 'domio' ),
			'type'  => 'text',
		),
		'badge_days_one'     => array(
			'label'       => __( 'Merkki: päivä jäljellä', 'domio' ),
			'type'        => 'text',
			'description' => __( 'Käytä %d paikkana, esim. %d päivä jäljellä.', 'domio' ),
		),
		'badge_days_many'    => array(
			'label'       => __( 'Merkki: päiviä jäljellä', 'domio' ),
			'type'        => 'text',
			'description' => __( 'Käytä %d paikkana, esim. %d päivää jäljellä.', 'domio' ),
		),
	);
	?>
	<table class="form-table" role="presentation">
		<?php foreach ( $fields as $key => $field ) : ?>
			<?php
			$value = isset( $copy[ $key ] ) ? $copy[ $key ] : '';
			$id    = 'domio_jobs_' . $key;
			$name  = 'domio_theme_settings[jobs_copy][' . $key . ']';
			?>
			<tr>
				<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
				<td>
					<?php if ( 'textarea' === $field['type'] ) : ?>
						<textarea name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" class="large-text" rows="4"><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input name="<?php echo esc_attr( $name ); ?>" type="text" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
					<?php endif; ?>
					<?php if ( ! empty( $field['description'] ) ) : ?>
						<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/**
 * Render related-links repeater fields.
 *
 * @param array<int, array<string, mixed>> $groups Groups.
 * @return void
 */
function domio_render_related_links_fields( $groups ) {
	if ( ! is_array( $groups ) ) {
		$groups = array();
	}
	?>
	<style>
		.domio-related-admin__group {
			margin: 0 0 1.25rem;
			padding: 1rem 1.1rem 1.1rem;
			background: #fff;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			max-width: 52rem;
		}
		.domio-related-admin__group-head {
			display: flex;
			gap: 0.75rem;
			align-items: flex-end;
			margin-bottom: 0.85rem;
		}
		.domio-related-admin__group-head label {
			flex: 1;
			font-weight: 600;
		}
		.domio-related-admin__group-head input[type="text"] {
			width: 100%;
			margin-top: 0.25rem;
		}
		.domio-related-admin__link {
			display: grid;
			grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr) auto;
			gap: 0.5rem;
			align-items: center;
			margin-bottom: 0.5rem;
		}
		.domio-related-admin__actions {
			display: flex;
			gap: 0.5rem;
			margin-top: 0.75rem;
		}
	</style>
	<div class="domio-related-admin" id="domio-related-admin">
		<div class="domio-related-admin__groups" data-groups>
			<?php foreach ( $groups as $group_index => $group ) : ?>
				<?php
				$group_id    = isset( $group['id'] ) ? $group['id'] : '';
				$group_title = isset( $group['title'] ) ? $group['title'] : '';
				$links       = isset( $group['links'] ) && is_array( $group['links'] ) ? $group['links'] : array();
				?>
				<div class="domio-related-admin__group" data-group>
					<input type="hidden" name="domio_theme_settings[related_link_groups][<?php echo esc_attr( (string) $group_index ); ?>][id]" value="<?php echo esc_attr( $group_id ); ?>" />
					<div class="domio-related-admin__group-head">
						<label>
							<?php echo esc_html__( 'Ryhmän otsikko', 'domio' ); ?>
							<input
								type="text"
								name="domio_theme_settings[related_link_groups][<?php echo esc_attr( (string) $group_index ); ?>][title]"
								value="<?php echo esc_attr( $group_title ); ?>"
							/>
						</label>
					</div>
					<div data-links>
						<?php foreach ( $links as $link_index => $link ) : ?>
							<?php
							$link_id    = isset( $link['id'] ) ? $link['id'] : '';
							$link_label = isset( $link['label'] ) ? $link['label'] : '';
							$link_url   = isset( $link['url'] ) ? $link['url'] : '';
							?>
							<div class="domio-related-admin__link" data-link>
								<input type="hidden" name="domio_theme_settings[related_link_groups][<?php echo esc_attr( (string) $group_index ); ?>][links][<?php echo esc_attr( (string) $link_index ); ?>][id]" value="<?php echo esc_attr( $link_id ); ?>" />
								<input
									type="text"
									name="domio_theme_settings[related_link_groups][<?php echo esc_attr( (string) $group_index ); ?>][links][<?php echo esc_attr( (string) $link_index ); ?>][label]"
									value="<?php echo esc_attr( $link_label ); ?>"
									placeholder="<?php echo esc_attr__( 'Linkin teksti', 'domio' ); ?>"
								/>
								<input
									type="url"
									name="domio_theme_settings[related_link_groups][<?php echo esc_attr( (string) $group_index ); ?>][links][<?php echo esc_attr( (string) $link_index ); ?>][url]"
									value="<?php echo esc_attr( $link_url ); ?>"
									placeholder="<?php echo esc_attr__( 'URL', 'domio' ); ?>"
								/>
								<button type="button" class="button-link-delete" data-remove-link><?php echo esc_html__( 'Poista', 'domio' ); ?></button>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="domio-related-admin__actions">
						<button type="button" class="button" data-add-link><?php echo esc_html__( 'Lisää linkki', 'domio' ); ?></button>
						<button type="button" class="button-link-delete" data-remove-group><?php echo esc_html__( 'Poista ryhmä', 'domio' ); ?></button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<p>
			<button type="button" class="button" data-add-group><?php echo esc_html__( 'Lisää ryhmä', 'domio' ); ?></button>
		</p>
	</div>
	<template id="domio-related-group-tpl">
		<div class="domio-related-admin__group" data-group>
			<input type="hidden" data-name="id" value="" />
			<div class="domio-related-admin__group-head">
				<label>
					<?php echo esc_html__( 'Ryhmän otsikko', 'domio' ); ?>
					<input type="text" data-name="title" value="" />
				</label>
			</div>
			<div data-links></div>
			<div class="domio-related-admin__actions">
				<button type="button" class="button" data-add-link><?php echo esc_html__( 'Lisää linkki', 'domio' ); ?></button>
				<button type="button" class="button-link-delete" data-remove-group><?php echo esc_html__( 'Poista ryhmä', 'domio' ); ?></button>
			</div>
		</div>
	</template>
	<template id="domio-related-link-tpl">
		<div class="domio-related-admin__link" data-link>
			<input type="hidden" data-name="id" value="" />
			<input type="text" data-name="label" placeholder="<?php echo esc_attr__( 'Linkin teksti', 'domio' ); ?>" />
			<input type="url" data-name="url" placeholder="<?php echo esc_attr__( 'URL', 'domio' ); ?>" />
			<button type="button" class="button-link-delete" data-remove-link><?php echo esc_html__( 'Poista', 'domio' ); ?></button>
		</div>
	</template>
	<script>
	(function () {
		const root = document.getElementById('domio-related-admin');
		if (!root) return;
		const groupsWrap = root.querySelector('[data-groups]');
		const groupTpl = document.getElementById('domio-related-group-tpl');
		const linkTpl = document.getElementById('domio-related-link-tpl');
		const prefix = 'domio_theme_settings[related_link_groups]';

		const bindNames = (groupEl, groupIndex) => {
			groupEl.querySelectorAll('[data-name]').forEach((input) => {
				const key = input.getAttribute('data-name');
				if (key === 'id' || key === 'title') {
					input.setAttribute('name', prefix + '[' + groupIndex + '][' + key + ']');
				}
			});
			groupEl.querySelectorAll('[data-links] [data-link]').forEach((linkEl, linkIndex) => {
				linkEl.querySelectorAll('[data-name]').forEach((input) => {
					const key = input.getAttribute('data-name');
					input.setAttribute('name', prefix + '[' + groupIndex + '][links][' + linkIndex + '][' + key + ']');
				});
			});
		};

		const reindex = () => {
			groupsWrap.querySelectorAll('[data-group]').forEach((groupEl, groupIndex) => {
				bindNames(groupEl, groupIndex);
			});
		};

		const addLink = (groupEl) => {
			const links = groupEl.querySelector('[data-links]');
			links.appendChild(linkTpl.content.cloneNode(true));
			reindex();
		};

		root.addEventListener('click', (event) => {
			const addGroup = event.target.closest('[data-add-group]');
			const addLinkBtn = event.target.closest('[data-add-link]');
			const removeLink = event.target.closest('[data-remove-link]');
			const removeGroup = event.target.closest('[data-remove-group]');

			if (addGroup) {
				groupsWrap.appendChild(groupTpl.content.cloneNode(true));
				const last = groupsWrap.querySelector('[data-group]:last-child');
				addLink(last);
			} else if (addLinkBtn) {
				addLink(addLinkBtn.closest('[data-group]'));
			} else if (removeLink) {
				removeLink.closest('[data-link]').remove();
				reindex();
			} else if (removeGroup) {
				removeGroup.closest('[data-group]').remove();
				reindex();
			}
		});
	})();
	</script>
	<?php
}
