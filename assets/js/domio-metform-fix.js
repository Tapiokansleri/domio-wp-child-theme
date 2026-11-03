/**
 * MetForm submit fix (inc/metform.php).
 */
( function () {
	// MetForm validates a field when it loses focus and keeps the old error until then. When the last field
	// is the e-mail (the calculator prefills the message), the press on the submit button blurs it, the error
	// line disappears, the button moves up and the click never lands: nothing is sent and nothing is shown.
	// Enter in that field fails the same way. So: let the field validate first, then press the button once.
	var html = document.documentElement;
	var SUBMIT = 'button[type="submit"], input[type="submit"]';
	var swallow = false;

	function submitAfterBlur( field, button ) {
		field.blur();
		swallow = true;
		window.setTimeout( function () {
			swallow = false;
			button.click();
		}, 150 );
	}

	function isMetForm( form ) {
		return !! ( form && form.closest && form.closest( '.mf-form-wrapper' ) );
	}

	html.addEventListener( 'mousedown', function ( event ) {
		var button = event.target.closest && event.target.closest( SUBMIT );
		var active = document.activeElement;
		if ( ! button || ! isMetForm( button.form ) || ! active || ! /^(INPUT|TEXTAREA|SELECT)$/.test( active.tagName ) || active.form !== button.form ) {
			return;
		}
		event.preventDefault();
		submitAfterBlur( active, button );
	}, true );

	html.addEventListener( 'keydown', function ( event ) {
		var field = event.target;
		if ( event.key !== 'Enter' || event.isComposing || field.tagName !== 'INPUT' || ! isMetForm( field.form ) ) {
			return;
		}
		var button = field.form.querySelector( SUBMIT );
		if ( ! button ) {
			return;
		}
		event.preventDefault();
		submitAfterBlur( field, button );
	}, true );

	// The visitor's own click, if it still lands on the button, would submit a second time.
	html.addEventListener( 'click', function ( event ) {
		if ( swallow && event.isTrusted ) {
			event.preventDefault();
			event.stopPropagation();
		}
	}, true );
} )();
