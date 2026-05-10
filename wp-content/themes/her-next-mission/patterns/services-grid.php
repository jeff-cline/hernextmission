<?php
/**
 * Title: Services grid
 * Slug: her-next-mission/services-grid
 * Categories: hnm
 * Description: Six-up grid of programs — coaching, bootcamps, retreats, summit, podcast, grants. Each card CTA = mailto.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$cta_1on1     = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('coaching-1on1')   : '#';
$cta_bootcamp = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('bootcamp')        : '#';
$cta_retreat  = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('retreats')        : '#';
$cta_summit   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('summit')          : '#';
$cta_pod_spon = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('sponsor-deck')    : '#';
$cta_grants   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('donor-inquiry')   : '#';

$services = [
    [
        'title' => '1‑on‑1 Coaching',
        'desc'  => 'Private somatic and life coaching with our founder for women working through identity, transition, and what\'s next.',
        'cta'   => 'Apply for Coaching',
        'href'  => $cta_1on1,
    ],
    [
        'title' => 'Group Bootcamps',
        'desc'  => 'Cohort-based business and clarity bootcamps — structured, intensive, and built around the brave space women in service know.',
        'cta'   => 'Join Next Cohort',
        'href'  => $cta_bootcamp,
    ],
    [
        'title' => 'Retreats',
        'desc'  => 'Immersive multi-day retreats in restorative settings — fitness, somatic work, and the kind of bonds forged in service.',
        'cta'   => 'Reserve a Spot',
        'href'  => $cta_retreat,
    ],
    [
        'title' => 'Summit & Events',
        'desc'  => 'An annual gathering of women, sponsors, and speakers — virtual, hybrid, and in-person tracks. Tiered tickets including scholarship seats.',
        'cta'   => 'Get Summit Info',
        'href'  => $cta_summit,
    ],
    [
        'title' => 'Podcast Sponsorships',
        'desc'  => 'Reach a vetted audience of female veterans and first responders through brand-safe partnership reads on the Her Next Mission podcast.',
        'cta'   => 'Sponsor the Podcast',
        'href'  => $cta_pod_spon,
    ],
    [
        'title' => 'Grants & Donations',
        'desc'  => 'Recurring giving, named gifts, and SBIR / DoD veteran-support grants. Funds coaching, scholarships, and the Summit.',
        'cta'   => 'Fund the Mission',
        'href'  => $cta_grants,
    ],
];
?>
<!-- wp:group {"className":"hnm-section hnm-section--services","backgroundColor":"paper","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--services has-paper-background-color has-background" style="padding:7rem 1.5rem">

    <!-- wp:html -->
    <p style="text-align:center;margin:0 0 0.75rem"><span class="hnm-eyebrow">Programs</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"spacing":{"margin":{"bottom":"1rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="margin-bottom:1rem">Whole-woman healing. Built for the mission ahead.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"4rem","left":"auto","right":"auto"}},"typography":{"fontSize":"1.125rem","lineHeight":"1.65","maxWidth":"720px"}},"textColor":"ink-soft"} -->
    <p class="has-text-align-center has-ink-soft-color has-text-color" style="font-size:1.125rem;line-height:1.65;max-width:720px;margin-left:auto;margin-right:auto;margin-bottom:4rem">Mind, body, and spirit. From somatic coaching to business bootcamps, every program is built for women redefining what service looks like.</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="wp-block-columns hnm-services" style="display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-bottom:2rem">
        <?php foreach (array_slice($services, 0, 3) as $svc): ?>
        <div class="hnm-service" style="padding:2.25rem 1.75rem 2rem">
            <h3 style="font-size:1.375rem;margin-top:0.5rem">
                <?php echo esc_html($svc['title']); ?>
            </h3>
            <p style="font-size:1rem;line-height:1.6;color:#54545A">
                <?php echo esc_html($svc['desc']); ?>
            </p>
            <p style="margin-top:1.25rem">
                <a href="<?php echo esc_url($svc['href']); ?>" style="font-weight:700;letter-spacing:0.04em;text-transform:uppercase;font-size:0.82rem;color:#A87B2A;text-decoration:none">
                    <?php echo esc_html($svc['cta']); ?> →
                </a>
            </p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="wp-block-columns hnm-services" style="display:grid;grid-template-columns:repeat(3,1fr);gap:2rem">
        <?php foreach (array_slice($services, 3, 3) as $svc): ?>
        <div class="hnm-service" style="padding:2.25rem 1.75rem 2rem">
            <h3 style="font-size:1.375rem;margin-top:0.5rem">
                <?php echo esc_html($svc['title']); ?>
            </h3>
            <p style="font-size:1rem;line-height:1.6;color:#54545A">
                <?php echo esc_html($svc['desc']); ?>
            </p>
            <p style="margin-top:1.25rem">
                <a href="<?php echo esc_url($svc['href']); ?>" style="font-weight:700;letter-spacing:0.04em;text-transform:uppercase;font-size:0.82rem;color:#A87B2A;text-decoration:none">
                    <?php echo esc_html($svc['cta']); ?> →
                </a>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
