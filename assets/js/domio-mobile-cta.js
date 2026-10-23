/**
 * Hide the floating "Laske hinta-arvio" button while the calculator or the quote form is on screen.
 */
( function () {
	var cta = document.querySelector( '[data-domio-mobile-cta]' );
	if ( ! cta || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var targets = document.querySelectorAll( '#hintalaskuri, .domio-cta--form' );
	if ( ! targets.length ) {
		return;
	}

	var onScreen = [];
	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				var index = onScreen.indexOf( entry.target );
				if ( entry.isIntersecting && index === -1 ) {
					onScreen.push( entry.target );
				} else if ( ! entry.isIntersecting && index !== -1 ) {
					onScreen.splice( index, 1 );
				}
			} );
			cta.classList.toggle( 'is-hidden', onScreen.length > 0 );
		},
		{ threshold: 0.15 }
	);

	Array.prototype.forEach.call( targets, function ( target ) {
		observer.observe( target );
	} );
} )();
