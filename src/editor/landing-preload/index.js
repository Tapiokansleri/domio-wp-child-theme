/**
 * Domio landing page preload panel in the page document sidebar.
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { Button, Notice } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';
import { createBlock, createBlocksFromInnerBlocksTemplate } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { getDomioPageTemplate } from '../../shared/template-notice';

const uid = ( prefix ) =>
	`${ prefix }-${ Date.now() }-${ Math.floor( Math.random() * 1000 ) }`;

const pad80 = {
	spacing: {
		padding: {
			top: 'var:preset|spacing|80',
			bottom: 'var:preset|spacing|80',
		},
		margin: { top: '0', bottom: '0' },
	},
};

const pad80ZeroXY = {
	spacing: {
		padding: {
			top: 'var:preset|spacing|80',
			bottom: 'var:preset|spacing|80',
			left: '0',
			right: '0',
		},
		margin: { top: '0', bottom: '0', left: '0', right: '0' },
	},
};

const pad40 = {
	spacing: {
		padding: {
			top: 'var:preset|spacing|40',
			bottom: 'var:preset|spacing|40',
		},
		margin: { top: '0', bottom: '0' },
	},
};

/**
 * Build a full area landing page with Domio default copy.
 *
 * @return {Object[]} Blocks.
 */
