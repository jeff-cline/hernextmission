<?php
/**
 * Title: Audience CTA Cards
 * Slug: her-next-mission/cta-cards
 * Categories: hnm, featured
 * Description: Three editorial image-led cards routing to mailto. Beneficiaries, Sponsors, Donors.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$cta_intake  = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('explore-programs') : '#';
$cta_sponsor = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('sponsor-deck')      : '#';
$cta_donor   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('donor-inquiry')     : '#';

$cards = [
    [
        'eyebrow' => 'For women in transition',
        'title'   => 'Find your next mission.',
        'body'    => 'Coaching, bootcamps, retreats, and a community of women who\'ve stood where you stand. Built to move you forward — fast.',
        'cta'     => 'Explore Programs',
        'href'    => $cta_intake,
        'image'   => $theme_uri . '/assets/images/uniform/ml-02.jpg',
        'theme'   => 'light',
        'anchor'  => 'beneficiaries',
    ],
    [
        'eyebrow' => 'For sponsors',
        'title'   => 'Stand with these women.',
        'body'    => 'Tiered partnerships from Featured to Mission Partner — naming rights at live events, podcast features, and corporate consulting tracks.',
        'cta'     => 'Request Sponsor Deck',
        'href'    => $cta_sponsor,
        'image'   => $theme_uri . '/assets/images/uniform/po-01.jpg',
        'theme'   => 'navy',
        'anchor'  => 'sponsors',
    ],
    [
        'eyebrow' => 'For donors',
        'title'   => 'Move her mission forward.',
        'body'    => 'Recurring giving, scholarships, and named gifts that fund coaching, retreats, and the Summit. Every gift moves a woman through her transition.',
        'cta'     => 'Give Now',
        'href'    => $cta_donor,
        'image'   => $theme_uri . '/assets/images/uniform/ff-04.jpg',
        'theme'   => 'gold',
        'anchor'  => 'donors',
    ],
];
?>
<!-- wp:group {"className":"hnm-section hnm-section--cards","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--cards" style="padding:6rem 1.5rem">

    <!-- wp:html -->
    <p style="text-align:center;margin:0 0 0.5rem"><span class="hnm-eyebrow">Three ways forward</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"spacing":{"margin":{"bottom":"3.5rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="margin-bottom:3.5rem">Find your role in HER NEXT MISSION.</h2>
    <!-- /wp:heading -->

    <!-- wp:html -->
    <div class="hnm-audience-cards">
        <?php foreach ($cards as $c): ?>
            <a class="hnm-acard hnm-acard--<?php echo esc_attr($c['theme']); ?>" id="<?php echo esc_attr($c['anchor']); ?>" href="<?php echo esc_url($c['href']); ?>">
                <span class="hnm-acard__media">
                    <img src="<?php echo esc_url($c['image']); ?>" alt="" loading="lazy" />
                </span>
                <span class="hnm-acard__body">
                    <span class="hnm-acard__eyebrow"><?php echo esc_html($c['eyebrow']); ?></span>
                    <span class="hnm-acard__title"><?php echo esc_html($c['title']); ?></span>
                    <span class="hnm-acard__lede"><?php echo esc_html($c['body']); ?></span>
                    <span class="hnm-acard__cta"><?php echo esc_html($c['cta']); ?> <span aria-hidden="true">→</span></span>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
