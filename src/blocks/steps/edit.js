/**
 * Domio Steps block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	Button,
	Flex,
	FlexItem,
} from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, steps, orientation, showNumbers, background, pattern, patternOpacity } =
		attributes;

	const blockProps = useBlockProps( {
		className: [
			'domio-steps',
			`domio-steps--${ orientation }`,
			showNumbers ? 'domio-steps--numbered' : 'domio-steps--plain',
			getSectionClasses( background, pattern ),
		].join( ' ' ),
		style: getPatternStyle( patternOpacity ),
	} );

	const updateStep = ( index, key, value ) => {
		const next = steps.map( ( step, i ) =>
			i === index ? { ...step, [ key ]: value } : step
		);
		setAttributes( { steps: next } );
	};

	const addStep = () => {
		setAttributes( {
			steps: [
				...steps,
				{
					id: `step-${ Date.now() }`,
					title: '',
					text: '',
					timeLabel: '',
				},
			],
		} );
	};

	const removeStep = ( index ) => {
		setAttributes( {
			steps: steps.filter( ( _, i ) => i !== index ),
		} );
	};

	const moveStep = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= steps.length ) {
			return;
		}
		const next = [ ...steps ];
		const [ item ] = next.splice( index, 1 );
		next.splice( target, 0, item );
		setAttributes( { steps: next } );
	};

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
				<PanelBody title={ __( 'Asettelu', 'domio' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Suunta', 'domio' ) }
						value={ orientation }
						options={ [
							{
								label: __( 'Vaakasuora', 'domio' ),
								value: 'horizontal',
							},
							{
								label: __( 'Pystysuora', 'domio' ),
								value: 'vertical',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { orientation: value } )
						}
						help={ __(
							'Mobiilissa näytetään aina pystysuorana.',
							'domio'
						) }
					/>
					<ToggleControl
						label={ __( 'Näytä numerot', 'domio' ) }
						checked={ showNumbers }
						onChange={ ( value ) =>
							setAttributes( { showNumbers: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Vaiheet', 'domio' ) } initialOpen={ true }>
					{ steps.map( ( step, index ) => (
						<div
							className="domio-steps-editor__item"
							key={ step.id || index }
						>
							<p className="domio-steps-editor__label">
								{ __( 'Vaihe', 'domio' ) } { index + 1 }
							</p>
							<TextControl
								label={ __( 'Aikaleima / kesto', 'domio' ) }
								value={ step.timeLabel || '' }
								onChange={ ( value ) =>
									updateStep( index, 'timeLabel', value )
								}
								placeholder={ __( 'Esim. 1–2 pv', 'domio' ) }
							/>
							<Flex gap={ 2 }>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === 0 }
										onClick={ () => moveStep( index, -1 ) }
									>
										{ __( 'Ylös', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === steps.length - 1 }
										onClick={ () => moveStep( index, 1 ) }
									>
										{ __( 'Alas', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										isDestructive
										onClick={ () => removeStep( index ) }
									>
										{ __( 'Poista', 'domio' ) }
									</Button>
								</FlexItem>
							</Flex>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addStep }>
						{ __( 'Lisää vaihe', 'domio' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-steps__inner">
					<RichText
						tagName="h2"
						className="domio-steps__heading"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						placeholder={ __( 'Osion otsikko…', 'domio' ) }
						allowedFormats={ [] }
					/>

					{ steps.length > 0 ? (
						<ol className="domio-steps__list">
							{ steps.map( ( step, index ) => (
								<li
									className="domio-steps__item"
									key={ step.id || index }
								>
									<div className="domio-steps__content">
										<RichText
											tagName="h3"
											className="domio-steps__title"
											value={ step.title || '' }
											onChange={ ( value ) =>
												updateStep( index, 'title', value )
											}
											placeholder={ __( 'Vaiheen otsikko…', 'domio' ) }
											allowedFormats={ [] }
										/>
										<RichText
											tagName="p"
											className="domio-steps__text"
											value={ step.text || '' }
											onChange={ ( value ) =>
												updateStep( index, 'text', value )
											}
											placeholder={ __( 'Vaiheen kuvaus…', 'domio' ) }
											allowedFormats={ [
												'core/bold',
												'core/italic',
											] }
										/>
										{ step.timeLabel ? (
											<span className="domio-steps__time">
												{ step.timeLabel }
											</span>
										) : null }
									</div>
								</li>
							) ) }
						</ol>
					) : (
						<p className="domio-steps-editor__empty">
							{ __( 'Lisää vaiheita sivupalkista.', 'domio' ) }
						</p>
					) }
				</div>
			</section>
		</>
	);
}