function buildLandingBlocks() {
	const formShortcode =
		( typeof window !== 'undefined' &&
			window.domioLandingPreload &&
			window.domioLandingPreload.formShortcode ) ||
		'[metform form_id="270"]';
	const heroImageUrl =
		( typeof window !== 'undefined' &&
			window.domioLandingPreload &&
			window.domioLandingPreload.heroImageUrl ) ||
		'';
	const heroImageAlt =
		( typeof window !== 'undefined' &&
			window.domioLandingPreload &&
			window.domioLandingPreload.heroImageAlt ) ||
		'';

	return [
		createBlock( 'domio/hero', {
			align: 'full',
			heading: 'Kiinteistöhuolto [alue]',
			headingLevel: 1,
			subheading:
				'Toimipisteemme sijaitsee [alueella], joten olemme paikalla nopeasti. Huolto, siivous ja pihatyöt saman sopimuksen alla taloyhtiöille, yrityksille ja liikekiinteistöille.',
			primaryCtaText: 'Pyydä maksuton kartoitus',
			primaryCtaUrl: '#tarjous',
			secondaryCtaText: 'Laske hinta-arvio',
			secondaryCtaUrl: '#hintalaskuri',
			layout: 'banner',
			imageId: 0,
			imageUrl: heroImageUrl,
			imageAlt: heroImageAlt,
			background: 'surface',
		} ),
		createBlock( 'domio/trust-bar', {
			align: 'full',
			items: [
				{ id: uid( 't' ), icon: 'clock', text: 'Vikapäivystys 24/7' },
				{
					id: uid( 't' ),
					icon: 'shield',
					text: '10 vuoden kokemus alalta',
				},
				{
					id: uid( 't' ),
					icon: 'check',
					text: 'Kaikki palvelut yhdeltä kumppanilta',
				},
				{
					id: uid( 't' ),
					icon: 'pin',
					text: 'Toimipiste [alueella]',
				},
			],
			background: 'green',
		} ),
		createBlock( 'domio/hintalaskuri', {
			align: 'full',
			anchor: 'hintalaskuri',
			background: 'surface',
			style: {
				spacing: {
					margin: { top: '0', bottom: '0', left: '0', right: '0' },
					padding: {
						top: 'var:preset|spacing|80',
						bottom: 'var:preset|spacing|80',
					},
				},
			},
		} ),
		createBlock(
			'domio/media-text',
			{
				heading: 'Kiinteistöhuolto ja kiinteistönhoitopalvelut [alueella]',
				mediaPosition: 'right',
				mediaWidth: 50,
				background: 'surface',
				style: pad80,
			},
			createBlocksFromInnerBlocksTemplate( [
				[
					'core/paragraph',
					{
						content:
							'Tunnemme [alueen] kohteemme läpikotaisin ja pidämme niistä huolta pitkäjänteisesti. Jokaisella kiinteistöllä on nimetty yhteyshenkilö, joka tuntee kohteen erityispiirteet.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Aina askelman edellä. Säännölliset tarkastukset ja järjestelmien seuranta paljastavat viat siinä vaiheessa, kun ne ovat vielä pieniä ja edullisia korjata.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Päivystys palvelee kellon ja kalenterin ympäri, ja toimipisteemme sijainti pitää vasteajat lyhyinä koko [alueella].',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Näin asuminen pysyy turvallisena ja viihtyisänä ja kiinteistön arvo säilyy vuosien yli.',
					},
				],
				[
					'core/buttons',
					{
						style: {
							spacing: {
								padding: {
									top: 'var:preset|spacing|30',
									bottom: 'var:preset|spacing|30',
								},
							},
						},
					},
					[
						[
							'core/button',
							{
								text: 'Ota yhteyttä',
								url: '#tarjous',
							},
						],
					],
				],
			] )
		),
		createBlock( 'domio/trustmary', {
			align: 'full',
			widgetSrc: 'https://widget.trustmary.com/n3eIUlhS5',
			background: 'muted',
			pattern: '1',
			patternOpacity: 4,
			style: pad40,
		} ),
		createBlock( 'domio/card-grid', {
			heading: 'Domion kiinteistöpalvelut [alueella]',
			intro: '',
			columns: 3,
			variant: 'service',
			className: 'is-style-service',
			background: 'surface',
			style: pad80ZeroXY,
			cards: [
				{
					id: uid( 'c' ),
					icon: 'key',
					title: 'Tekninen kiinteistöhuolto',
					text: 'LVIS-järjestelmien huolto, määräaikaistarkastukset ja ennakoiva seuranta. Viat havaitaan ennen kuin ne kasvavat.',
					linkUrl: '/kiinteistohuolto/',
					linkText: 'Lue lisää',
				},
				{
					id: uid( 'c' ),
					icon: 'check',
					title: 'Kiinteistön ylläpito',
					text: 'Valaistus, lukitus, kulkureitit ja yleisten tilojen kunnon seuranta. Arki sujuu ilman katkoja.',
					linkUrl: '/kiinteistohuolto/',
					linkText: 'Lue lisää',
				},
				{
					id: uid( 'c' ),
					icon: 'heart',
					title: 'Kiinteistönhoitopalvelut',
					text: 'Talonmiespalvelut [alueen] taloyhtiöille ja yrityksille. Avaintenhallinta, pienkorjaukset ja kiinnitystyöt.',
					linkUrl: '/kiinteistohuolto/',
					linkText: 'Lue lisää',
				},
				{
					id: uid( 'c' ),
					icon: 'snowflake',
					title: 'Ulkoalueiden hoito',
					text: 'Hoidamme aurauksen, hiekoituksen, nurmikot ja puuston konetyönä [alueen] olosuhteisiin sopivalla tavalla.',
					linkUrl: '/piha-ja-vihertyot/',
					linkText: 'Lue lisää',
				},
				{
					id: uid( 'c' ),
					icon: 'shield',
					title: 'Siivouspalvelut',
					text: 'Porrassiivous, toimitilasiivous ja erikoissiivoukset. Vakiosiivooja ja selkeä raportointi.',
					linkUrl: '/siivouspalvelut/',
					linkText: 'Lue lisää',
				},
				{
					id: uid( 'c' ),
					icon: 'clock',
					title: 'Vikapäivystys 24/7',
					text: 'Vesivuodot, sähköhäiriöt ja kiireelliset ovenavaukset. Päivystys vastaa vuorokauden ympäri.',
					linkUrl: '/kiinteistohuolto/',
					linkText: 'Lue lisää',
				},
			],
		} ),
		createBlock(
			'domio/media-text',
			{
				heading: 'Kiinteistöhuolto [alueella] on erilaista kuin muualla',
				mediaPosition: 'left',
				mediaWidth: 50,
				background: 'muted',
				pattern: '1',
				style: pad80,
			},
			createBlocksFromInnerBlocksTemplate( [
				[
					'core/heading',
					{
						level: 3,
						content: 'Rakennuskanta on tulossa remontti-ikään',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Ennakoiva huolto ratkaisee tässä ikäluokassa eniten. Kun tekniikkaa seurataan järjestelmällisesti, taloyhtiö ehtii suunnitella remontit rauhassa sen sijaan, että ne tehtäisiin vahingon jälkeen.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Suuri osa [alueen] taloyhtiöistä on rakennettu 1960-luvun ja 1980-luvun välillä. Näissä kohteissa putkistot, ilmanvaihtokoneet ja lämmönjakokeskukset ovat käyttöikänsä loppupäässä.',
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: 'Ulkoalueet vaativat suunnitelmallista hoitoa',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'[Alueen] taloyhtiöillä on tyypillisesti selvästi enemmän piha-aluetta, nurmikkoa ja puustoa asuntoa kohden kuin kantakaupungin kohteilla.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Käytännössä tämä siirtää painopisteen ulkoalueiden hoitoon. Pitkät ajotiet ja laajat pysäköintialueet hoituvat konetyönä, eivät lapiolla, ja lunta mahtuu läjittämään omalle tontille.',
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: 'Uudemmissa kohteissa on automaatiota',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'[Alueen] uudemmissa kohteissa on rakennusautomaatiota, hybridilämmitystä ja ilmanvaihdon ohjausjärjestelmiä.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Nämä järjestelmät vaativat säätöä ja seurantaa, ei pelkkiä huoltokierroksia. Säädöillä on suora vaikutus energialaskuun.',
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: 'Paikallinen toimipiste pitää vasteajat lyhyinä',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'[Alue] on laaja, ja matkat kaupunginosien välillä voivat olla pitkiä. Kaukaa käsin operoiva huoltoyhtiö menettää tässä välissä sen ajan, jonka pitäisi mennä työn tekemiseen.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Toimipisteemme sijaitsee [alueella]. Se on syy siihen, miksi pääsemme kohteeseen nopeasti riippumatta siitä, missä päin [aluetta] kiinteistö on.',
					},
				],
			] )
		),
		createBlock(
			'domio/media-text',
			{
				heading: 'Akuutti asia? Soita, me hoidamme.',
				mediaPosition: 'left',
				mediaWidth: 50,
				background: 'surface',
				style: pad80,
			},
			createBlocksFromInnerBlocksTemplate( [
				[
					'core/heading',
					{
						level: 3,
						content: '[Alueen] 24/7 päivystys',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'<strong>040 665 6598</strong><br>Akuutit vikatilanteet ja ovenavaukset vuorokauden ympäri.',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'<strong>Asiakaspalvelu</strong><br>040 630 5536<br>asiakaspalvelu@domio.fi',
					},
				],
				[
					'core/paragraph',
					{
						content:
							'Asutko [alueella] taloyhtiössä, jonka huoltoyhtiö on joku muu? Löydät oikean päivystysnumeron taloyhtiösi porrastaulusta tai isännöitsijältä.',
					},
				],
			] )
		),
		createBlock( 'domio/card-grid', {
			heading: 'Miksi valita Domio?',
			intro: 'Domio on paikallinen ja joustava kumppani, joka tuntee [alueen] olosuhteet ja kiinteistökannan.',
			columns: 2,
			variant: 'reason',
			className: 'is-style-reason',
			background: 'green',
			pattern: '2',
			style: pad80,
			cards: [
				{
					id: uid( 'c' ),
					icon: 'check',
					title: 'Kaikki yhdeltä kumppanilta',
					text: 'Huolto, siivous, pihatyöt ja LVI saman sopimuksen alla. Isännöitsijän ei tarvitse koordinoida useaa toimittajaa.',
				},
				{
					id: uid( 'c' ),
					icon: 'heart',
					title: 'Tuttu yhteyshenkilö',
					text: 'Saat nimetyn kontaktihenkilön, joka tuntee kohteen ja sen erityispiirteet. Et selitä samaa asiaa joka kerta uudelleen.',
				},
				{
					id: uid( 'c' ),
					icon: 'pin',
					title: 'Toimipiste [alueella]',
					text: 'Emme aja kaukaa. Sijaintimme pitää vasteajat lyhyinä koko [alueella].',
				},
				{
					id: uid( 'c' ),
					icon: 'clock',
					title: 'Päivystys ympäri vuorokauden',
					text: 'Äkilliset vikatilanteet hoidetaan nopeasti, jotta vahingot pysyvät pieninä.',
				},
			],
		} ),
		createBlock( 'domio/steps', {
			heading: 'Näin pääset alkuun',
			orientation: 'horizontal',
			showNumbers: true,
			background: 'surface',
			style: pad80ZeroXY,
			steps: [
				{
					id: uid( 's' ),
					title: 'Ota yhteyttä',
					text: 'Kerro kiinteistöstäsi lomakkeella tai puhelimitse. Riittää, että tiedämme kohteen tyypin ja osoitteen.',
					timeLabel: '',
				},
				{
					id: uid( 's' ),
					title: 'Maksuton kartoitus',
					text: 'Käymme kohteessa [alueella] ja käymme läpi, mitä kiinteistö todella tarvitsee. Kartoitus ei sido mihinkään.',
					timeLabel: '',
				},
				{
					id: uid( 's' ),
					title: 'Selkeä tarjous',
					text: 'Saat tarjouksen, jossa näkyy mitä sopimukseen kuuluu ja mitä laskutetaan erikseen.',
					timeLabel: '',
				},
				{
					id: uid( 's' ),
					title: 'Sopimus ja aloitus',
					text: 'Sovimme aloitusajankohdan ja yhteyshenkilön. Huolto käynnistyy sovitusti ilman katkosta.',
					timeLabel: '',
				},
			],
		} ),
		createBlock( 'domio/cta', {
			heading: 'Tilaa maksuton kiinteistön kartoitus',
			text: 'Olitpa isännöitsijä, hallituksen puheenjohtaja tai kiinteistön omistaja, saat meiltä selkeän tarjouksen kiinteistösi hoitoon. Kartoitus on maksuton eikä sido mihinkään.',
			ctaText: 'Pyydä maksuton kartoitus',
			ctaUrl: '#tarjous',
			phone: '',
			formShortcode,
			variant: 'form',
			align: 'full',
			className: 'is-style-form',
			anchor: 'tarjous',
			background: 'green',
			pattern: '2',
			patternOpacity: 10,
			style: pad80,
		} ),
		createBlock( 'domio/contacts', {
			align: 'full',
			background: 'green',
			style: pad80,
		} ),
		createBlock( 'domio/related-links', {
			heading: 'Tutustu myös',
			background: 'surface',
			pattern: '1',
			style: pad80,
		} ),
	];
}

