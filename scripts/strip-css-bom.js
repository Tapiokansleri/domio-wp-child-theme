/**
 * Removes the UTF-8 BOM that Sass puts at the start of compressed CSS containing non-ASCII characters
 * (faq "–", hero "»", trustmary "★"). WordPress inlines small block stylesheets into a <style> tag, where
 * the BOM becomes part of the first selector and that whole rule is dropped (the hero lost its colour
 * variables on the blog index). Runs after `npm run build` (postbuild).
 */
const fs = require( 'fs' );
const path = require( 'path' );

function walk( dir ) {
	for ( const entry of fs.readdirSync( dir, { withFileTypes: true } ) ) {
		const file = path.join( dir, entry.name );
		if ( entry.isDirectory() ) {
			walk( file );
		} else if ( file.endsWith( '.css' ) ) {
			const buffer = fs.readFileSync( file );
			if ( buffer[ 0 ] === 0xef && buffer[ 1 ] === 0xbb && buffer[ 2 ] === 0xbf ) {
				fs.writeFileSync( file, buffer.subarray( 3 ) );
				console.log( 'stripped BOM:', path.relative( process.cwd(), file ) );
			}
		}
	}
}

walk( path.join( __dirname, '..', 'build' ) );
