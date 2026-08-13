/**
 * Domio hintalaskuri block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { background, pattern, patternOpacity } = attributes;

	const blockProps = useBlockProps( {
		className: `domio-hintalaskuri-block alignfull ${ getSectionClasses(
			background,
			pattern
		) }`,
		style: getPatternStyle( patternOpacity ),
	} );

	return (
		<>
			<DomioTemplateNotice />
			<BackgroundControls
				value={ background }
				onChange={ ( value ) => setAttributes( { background: value } ) }
				pattern={ pattern }
				onPatternChange={ ( value ) => setAttributes( { pattern: value } ) }
				patternOpacity={ patternOpacity }
				onPatternOpacityChange={ ( value ) =>
					setAttributes( { patternOpacity: value } )
				}
			/>
			<section { ...blockProps }>
				<div className="domio-hintalaskuri-block__inner">
					<div className="domio-hintalaskuri-block__placeholder">
						{ __(
							'Hintalaskuri ([domio_hintalaskuri]) näkyy julkaistulla sivulla.',
							'domio'
						) }
					</div>
				</div>
			</section>
		</>
	);
}