function LandingPreloadPanel() {
	const [ notice, setNotice ] = useState( null );
	const { resetBlocks } = useDispatch( blockEditorStore );
	const { editPost } = useDispatch( editorStore );
	const postType = useSelect(
		( select ) => select( editorStore ).getCurrentPostType(),
		[]
	);
	const blockCount = useSelect(
		( select ) => select( blockEditorStore ).getBlockCount(),
		[]
	);

	if ( postType && ! [ 'page', 'post' ].includes( postType ) ) {
		return null;
	}

	const onPreload = () => {
		if ( blockCount > 0 ) {
			const ok = window.confirm(
				__(
					'Sivulla on jo sisältöä. Korvataanko se Domio-ländärin oletuspalikoilla?',
					'domio'
				)
			);
			if ( ! ok ) {
				return;
			}
		}

		const pageTemplate =
			( typeof window !== 'undefined' &&
				window.domioLandingPreload &&
				window.domioLandingPreload.pageTemplate ) ||
			'page-templates/domio-sivupohja.php';

		editPost( { template: pageTemplate } );
		resetBlocks( buildLandingBlocks() );
		setNotice(
			__(
				'Ländäripalikat ladattu ja sivupohjaksi asetettu “Domio sivupohja”. Korvaa sivukohtaiset placeholderit ennen julkaisua.',
				'domio'
			)
		);
	};

	return (
		<PluginDocumentSettingPanel
			name="domio-landing-preload"
			title={ __( 'Domio ländäri', 'domio' ) }
			className="domio-landing-preload-panel"
		>
			<p>
				{ __(
					'Lataa valmis aluesivun lohkorakenne Domion oletusteksteillä ja vaihtaa sivupohjaksi Domio sivupohja.',
					'domio'
				) }
			</p>
			{ notice ? (
				<Notice
					status="success"
					isDismissible
					onRemove={ () => setNotice( null ) }
				>
					{ notice }
				</Notice>
			) : null }
			<Button variant="primary" onClick={ onPreload }>
				{ __( 'Esilataa landing page palikat', 'domio' ) }
			</Button>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'domio-landing-preload', {
	render: LandingPreloadPanel,
	icon: 'layout',
} );

/**
 * Toggle body class so Elementor “Edit with Elementor” can be hidden via CSS
 * whenever Domio sivupohja is the selected page template.
 */
function DomioElementorGuard() {
	const pageTemplate = getDomioPageTemplate();
	const template = useSelect(
		( select ) => select( editorStore ).getEditedPostAttribute( 'template' ),
		[]
	);
	const isDomioTemplate = template === pageTemplate;

	useEffect( () => {
		document.body.classList.toggle( 'domio-uses-sivupohja', isDomioTemplate );

		const hideElementorButtons = () => {
			if ( ! isDomioTemplate ) {
				return;
			}

			[
				'elementor-switch-mode',
				'elementor-edit-button-gutenberg',
				'elementor-editor',
			].forEach( ( id ) => {
				const el = document.getElementById( id );
				if ( el ) {
					el.style.setProperty( 'display', 'none', 'important' );
				}
			} );
		};

		hideElementorButtons();

		const observer = new MutationObserver( hideElementorButtons );
		observer.observe( document.body, { childList: true, subtree: true } );

		return () => {
			observer.disconnect();
			document.body.classList.remove( 'domio-uses-sivupohja' );
		};
	}, [ isDomioTemplate ] );

	return null;
}

registerPlugin( 'domio-elementor-guard', {
	render: DomioElementorGuard,
} );
