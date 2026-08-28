<?php
/**
 * Domio site header markup.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = domio_get_settings();
$phone      = isset( $settings['phone'] ) ? $settings['phone'] : '';
$email      = isset( $settings['email'] ) ? $settings['email'] : '';
$cta_text   = isset( $settings['cta_text'] ) ? $settings['cta_text'] : __( 'Ota yhteyttä', 'domio' );
$cta_url    = isset( $settings['cta_url'] ) ? $settings['cta_url'] : '';
$phone_href = $phone ? domio_phone_href( $phone ) : '';
$menu_id    = domio_get_primary_menu_id();
?>
<header class="domio-header" data-domio-header>
	<div class="domio-header__top">
		<div class="domio-header__shell">
			<ul class="domio-header__contacts">
				<?php if ( $phone && $phone_href ) : ?>
					<li>
						<a class="domio-header__contact" href="<?php echo esc_url( $phone_href ); ?>">
							<span class="domio-header__contact-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.1-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.1a2 2 0 012.1-.4c.8.3 1.7.5 2.6.6a2 2 0 011.7 2z"/></svg>
							</span>
							<span><?php echo esc_html( $phone ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li>
						<a class="domio-header__contact" href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>">
							<span class="domio-header__contact-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/><path d="M22 6l-10 7L2 6"/></svg>
							</span>
							<span><?php echo esc_html( antispambot( $email ) ); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="domio-header__main">
		<div class="domio-header__shell domio-header__main-inner">
			<a class="domio-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo domio_get_header_logo_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built/escaped in helper. ?>
			</a>

			<button
				type="button"
				class="domio-header__toggle"
				data-domio-nav-toggle
				aria-expanded="false"
				aria-controls="domio-header-nav"
			>
				<span class="screen-reader-text"><?php echo esc_html__( 'Avaa valikko', 'domio' ); ?></span>
				<span class="domio-header__toggle-bars" aria-hidden="true"></span>
			</button>
			<script>
			/* domio-nav-boot: open mobile menu before deferred header.js loads */
			(function(){var h=document.querySelector("[data-domio-header]");if(!h||h.dataset.domioNavBound)return;var t=h.querySelector("[data-domio-nav-toggle]");if(!t)return;h.dataset.domioNavBound="1";t.addEventListener("click",function(){var o=!h.classList.contains("is-nav-open");h.classList.toggle("is-nav-open",o);t.setAttribute("aria-expanded",o?"true":"false");document.body.classList.toggle("domio-nav-lock",o);if(!o){h.querySelectorAll(".menu-item-has-children.is-sub-open").forEach(function(i){i.classList.remove("is-sub-open");});}});})();
			</script>

			<nav id="domio-header-nav" class="domio-header__nav" data-domio-nav aria-label="<?php echo esc_attr__( 'Päävalikko', 'domio' ); ?>">
				<?php
				if ( $menu_id ) {
					wp_nav_menu(
						array(
							'menu'            => $menu_id,
							'container'       => false,
							'menu_class'      => 'domio-header__menu',
							'depth'           => 3,
							'fallback_cb'     => false,
							'item_spacing'    => 'discard',
						)
					);
				} elseif ( has_nav_menu( 'domio-primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location'  => 'domio-primary',
							'container'       => false,
							'menu_class'      => 'domio-header__menu',
							'depth'           => 3,
							'fallback_cb'     => false,
							'item_spacing'    => 'discard',
						)
					);
				}
				?>

				<?php if ( $cta_text && $cta_url ) : ?>
					<a class="domio-header__cta domio-header__cta--mobile" href="<?php echo esc_url( $cta_url ); ?>">
						<?php echo esc_html( $cta_text ); ?>
					</a>
				<?php endif; ?>
			</nav>

			<?php if ( $cta_text && $cta_url ) : ?>
				<a class="domio-header__cta domio-header__cta--desktop" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_text ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>
