<?php
/**
 * Seed content for `wp djm seed`. Copy comes from the approved prototype.
 *
 * Media files live in the repo's seed/media folder (outside the theme).
 * Reference them in field values as "@media:<key>".
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

return array(

	'media'   => array(
		'hero' => array(
			'file' => 'hero-couple-dancing.jpg',
			'alt'  => 'A bride and groom dancing in a pool of warm light, surrounded by guests on a crowded dance floor',
		),
	),

	'options' => array(
		'business_name'    => 'DJ Michael',
		'owner_name'       => "Michael O'Neill",
		'tagline'          => 'Wedding DJ · Boston and Portland, ME',
		'location'         => 'Boston and Portland, ME',
		'email'            => 'michael@djmichael.com',
		'instagram_handle' => '@djmichaelo',
		'instagram_url'    => 'https://www.instagram.com/djmichaelo/',
		'areas_served'     => "Greater Boston, Massachusetts\nPortland, Maine\nNew England",
		'cta_label'        => 'Contact me',
		'booking_note'     => "I'm usually booked 1–2 years out. If I'm taken on your date, I'll help you find someone great.",
		'review_platforms' => array(
			array(
				'platform' => 'WeddingWire',
				'rating'   => '5.0',
				'count'    => 138,
				'url'      => 'https://www.weddingwire.com/reviews/dj-michael-brighton/50241bcbd0536563.html',
			),
			array(
				'platform' => 'The Knot',
				'rating'   => '5.0',
				'count'    => 67,
				'url'      => 'https://www.theknot.com/marketplace/dj-michael-brighton-ma-236545',
			),
			array(
				'platform' => 'Yelp',
				'rating'   => '4.9',
				'count'    => 78,
				'url'      => 'http://www.yelp.com/biz/michael-o-neill-boston',
			),
			array(
				'platform' => 'Zola',
				'rating'   => '5.0',
				'count'    => 3,
				'url'      => '', // TODO: profile URL from the client.
			),
		),
		'award_value'      => '16 years',
		'award_label'      => "Couples' Choice Award",
		'award_source'     => 'WeddingWire',
		'ga4_id'           => 'G-7Y08SVNRHQ',
	),

	'pages'   => array(
		'home'    => array(
			'title'  => 'Home',
			'seo'    => array(
				'seo_title'       => 'DJ Michael | Wedding DJ in Boston and Portland, Maine',
				'seo_description' => "Wedding DJ Michael O'Neill: 25 years, 1,000+ weddings and 286 five-star reviews across New England. Based in Boston and Portland, Maine.",
			),
			'blocks' => array(
				array(
					'acf/hero',
					array(
						'image'   => '@media:hero',
						'eyebrow' => 'Wedding DJ · Boston and Portland, Maine',
						'title'   => 'New England wedding DJ',
						'subhead' => '25 years. 1,000+ weddings. A dance floor that stays full.',
					),
				),
				array( 'acf/proof-strip' ),
				array(
					'acf/testimonials',
					array(
						'title'  => 'What couples say',
						// Placeholder excerpts: Michael chooses and approves the real ones.
						'quotes' => array(
							array(
								'text'   => 'Our guests were dancing from the first song to the last, from our grandparents to our college friends. The energy never dropped all night.',
								'name'   => 'Kasey',
								'date'   => 'Nov 2025',
								'source' => 'WeddingWire',
							),
							array(
								'text'   => 'When the rain moved everything inside, Michael stayed completely calm and had us set up in minutes. The floor never emptied.',
								'name'   => 'Mary',
								'date'   => 'Jul 2026',
								'source' => 'WeddingWire',
							),
							array(
								'text'   => 'He listened to everything we asked for and read the room perfectly. Every announcement was on time, and people are still talking about the dancing.',
								'name'   => 'Name',
								'date'   => 'Month 2026',
								'source' => 'The Knot',
							),
						),
					),
				),
			),
		),
		'about'   => array(
			'title'  => 'About',
			'seo'    => array(
				'seo_title'       => "About Michael O'Neill | DJ Michael",
				'seo_description' => "From a high school dance to the Bruins' Stanley Cup season to 1,000+ weddings: the story behind DJ Michael.",
			),
			'blocks' => array(),
		),
		'faq'     => array(
			'title'  => 'FAQ',
			'seo'    => array(
				'seo_title'       => 'FAQ | DJ Michael, Wedding DJ',
				'seo_description' => 'Booking, music, equipment and the wedding day: answers to the questions couples ask DJ Michael most.',
			),
			'blocks' => array(),
		),
		'contact' => array(
			'title'  => 'Contact',
			'seo'    => array(
				'seo_title'       => 'Contact DJ Michael | Wedding DJ in Boston and Portland, Maine',
				'seo_description' => "Tell DJ Michael about your wedding or event. He'll reply by email, usually within a day.",
			),
			'blocks' => array(),
		),
		'vendors' => array(
			'title'  => 'Preferred vendors',
			'seo'    => array(
				'seo_title'       => 'Preferred vendors | DJ Michael',
				'seo_description' => 'Wedding planners, photographers, venues and more that DJ Michael works with often and recommends.',
			),
			'blocks' => array(),
		),
	),

	'menus'   => array(
		'primary' => array(
			'name'  => 'Header',
			'items' => array(
				array(
					'title' => 'Home',
					'page'  => 'home',
					'class' => 'site-nav__item--mobile',
				),
				array(
					'title' => 'About',
					'page'  => 'about',
				),
				array(
					'title' => 'FAQ',
					'page'  => 'faq',
				),
			),
		),
		'footer'  => array(
			'name'  => 'Footer',
			'items' => array(
				array(
					'title' => 'About',
					'page'  => 'about',
				),
				array(
					'title' => 'FAQ',
					'page'  => 'faq',
				),
				array(
					'title' => 'Contact',
					'page'  => 'contact',
				),
				array(
					'title' => 'Preferred vendors',
					'page'  => 'vendors',
				),
				array(
					'title'  => 'Instagram',
					'url'    => 'https://www.instagram.com/djmichaelo/',
					'target' => '_blank',
				),
				array(
					'title'  => 'Trivia',
					'url'    => 'https://mcoproductions.com',
					'target' => '_blank',
				),
			),
		),
	),
);
