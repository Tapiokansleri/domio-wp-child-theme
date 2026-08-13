/**
 * Domio Related Links block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ExternalLink, Notice } from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @return {Array} Central related-link groups.
 */
function getGlobalGroups() {
	return (
		( typeof window !== 'undefined' &&
			window.domioBlockDefaults &&
			window.domioBlockDefaults.relatedLinkGroups ) ||
		[]
	);
}

/**
 * @return {string} Theme settings admin URL.
 */
function getSettingsUrl() {
	return (
		( typeof window !== 'undefined' &&
			window.domioBlockDefaults &&
			window.domioBlockDefaults.settingsUrl ) ||
		''
	);
}

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, background, pattern, patternOpacity } = attributes;
	const groups = getGlobalGroups();
	const settingsUrl = getSettingsUrl();

	const blockProps = useBlockProps( {
		className: `domio-related-links ${ getSectionClasses( background, pattern ) }`,
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
				<PanelBody
					title={ __( 'Linkit', 'domio' ) }
					initialOpen={ true }
				>
					<Notice status="info" isDismissible={ false }>
						{ __(
							'Linkit hallitaan keskitetysti. Muutos päivittyy kaikille ländäreille.',
							'domio'
						) }
					</Notice>
					{ settingsUrl ? (
						<p style={ { marginTop: '0.75rem' } }>
							<ExternalLink href={ settingsUrl }>
								{ __(
									'Muokkaa linkkejä teeman asetuksissa',
									'domio'
								) }
							</ExternalLink>
						</p>
					) : null }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-related-links__inner">
					<RichText
						tagName="h2"
						className="domio-related-links__heading"
						value={ heading }
						onChange={ ( value ) =>
							setAttributes( { heading: value } )
						}
						placeholder={ __( 'Tutustu myös', 'domio' ) }
						allowedFormats={ [] }
					/>

					{ groups.length > 0 ? (
						<div className="domio-related-links__grid">
							{ groups.map( ( group, groupIndex ) => (
								<div
									className="domio-related-links__group"
									key={ group.id || groupIndex }
								>
									{ group.title ? (
										<h3 className="domio-related-links__group-title">
											{ group.title }
										</h3>
									) : null }
									<ul className="domio-related-links__list">
										{ ( group.links || [] ).map(
											( link, linkIndex ) => (
												<li
													className="domio-related-links__item"
													key={
														link.id || linkIndex
													}
												>
													<span className="domio-related-links__link">
														{ link.label ||
															__(
																'Linkin teksti…',
																'domio'
															) }
														<span
															className="domio-related-links__arrow"
															aria-hidden="true"
														>
															→
														</span>
													</span>
												</li>
											)
										) }
									</ul>
								</div>
							) ) }
						</div>
					) : (
						<p className="domio-related-links-editor__empty">
							{ __(
								'Lisää linkit kohdassa Ulkoasu → Teeman asetukset.',
								'domio'
							) }
						</p>
					) }
				</div>
			</section>
		</>
	);
}
