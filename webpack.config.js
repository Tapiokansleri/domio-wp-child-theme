/**
 * Webpack config for Domio theme blocks and editor plugins.
 *
 * @package Domio
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const defaultEntry = defaultConfig.entry;

module.exports = {
	...defaultConfig,
	entry: () => {
		const entries =
			typeof defaultEntry === 'function' ? defaultEntry() : defaultEntry;

		return {
			...entries,
			'editor/landing-preload': path.resolve(
				__dirname,
				'src/editor/landing-preload/index.js'
			),
		};
	},
};
