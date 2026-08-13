/**
 * Domio FAQ block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	Button,
	Flex,
	FlexItem,
	SelectControl,
} from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, items, defaultOpen, emitSchema, background, pattern, patternOpacity } =
		attributes;

	const blockProps = useBlockProps( {
		className: `domio-faq ${ getSectionClasses( background, pattern ) }`,
		style: getPatternStyle( patternOpacity ),
	} );

	const updateItem = ( index, key, value ) => {
		const next = items.map( ( item, i ) =>
			i === index ? { ...item, [ key ]: value } : item
		);
		setAttributes( { items: next } );
	};

	const addItem = () => {
		setAttributes( {
			items: [
				...items,
				{
					id: `faq-${ Date.now() }`,
					question: '',
					answer: '',
				},
			],
		} );
	};

	const removeItem = ( index ) => {
		const next = items.filter( ( _, i ) => i !== index );
		let nextDefault = defaultOpen;
		if ( defaultOpen === index ) {
			nextDefault = -1;
		} else if ( defaultOpen > index ) {
			nextDefault = defaultOpen - 1;
		}
		setAttributes( { items: next, defaultOpen: nextDefault } );
	};

	const moveItem = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= items.length ) {
			return;
		}
		const next = [ ...items ];
		const [ item ] = next.splice( index, 1 );
		next.splice( target, 0, item );

		let nextDefault = defaultOpen;
		if ( defaultOpen === index ) {
			nextDefault = target;
		} else if ( defaultOpen === target ) {
			nextDefault = index;
		}

		setAttributes( { items: next, defaultOpen: nextDefault } );
	};

	const openOptions = [
		{ label: __( 'Ei mitään auki', 'domio' ), value: '-1' },
		...items.map( ( item, index ) => ( {
			label: item.question
				? `${ index + 1 }. ${ item.question }`
				: `${ __( 'Kysymys', 'domio' ) } ${ index + 1 }`,
			value: String( index ),
		} ) ),
	];

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
					<SelectControl
						label={ __( 'Oletuksena auki', 'domio' ) }
						value={ String( defaultOpen ) }
						options={ openOptions }
						onChange={ ( value ) =>
							setAttributes( { defaultOpen: parseInt( value, 10 ) } )
						}
					/>
					<ToggleControl
						label={ __( 'Lisää FAQ-skeemaan', 'domio' ) }
						checked={ emitSchema }
						onChange={ ( value ) =>
							setAttributes( { emitSchema: value } )
						}
						help={ __(
							'Kerää kysymykset sivun FAQPage-skeemaan (ei tulosta JSON-LD:tä lohkossa).',
							'domio'
						) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Kysymykset', 'domio' ) } initialOpen={ true }>
					{ items.map( ( item, index ) => (
						<div
							className="domio-faq-editor__item"
							key={ item.id || index }
						>
							<p className="domio-faq-editor__label">
								{ __( 'Kysymys', 'domio' ) } { index + 1 }
							</p>
							<TextControl
								label={ __( 'Kysymysteksti (sivupalkki)', 'domio' ) }
								value={ item.question || '' }
								onChange={ ( value ) =>
									updateItem( index, 'question', value )
								}
								help={ __(
									'Voit muokata myös suoraan lohkossa.',
									'domio'
								) }
							/>
							<Flex gap={ 2 }>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === 0 }
										onClick={ () => moveItem( index, -1 ) }
									>
										{ __( 'Ylös', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === items.length - 1 }
										onClick={ () => moveItem( index, 1 ) }
									>
										{ __( 'Alas', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										isDestructive
										onClick={ () => removeItem( index ) }
									>
										{ __( 'Poista', 'domio' ) }
									</Button>
								</FlexItem>
							</Flex>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addItem }>
						{ __( 'Lisää kysymys', 'domio' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-faq__inner">
					<RichText
						tagName="h2"
						className="domio-faq__heading"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						placeholder={ __( 'Osion otsikko…', 'domio' ) }
						allowedFormats={ [] }
					/>

					{ items.length > 0 ? (
						<div className="domio-faq__list">
							{ items.map( ( item, index ) => (
								<details
									className="domio-faq__item"
									key={ item.id || index }
									open
									onToggle={ ( event ) => {
										// Keep items open in the editor for editing.
										event.preventDefault();
									} }
								>
									<summary className="domio-faq__summary">
										<RichText
											tagName="h3"
											className="domio-faq__question"
											value={ item.question || '' }
											onChange={ ( value ) =>
												updateItem( index, 'question', value )
											}
											placeholder={ __( 'Kysymys…', 'domio' ) }
											allowedFormats={ [] }
										/>
									</summary>
									<RichText
										tagName="div"
										className="domio-faq__answer"
										value={ item.answer || '' }
										onChange={ ( value ) =>
											updateItem( index, 'answer', value )
										}
										placeholder={ __( 'Vastaus…', 'domio' ) }
										allowedFormats={ [
											'core/bold',
											'core/italic',
											'core/link',
										] }
									/>
								</details>
							) ) }
						</div>
					) : (
						<p className="domio-faq-editor__empty">
							{ __( 'Lisää kysymyksiä sivupalkista.', 'domio' ) }
						</p>
					) }
				</div>
			</section>
		</>
	);
}
