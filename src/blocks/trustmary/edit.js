/**
 * Domio Trustmary block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { widgetSrc, background, pattern, patternOpacity } = attributes;

	const blockProps = useBlockProps( {
		className: `domio-trustmary alignfull ${ getSectionClasses(
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
			<InspectorControls>
				<PanelBody title={ __( 'Asetukset', 'domio' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Widgetin osoite', 'domio' ) }
						value={ widgetSrc }
						onChange={ ( value ) => setAttributes( { widgetSrc: value } ) }
						type="url"
						help={ __(
							'Trustmary-widgetin script-osoite. Widgetti tuo otsikon mukanaan.',
							'domio'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-trustmary__inner">
					<div className="domio-trustmary__placeholder">
						{ __(
							'Trustmary-arvostelut näkyvät julkaistulla sivulla.',
							'domio'
						) }
					</div>
				</div>
			</section>
		</>
	);
}
