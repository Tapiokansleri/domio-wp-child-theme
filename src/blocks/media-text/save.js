/**
 * Domio Media Text block save — persist InnerBlocks content.
 */
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * @return {JSX.Element} Saved inner blocks.
 */
export default function save() {
	return <InnerBlocks.Content />;
}
