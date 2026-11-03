/**
 * Exit-intent nudge (inc/exit-intent.php). Desktop only: a fine pointer and a wide window.
 */
( function () {
	var root = document.querySelector( '[data-domio-exit]' );
	if ( ! root || ! window.matchMedia || ! window.matchMedia( '(pointer: fine) and (min-width: 1024px)' ).matches ) {
		return;
	}

	// Listeners go on <html>, not document: WP Rocket's delayed JavaScript holds back document-level
	// mouse and key listeners until the visitor's first interaction.
	var html = document.documentElement;
	var SHOWN = 'domio_exit_shown';
	var ENGAGED = 'domio_exit_engaged';
	var ARM_AFTER_MS = 10000;
	var armed = false;
	var lastFocus = null;

	function stored( key ) {
		try {
			return window.sessionStorage.getItem( key ) === '1';
		} catch ( e ) {
			return false;
		}
	}

	function store( key ) {
		try {
			window.sessionStorage.setItem( key, '1' );
		} catch ( e ) {}
	}

	function track( name, params ) {
		if ( typeof window.gtag === 'function' ) {
			window.gtag( 'event', name, params || {} );
		}
	}

	if ( stored( SHOWN ) || stored( ENGAGED ) ) {
		return;
	}

	window.setTimeout( function () {
		armed = true;
	}, ARM_AFTER_MS );

	// Someone who already uses the calculator or a form does not need the nudge.
	html.addEventListener( 'focusin', function ( event ) {
		if ( event.target.closest && event.target.closest( '#hintalaskuri, .domio-form, form' ) ) {
			store( ENGAGED );
		}
	} );
	html.addEventListener( 'click', function ( event ) {
		var link = event.target.closest && event.target.closest( 'a[href]' );
		if ( event.target.closest && event.target.closest( '#hintalaskuri' ) ) {
			store( ENGAGED );
		} else if ( link && /\/(hintalaskuri|ota-yhteytta|tarjouspyynto)\//.test( link.getAttribute( 'href' ) || '' ) && ! link.closest( '[data-domio-exit]' ) ) {
			store( ENGAGED );
		}
	} );

	function close() {
		root.classList.remove( 'is-open' );
		html.removeEventListener( 'keydown', onKey );
		window.setTimeout( function () {
			root.hidden = true;
		}, 200 );
		if ( lastFocus && lastFocus.focus ) {
			lastFocus.focus();
		}
	}

	function onKey( event ) {
		if ( event.key === 'Escape' ) {
			close();
		}
	}

	function open() {
		store( SHOWN );
		lastFocus = document.activeElement;
		root.hidden = false;
		window.requestAnimationFrame( function () {
			root.classList.add( 'is-open' );
		} );
		root.querySelector( '.domio-exit__panel' ).focus( { preventScroll: true } );
		html.addEventListener( 'keydown', onKey );
		track( 'exit_intent_shown', { page_path: window.location.pathname } );
	}

	// The pointer leaves the page through the top edge (towards the tabs or the address bar).
	html.addEventListener( 'mouseleave', function ( event ) {
		if ( ! armed || event.clientY > 0 || stored( SHOWN ) || stored( ENGAGED ) ) {
			return;
		}
		open();
	} );

	root.querySelector( '[data-domio-exit-close]' ).addEventListener( 'click', close );
	root.querySelectorAll( '[data-domio-exit-cta]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			track( 'exit_intent_click', { cta: link.getAttribute( 'data-domio-exit-cta' ), page_path: window.location.pathname } );
			store( ENGAGED );
			if ( ( link.getAttribute( 'href' ) || '' ).charAt( 0 ) === '#' ) {
				close();
			}
		} );
	} );
} )();
