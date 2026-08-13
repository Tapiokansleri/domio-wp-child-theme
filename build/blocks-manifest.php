<?php
// This file is generated. Do not modify it manually.
return array(
	'card-grid' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/card-grid',
		'version' => '1.0.0',
		'title' => 'Domio: Korttiruudukko',
		'category' => 'domio',
		'icon' => 'grid-view',
		'description' => 'Korttiruudukko palveluille, perusteluille tai referensseille.',
		'keywords' => array(
			'kortti',
			'ruudukko',
			'palvelut',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Kiinteistöhuoltomme osa-alueet'
			),
			'intro' => array(
				'type' => 'string',
				'default' => 'Domion kiinteistöhuolto kattaa kaikki ne osa-alueet, joista kiinteistön laadukas huolto ja ylläpito koostuu.'
			),
			'columns' => array(
				'type' => 'number',
				'default' => 3
			),
			'cards' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'service-1',
						'icon' => 'key',
						'title' => 'Kiinteistöhuolto ja kiinteistönhoitopalvelut',
						'text' => 'Säännölliset huoltokierrokset, LVIS-järjestelmien valvonta, yleisten tilojen kunnossapito ja ns. talonmiespalvelut taloyhtiöille ja yrityskiinteistöille.',
						'linkUrl' => '/kiinteistohuolto/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-2',
						'icon' => 'shield',
						'title' => 'Siivouspalvelut',
						'text' => 'Porrassiivous ja toimitilojen ylläpitosiivous.',
						'linkUrl' => '/siivouspalvelut/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-3',
						'icon' => 'heart',
						'title' => 'Piha- ja vihertyöt',
						'text' => 'Nurmikonleikkuu, pensaiden ja puiden leikkaukset, lehtityöt ja piha-alueiden hoito vuodenajasta riippumatta.',
						'linkUrl' => '/piha-ja-vihertyot/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-4',
						'icon' => 'check',
						'title' => 'Remontti- ja korjauspalvelut',
						'text' => 'Huoneistokunnostukset, pienkorjaukset ja laajemmat kunnostustyöt.',
						'linkUrl' => '/remontti-ja-korjauspalvelut/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-5',
						'icon' => 'pin',
						'title' => 'Kone- ja kuljetuspalvelut',
						'text' => 'Kalusto maansiirtotöihin ja kiinteistön konetöihin.',
						'linkUrl' => '/kone-ja-kuljetuspalvelut/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-6',
						'icon' => 'snowflake',
						'title' => 'Lumenauraus ja hiekoitus',
						'text' => 'Talvikunnossapito [alueen] vaihteleviin keliolosuhteisiin.',
						'linkUrl' => '/piha-ja-vihertyot/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-7',
						'icon' => 'key',
						'title' => 'LVI-palvelut',
						'text' => 'Putkityöt, korjaukset ja talotekniset huollot.',
						'linkUrl' => '/lvi-palvelut/',
						'linkText' => 'Lue lisää'
					),
					array(
						'id' => 'service-8',
						'icon' => 'clock',
						'title' => '24/7 vikapäivystys',
						'text' => 'Apu vesivuotoihin, sähköhäiriöihin ja muihin äkillisiin tilanteisiin, kellonajasta riippumatta.',
						'linkUrl' => '/kiinteistohuolto/',
						'linkText' => 'Lue lisää'
					)
				)
			),
			'variant' => array(
				'type' => 'string',
				'default' => 'service'
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Kiinteistöhuoltomme osa-alueet',
				'columns' => 3,
				'variant' => 'service'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'contacts' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/contacts',
		'version' => '1.0.0',
		'title' => 'Domio: Yhteystiedot',
		'category' => 'domio',
		'icon' => 'id',
		'description' => 'Yhteystiedot toimipisteillä, henkilöillä ja laskutustiedoilla.',
		'keywords' => array(
			'yhteystiedot',
			'kontakti',
			'osoite',
			'laskutus',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Yhteystiedot'
			),
			'serviceHours' => array(
				'type' => 'string',
				'default' => ''
			),
			'servicePhone' => array(
				'type' => 'string',
				'default' => ''
			),
			'urgentHeading' => array(
				'type' => 'string',
				'default' => 'Akuutti asia? Soita, me hoidamme.'
			),
			'serviceBlocks' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'espoo-oncall',
						'title' => 'Espoon 24/7 päivystys',
						'phone' => '040 665 6598',
						'email' => '',
						'description' => 'Akuutit vikatilanteet ja ovenavaukset vuorokauden ympäri.'
					),
					array(
						'id' => 'customer-service',
						'title' => 'Asiakaspalvelu',
						'phone' => '040 630 5536',
						'email' => 'asiakaspalvelu@domio.fi',
						'description' => ''
					)
				)
			),
			'serviceNote' => array(
				'type' => 'string',
				'default' => 'Asutko espoolaisessa taloyhtiössä, jonka huoltoyhtiö on joku muu? Löydät oikean päivystysnumeron taloyhtiösi porrastaulusta tai isännöitsijältä.'
			),
			'activeArea' => array(
				'type' => 'string',
				'default' => 'helsinki'
			),
			'areas' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'espoo',
						'label' => 'Espoo ja Kauniainen',
						'street' => 'Tiistinniityntie 8C, halli 8',
						'city' => '02230 Espoo',
						'mapQuery' => 'Tiistinniityntie 8C halli 8, 02230 Espoo'
					),
					array(
						'id' => 'helsinki',
						'label' => 'Helsinki',
						'street' => 'Keimolanmäentie 11, halli 21',
						'city' => '01700 Vantaa',
						'mapQuery' => 'Keimolanmaentie 11 halli 21, 01700 Vantaa'
					),
					array(
						'id' => 'vantaa',
						'label' => 'Vantaa',
						'street' => 'Keimolanmäentie 11, halli 21',
						'city' => '01700 Vantaa',
						'mapQuery' => 'Keimolanmaentie 11 halli 21, 01700 Vantaa'
					)
				)
			),
			'people' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'elias',
						'section' => 'Myynti',
						'name' => 'Elias Myllykoski',
						'role' => 'Tarjoukset',
						'phone' => '040 156 9126',
						'email' => 'elias.myllykoski@domio.fi',
						'imageId' => 0,
						'imageAlt' => 'Elias Myllykoski, Domion myynti'
					),
					array(
						'id' => 'eero',
						'section' => 'Operatiivinen toiminta',
						'name' => 'Eero Järvenpää',
						'role' => 'Operatiiviseen toimintaan liittyvät asiat',
						'phone' => '044 983 2760',
						'email' => 'eero.jarvenpaa@domio.fi',
						'imageId' => 0,
						'imageAlt' => 'Eero Järvenpää, Domion operatiivinen toiminta'
					),
					array(
						'id' => 'henna',
						'section' => 'Hallinto',
						'name' => 'Henna Järvenpää',
						'role' => 'Hallinto',
						'phone' => '',
						'email' => 'henna.jarvenpaa@domio.fi',
						'imageId' => 0,
						'imageAlt' => 'Henna Järvenpää, Domion hallinto'
					)
				)
			),
			'invoiceHeading' => array(
				'type' => 'string',
				'default' => 'Laskutustiedot'
			),
			'invoiceRows' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'company',
						'label' => 'Yritys',
						'value' => 'Domio Kiinteistöpalvelut Oy'
					),
					array(
						'id' => 'business-id',
						'label' => 'Y-tunnus',
						'value' => '3534853-3'
					),
					array(
						'id' => 'einvoice',
						'label' => 'Verkkolaskutusosoite',
						'value' => '003735348533'
					),
					array(
						'id' => 'operator',
						'label' => 'Välittäjätunnus',
						'value' => '003708599126 (OpenText)'
					),
					array(
						'id' => 'email-invoice',
						'label' => 'Sähköpostilaskutus',
						'value' => 'fennoa.519570@erin.posti.com',
						'href' => 'mailto:fennoa.519570@erin.posti.com'
					),
					array(
						'id' => 'paper',
						'label' => 'Paperilaskut',
						'value' => 'Domio Kiinteistöpalvelut Oy
PL 62521
00062 LASKUTUS'
					)
				)
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Yhteystiedot'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'cta' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/cta',
		'version' => '1.1.0',
		'title' => 'Domio: Toimintakehote',
		'category' => 'domio',
		'icon' => 'megaphone',
		'description' => 'Täysleveä CTA toistuvalla taustakuviolla, väli-CTA:na tai lomakkeella.',
		'keywords' => array(
			'cta',
			'lomake',
			'yhteydenotto',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'heading' => array(
				'type' => 'string',
				'default' => 'Haluatko tietää mitä kiinteistösi huolto maksaisi?'
			),
			'text' => array(
				'type' => 'string',
				'default' => 'Laske suuntaa-antava arvio muutamassa sekunnissa.'
			),
			'ctaText' => array(
				'type' => 'string',
				'default' => 'Laske hinta-arvio'
			),
			'ctaUrl' => array(
				'type' => 'string',
				'default' => '/hintalaskuri/'
			),
			'phone' => array(
				'type' => 'string',
				'default' => ''
			),
			'formShortcode' => array(
				'type' => 'string',
				'default' => ''
			),
			'variant' => array(
				'type' => 'string',
				'default' => 'band'
			),
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'background' => array(
				'type' => 'string',
				'default' => 'green'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => '1'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 100
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Haluatko tietää mitä kiinteistösi huolto maksaisi?',
				'text' => 'Laske suuntaa-antava arvio muutamassa sekunnissa.',
				'ctaText' => 'Laske hinta-arvio',
				'variant' => 'band',
				'align' => 'full'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'faq' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/faq',
		'version' => '1.0.0',
		'title' => 'Domio: UKK',
		'category' => 'domio',
		'icon' => 'editor-help',
		'description' => 'Usein kysytyt kysymykset details/summary-rakenteella.',
		'keywords' => array(
			'ukk',
			'faq',
			'kysymykset',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Usein kysytyt kysymykset'
			),
			'items' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'faq-1',
						'question' => 'Mitä kiinteistöhuolto tarkoittaa?',
						'answer' => 'Kiinteistöhuolto tarkoittaa kiinteistön teknisten järjestelmien, yleisten tilojen ja ulkoalueiden säännöllistä ylläpitoa, jotta rakennus pysyy turvallisena ja toimivana. Käytännössä tähän kuuluu LVIS-järjestelmien valvonta, pienkorjaukset, talvikunnossapito ja vikatilanteisiin reagointi.'
					),
					array(
						'id' => 'faq-2',
						'question' => 'Missä asioissa kiinteistöhuoltoon voi olla yhteydessä?',
						'answer' => 'Esimerkiksi vesivuodoissa, lämmityksen tai ilmanvaihdon ongelmissa, valaistuksen vioissa, lukitusongelmissa sekä piha-alueiden ja talvikunnossapidon tarpeissa. Akuuteissa tilanteissa päivystyksemme vastaa vuorokauden ympäri.'
					),
					array(
						'id' => 'faq-3',
						'question' => 'Mitä kiinteistöhuolto maksaa?',
						'answer' => 'Hinta määräytyy kiinteistön koon, käyttötarkoituksen ja sovittujen palveluiden mukaan. Räätälöimme sopimuksen aina kohteen todellisten tarpeiden mukaan, joten pyydä tarjous tai laske suuntaa-antava arvio hintalaskurillamme.'
					),
					array(
						'id' => 'faq-4',
						'question' => 'Mitä sopimukseen kuuluu ja mitä laskutetaan erikseen?',
						'answer' => 'Perussopimus kattaa sovitun säännöllisen huollon ja ylläpidon. Korjaustyöt, tarvikkeet ja suuremmat kausityöt laskutetaan yleensä erikseen. Erittelemme tämän aina tarjouksessa.'
					),
					array(
						'id' => 'faq-5',
						'question' => 'Toimitteko koko pääkaupunkiseudulla?',
						'answer' => 'Palvelemme kiinteistöjä Helsingissä, Espoossa ja Vantaalla, ja toimimme tarvittaessa myös muualla Uudellamaalla.'
					),
					array(
						'id' => 'faq-6',
						'question' => 'Miten huoltoyhtiön vaihto käytännössä tapahtuu?',
						'answer' => 'Vaihto tehdään voimassa olevan sopimuksen irtisanomisajan puitteissa. Sovimme aloituspäivän niin, ettei palveluun jää katkosta, ja varmistamme huoltokirjan ja avainten siirron.'
					)
				)
			),
			'defaultOpen' => array(
				'type' => 'number',
				'default' => -1
			),
			'emitSchema' => array(
				'type' => 'boolean',
				'default' => true
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Usein kysytyt kysymykset'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'hero' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/hero',
		'version' => '1.1.0',
		'title' => 'Domio: Hero',
		'category' => 'domio',
		'icon' => 'cover-image',
		'description' => 'Sivun yläbanneri taustakuvalla, otsikolla ja CTA:lla.',
		'keywords' => array(
			'hero',
			'banneri',
			'otsikko',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Kiinteistöhuolto [alue]'
			),
			'headingLevel' => array(
				'type' => 'number',
				'default' => 1
			),
			'subheading' => array(
				'type' => 'string',
				'default' => 'Huolehdimme kiinteistösi arjen sujuvuudesta, turvallisuudesta ja viihtyisyydestä. Kaikki palvelut saman sopimuksen alla.'
			),
			'primaryCtaText' => array(
				'type' => 'string',
				'default' => 'Pyydä maksuton kartoitus'
			),
			'primaryCtaUrl' => array(
				'type' => 'string',
				'default' => '#tarjous'
			),
			'secondaryCtaText' => array(
				'type' => 'string',
				'default' => 'Laske hinta-arvio'
			),
			'secondaryCtaUrl' => array(
				'type' => 'string',
				'default' => '#hintalaskuri'
			),
			'imageId' => array(
				'type' => 'number',
				'default' => 0
			),
			'imageUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageAlt' => array(
				'type' => 'string',
				'default' => ''
			),
			'layout' => array(
				'type' => 'string',
				'default' => 'banner'
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => false
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Kiinteistöhuolto [alue]',
				'subheading' => 'Huolehdimme kiinteistösi arjen sujuvuudesta, turvallisuudesta ja viihtyisyydestä. Kaikki palvelut saman sopimuksen alla.',
				'layout' => 'banner'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'hintalaskuri' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/hintalaskuri',
		'version' => '1.0.2',
		'title' => 'Domio: Hintalaskuri',
		'category' => 'domio',
		'icon' => 'calculator',
		'description' => 'Näyttää Domion hintalaskurin [domio_hintalaskuri]-shortcoden sisältä.',
		'keywords' => array(
			'hintalaskuri',
			'laskuri',
			'hinta',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'anchor' => array(
				'type' => 'string',
				'default' => 'hintalaskuri'
			),
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'render' => 'file:./render.php'
	),
	'media-text' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/media-text',
		'version' => '1.0.0',
		'title' => 'Domio: Teksti ja media',
		'category' => 'domio',
		'icon' => 'align-pull-right',
		'description' => 'Teksti- ja mediapalsta InnerBlocks-sisällöllä.',
		'keywords' => array(
			'media',
			'teksti',
			'kuva',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Kiinteistöhuolto [alueella]'
			),
			'layout' => array(
				'type' => 'string',
				'default' => 'default'
			),
			'mediaPosition' => array(
				'type' => 'string',
				'default' => 'right'
			),
			'mediaId' => array(
				'type' => 'number',
				'default' => 0
			),
			'mediaUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'mediaAlt' => array(
				'type' => 'string',
				'default' => ''
			),
			'mediaWidth' => array(
				'type' => 'number',
				'default' => 50
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Kiinteistöhuolto [alueella]',
				'mediaPosition' => 'right',
				'background' => 'surface'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'related-links' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/related-links',
		'version' => '1.0.0',
		'title' => 'Domio: Tutustu myös',
		'category' => 'domio',
		'icon' => 'admin-links',
		'description' => 'Keskitetysti hallittavat sisäiset linkit alueille ja palveluille.',
		'keywords' => array(
			'linkit',
			'alueet',
			'palvelut',
			'tutustu',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Tutustu myös'
			),
			'groups' => array(
				'type' => 'array',
				'default' => array(
					
				)
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Tutustu myös'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'steps' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/steps',
		'version' => '1.0.0',
		'title' => 'Domio: Vaiheet',
		'category' => 'domio',
		'icon' => 'editor-ol',
		'description' => 'Numeroitu vaihelista prosessin esittelyyn.',
		'keywords' => array(
			'vaiheet',
			'prosessi',
			'lista',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'heading' => array(
				'type' => 'string',
				'default' => 'Näin pääset alkuun'
			),
			'steps' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'step-1',
						'title' => 'Ota yhteyttä',
						'text' => 'Kerro kiinteistöstäsi lomakkeella tai puhelimitse. Riittää, että tiedämme kohteen tyypin ja osoitteen.',
						'timeLabel' => ''
					),
					array(
						'id' => 'step-2',
						'title' => 'Maksuton kartoitus',
						'text' => 'Käymme kohteessa ja käymme läpi, mitä kiinteistö todella tarvitsee. Kartoitus ei sido mihinkään.',
						'timeLabel' => ''
					),
					array(
						'id' => 'step-3',
						'title' => 'Selkeä tarjous',
						'text' => 'Saat tarjouksen, jossa näkyy mitä sopimukseen kuuluu ja mitä laskutetaan erikseen.',
						'timeLabel' => ''
					),
					array(
						'id' => 'step-4',
						'title' => 'Sopimus ja aloitus',
						'text' => 'Sovimme aloitusajankohdan ja yhteyshenkilön. Huolto käynnistyy sovitusti.',
						'timeLabel' => ''
					)
				)
			),
			'orientation' => array(
				'type' => 'string',
				'default' => 'horizontal'
			),
			'showNumbers' => array(
				'type' => 'boolean',
				'default' => true
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'heading' => 'Näin pääset alkuun',
				'orientation' => 'horizontal'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'trust-bar' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/trust-bar',
		'version' => '1.0.1',
		'title' => 'Domio: Luottamuspalkki',
		'category' => 'domio',
		'icon' => 'shield',
		'description' => 'Kapea, koko leveä palkki ikoneilla ja luottamuslauseilla. Oletuksena heron jälkeen.',
		'keywords' => array(
			'luottamus',
			'ikoni',
			'palkki',
			'trust',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'items' => array(
				'type' => 'array',
				'default' => array(
					array(
						'id' => 'trust-clock',
						'icon' => 'clock',
						'text' => 'Vikapäivystys 24/7'
					),
					array(
						'id' => 'trust-shield',
						'icon' => 'shield',
						'text' => '10 vuoden kokemus alalta'
					),
					array(
						'id' => 'trust-check',
						'icon' => 'check',
						'text' => 'Kaikki palvelut yhdeltä kumppanilta'
					),
					array(
						'id' => 'trust-pin',
						'icon' => 'pin',
						'text' => 'Helsinki, Espoo, Vantaa'
					)
				)
			),
			'imageId' => array(
				'type' => 'number',
				'default' => 0
			),
			'imageUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageAlt' => array(
				'type' => 'string',
				'default' => ''
			),
			'background' => array(
				'type' => 'string',
				'default' => 'green'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 100
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => false
			)
		),
		'example' => array(
			'attributes' => array(
				'align' => 'full'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'trustmary' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'domio/trustmary',
		'version' => '1.0.1',
		'title' => 'Domio: Trustmary',
		'category' => 'domio',
		'icon' => 'star-filled',
		'description' => 'Upottaa Trustmary-arvosteluwidgetin.',
		'keywords' => array(
			'trustmary',
			'arvostelut',
			'referenssit',
			'domio'
		),
		'textdomain' => 'domio',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'widgetSrc' => array(
				'type' => 'string',
				'default' => 'https://widget.trustmary.com/n3eIUlhS5'
			),
			'background' => array(
				'type' => 'string',
				'default' => 'surface'
			),
			'pattern' => array(
				'type' => 'string',
				'default' => 'none'
			),
			'patternOpacity' => array(
				'type' => 'number',
				'default' => 3
			)
		),
		'supports' => array(
			'align' => array(
				'wide',
				'full'
			),
			'anchor' => true,
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'example' => array(
			'attributes' => array(
				
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	)
);
