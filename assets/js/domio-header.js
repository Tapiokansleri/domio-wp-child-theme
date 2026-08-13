/**
 * Domio header interactions: mobile nav, submenus, scrolled state.
 */
( function () {
	const header = document.querySelector( '[data-domio-header]' );
	if ( ! header ) {
		return;
	}

	const toggle = header.querySelector( '[data-domio-nav-toggle]' );
	const nav = header.querySelector( '[data-domio-nav]' );
	const mobileQuery = window.matchMedia( '(max-width: 980px)' );

	const setOpen = ( open ) => {
		header.classList.toggle( 'is-nav-open', open );
		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
		document.body.classList.toggle( 'domio-nav-lock', open );
		if ( ! open ) {
			closeAllSubs();
		}
	};

	const closeAllSubs = ( exceptItem ) => {
		header.querySelectorAll( '.menu-item-has-children.is-sub-open' ).forEach( ( item ) => {
			if ( exceptItem && ( item === exceptItem || item.contains( exceptItem ) ) ) {
				return;
			}
			resetSub( item );
		} );
	};

	const resetSub = ( item ) => {
		item.classList.remove( 'is-sub-open' );
		const btn = item.querySelector( ':scope > .domio-header__sub-toggle' );
		if ( btn ) {
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', 'Avaa alavalikko' );
		}
	};

	const closeDescendantSubs = ( item ) => {
		item.querySelectorAll( ':scope .menu-item-has-children.is-sub-open' ).forEach( resetSub );
	};

	const closeSiblingSubs = ( item ) => {
		const parentList = item.parentElement;
		if ( ! parentList ) {
			return;
		}
		Array.from( parentList.children ).forEach( ( sibling ) => {
			if ( sibling === item || ! sibling.classList.contains( 'is-sub-open' ) ) {
				return;
			}
			resetSub( sibling );
			closeDescendantSubs( sibling );
		} );
	};

	const setSubOpen = ( item, open ) => {
		if ( open ) {
			closeSiblingSubs( item );
		} else {
			closeDescendantSubs( item );
		}
		item.classList.toggle( 'is-sub-open', open );
		const btn = item.querySelector( ':scope > .domio-header__sub-toggle' );
		if ( btn ) {
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			btn.setAttribute( 'aria-label', open ? 'Sulje alavalikko' : 'Avaa alavalikko' );
		}
	};

	const enhanceSubmenus = () => {
		if ( ! nav ) {
			return;
		}

		nav.querySelectorAll( '.menu-item-has-children' ).forEach( ( item, index ) => {
			if ( item.querySelector( ':scope > .domio-header__sub-toggle' ) ) {
				return;
			}

			const link = item.querySelector( ':scope > a' );
			const submenu = item.querySelector( ':scope > .sub-menu' );
			if ( ! link || ! submenu ) {
				return;
			}

			const submenuId = 'domio-sub-' + index;
			submenu.id = submenuId;

			const btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'domio-header__sub-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-controls', submenuId );
			btn.setAttribute( 'aria-label', 'Avaa alavalikko' );
			btn.innerHTML = '<span aria-hidden="true"></span>';

			link.after( btn );

			btn.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				event.stopPropagation();
				const next = ! item.classList.contains( 'is-sub-open' );
				setSubOpen( item, next );
			} );
		} );
	};

	enhanceSubmenus();

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', () => {
			setOpen( ! header.classList.contains( 'is-nav-open' ) );
		} );

		document.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' ) {
				setOpen( false );
			}
		} );

		nav.addEventListener( 'click', ( event ) => {
			const link = event.target.closest( 'a' );
			if ( ! link || ! nav.contains( link ) || ! mobileQuery.matches ) {
				return;
			}

			const item = link.parentElement;
			if ( item && item.classList.contains( 'menu-item-has-children' ) ) {
				const href = link.getAttribute( 'href' ) || '';
				if ( href === '' || href === '#' ) {
					event.preventDefault();
					setSubOpen( item, ! item.classList.contains( 'is-sub-open' ) );
					return;
				}
			}

			setOpen( false );
		} );
	}

	if ( typeof mobileQuery.addEventListener === 'function' ) {
		mobileQuery.addEventListener( 'change', ( event ) => {
			if ( ! event.matches ) {
				setOpen( false );
			}
		} );
	}

	const onScroll = () => {
		header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
	};

	onScroll();
	window.addEventListener( 'scroll', onScroll, { passive: true } );
} )();
