<?php
/**
 * Domio contacts block render.
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

$heading         = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$service_hours   = isset( $attributes['serviceHours'] ) ? $attributes['serviceHours'] : '';
$service_phone   = isset( $attributes['servicePhone'] ) ? $attributes['servicePhone'] : '';
$urgent_heading  = isset( $attributes['urgentHeading'] ) ? $attributes['urgentHeading'] : '';
$service_blocks  = isset( $attributes['serviceBlocks'] ) && is_array( $attributes['serviceBlocks'] ) ? $attributes['serviceBlocks'] : array();
$service_note    = isset( $attributes['serviceNote'] ) ? $attributes['serviceNote'] : '';

if ( empty( $service_blocks ) ) {
	$urgent_heading = $urgent_heading ? $urgent_heading : __( 'Akuutti asia? Soita, me hoidamme.', 'domio' );
	$service_blocks = array(
		array(
			'id'          => 'espoo-oncall',
			'title'       => __( 'Espoon 24/7 päivystys', 'domio' ),
			'phone'       => '040 665 6598',
			'email'       => '',
			'description' => __( 'Akuutit vikatilanteet ja ovenavaukset vuorokauden ympäri.', 'domio' ),
		),
		array(
			'id'          => 'customer-service',
			'title'       => __( 'Asiakaspalvelu', 'domio' ),
			'phone'       => $service_phone ? $service_phone : '040 630 5536',
			'email'       => 'asiakaspalvelu@domio.fi',
			'description' => '',
		),
	);
	$service_note = $service_note ? $service_note : __( 'Asutko espoolaisessa taloyhtiössä, jonka huoltoyhtiö on joku muu? Löydät oikean päivystysnumeron taloyhtiösi porrastaulusta tai isännöitsijältä.', 'domio' );
}
$areas           = isset( $attributes['areas'] ) && is_array( $attributes['areas'] ) ? $attributes['areas'] : array();
$people          = isset( $attributes['people'] ) && is_array( $attributes['people'] ) ? $attributes['people'] : array();
$invoice_heading = isset( $attributes['invoiceHeading'] ) ? $attributes['invoiceHeading'] : '';
$invoice_rows    = isset( $attributes['invoiceRows'] ) && is_array( $attributes['invoiceRows'] ) ? $attributes['invoiceRows'] : array();

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-contacts ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);

$map_pin = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.05 7-12a7 7 0 1 0-14 0c0 5.95 7 12 7 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"></circle></svg>';

/**
 * Build a tel: href from a Finnish display number.
 *
 * @param string $phone Display phone.
 * @return string
 */
$domio_tel = static function ( $phone ) {
	if ( function_exists( 'domio_tel_href' ) ) {
		return domio_tel_href( $phone );
	}

	$digits = preg_replace( '/\D+/', '', (string) $phone );
	if ( '' === $digits ) {
		return '';
	}
	if ( 0 === strpos( $digits, '358' ) ) {
		return 'tel:+' . $digits;
	}
	if ( 0 === strpos( $digits, '0' ) ) {
		return 'tel:+358' . substr( $digits, 1 );
	}
	return 'tel:' . $digits;
};
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-contacts__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-contacts__title"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<div class="domio-contacts__columns">
			<div class="domio-contacts__aside">
				<?php if ( ! empty( $areas ) ) : ?>
					<div class="domio-contacts__addresses">
						<?php foreach ( $areas as $area ) : ?>
							<?php
							if ( ! is_array( $area ) ) {
								continue;
							}

							$label     = isset( $area['label'] ) ? $area['label'] : '';
							$street    = isset( $area['street'] ) ? $area['street'] : '';
							$city      = isset( $area['city'] ) ? $area['city'] : '';
							$map_query = isset( $area['mapQuery'] ) ? $area['mapQuery'] : '';

							if ( '' === $street && '' === $city ) {
								continue;
							}

							if ( '' === $map_query ) {
								$map_query = trim( $street . ', ' . $city, ' ,' );
							}

							$map_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $map_query );
							$aria    = sprintf(
								/* translators: %s: street address */
								__( 'Avaa %s Google Mapsissa', 'domio' ),
								$street ? $street : $city
							);
							?>
							<div class="domio-contacts__addr">
								<a
									class="domio-contacts__map-link"
									href="<?php echo esc_url( $map_url ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $aria ); ?>"
								>
									<span class="domio-contacts__map-icon" aria-hidden="true">
										<?php echo $map_pin; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
									<span class="domio-contacts__map-copy">
										<?php if ( $label ) : ?>
											<strong><?php echo esc_html( $label ); ?></strong>
										<?php elseif ( $street ) : ?>
											<strong><?php echo esc_html( $street ); ?></strong>
										<?php endif; ?>
										<?php if ( $label && ( $street || $city ) ) : ?>
											<span><?php echo esc_html( trim( $street . ', ' . $city, ' ,' ) ); ?></span>
										<?php elseif ( $city ) : ?>
											<span><?php echo esc_html( $city ); ?></span>
										<?php endif; ?>
									</span>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php
				$has_services = $urgent_heading || ! empty( $service_blocks ) || $service_note;
				if ( ! $has_services && ( $service_hours || $service_phone ) ) {
					$has_services = true;
				}
				?>
				<?php if ( $has_services ) : ?>
					<div class="domio-contacts__services">
						<?php if ( $urgent_heading ) : ?>
							<p class="domio-contacts__urgent-heading"><?php echo esc_html( $urgent_heading ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $service_blocks ) ) : ?>
							<div class="domio-contacts__service-blocks">
								<?php foreach ( $service_blocks as $block ) : ?>
									<?php
									if ( ! is_array( $block ) ) {
										continue;
									}

									$block_title = isset( $block['title'] ) ? $block['title'] : '';
									$block_phone = isset( $block['phone'] ) ? $block['phone'] : '';
									$block_email = isset( $block['email'] ) ? $block['email'] : '';
									$block_desc  = isset( $block['description'] ) ? $block['description'] : '';

									if ( ! $block_title && ! $block_phone && ! $block_email && ! $block_desc ) {
										continue;
									}
									?>
									<div class="domio-contacts__service">
										<?php if ( $block_phone || $block_email ) : ?>
											<p class="domio-contacts__service-contact">
												<?php if ( $block_phone ) : ?>
													<a href="<?php echo esc_url( $domio_tel( $block_phone ), array( 'tel' ) ); ?>"><?php echo esc_html( $block_phone ); ?></a>
													<?php if ( $block_email ) : ?>
														<br>
													<?php endif; ?>
												<?php endif; ?>
												<?php if ( $block_email ) : ?>
													<a href="<?php echo esc_url( 'mailto:' . $block_email ); ?>"><?php echo esc_html( $block_email ); ?></a>
												<?php endif; ?>
											</p>
										<?php endif; ?>
										<?php if ( $block_desc ) : ?>
											<p class="domio-contacts__service-desc"><?php echo esc_html( $block_desc ); ?></p>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php elseif ( $service_hours || $service_phone ) : ?>
							<p class="domio-contacts__sub">
								<?php echo esc_html( $service_hours ); ?>
								<?php if ( $service_phone ) : ?>
									<a href="<?php echo esc_url( $domio_tel( $service_phone ), array( 'tel' ) ); ?>">
										<?php echo esc_html( $service_phone ); ?>
									</a>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( $service_note ) : ?>
							<p class="domio-contacts__service-note"><?php echo esc_html( $service_note ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$has_people = false;
			foreach ( $people as $person ) {
				if ( is_array( $person ) ) {
					$has_people = true;
					break;
				}
			}
			?>
			<?php if ( $has_people ) : ?>
				<div class="domio-contacts__people">
					<?php foreach ( $people as $person ) : ?>
						<?php
						if ( ! is_array( $person ) ) {
							continue;
						}

						$section    = isset( $person['section'] ) ? $person['section'] : '';
						$name       = isset( $person['name'] ) ? $person['name'] : '';
						$role       = isset( $person['role'] ) ? $person['role'] : '';
						$phone      = isset( $person['phone'] ) ? $person['phone'] : '';
						$email      = isset( $person['email'] ) ? $person['email'] : '';
						$image_html = '';

						if ( function_exists( 'domio_get_contact_person_image' ) ) {
							$image_html = domio_get_contact_person_image( $person );
						}

						if ( ! $section && ! $name && ! $role && ! $phone && ! $email && ! $image_html ) {
							continue;
						}
						?>
						<div class="domio-contacts__person">
							<?php if ( $image_html ) : ?>
								<div class="domio-contacts__person-media">
									<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<div class="domio-contacts__person-body">
								<?php if ( $name ) : ?>
									<p class="domio-contacts__name"><?php echo esc_html( $name ); ?></p>
								<?php endif; ?>
								<?php if ( $role ) : ?>
									<p class="domio-contacts__role"><?php echo esc_html( $role ); ?></p>
								<?php endif; ?>
								<?php if ( $phone || $email ) : ?>
									<p class="domio-contacts__contact">
										<?php if ( $phone ) : ?>
											<a href="<?php echo esc_url( $domio_tel( $phone ), array( 'tel' ) ); ?>"><?php echo esc_html( $phone ); ?></a>
											<?php if ( $email ) : ?>
												<br>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ( $email ) : ?>
											<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $invoice_heading || ! empty( $invoice_rows ) ) : ?>
			<div class="domio-contacts__billing">
				<?php if ( ! empty( $invoice_rows ) ) : ?>
					<div class="domio-contacts__invoice">
						<?php foreach ( $invoice_rows as $row ) : ?>
							<?php
							if ( ! is_array( $row ) ) {
								continue;
							}
							$label = isset( $row['label'] ) ? $row['label'] : '';
							$value = isset( $row['value'] ) ? $row['value'] : '';
							$href  = isset( $row['href'] ) ? $row['href'] : '';
							if ( '' === $label && '' === $value ) {
								continue;
							}
							?>
							<div class="domio-contacts__inv-row">
								<span class="domio-contacts__inv-label"><?php echo esc_html( $label ); ?></span>
								<span class="domio-contacts__inv-value">
									<?php if ( $href ) : ?>
										<a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $value ); ?></a>
									<?php else : ?>
										<?php echo nl2br( esc_html( $value ) ); ?>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
