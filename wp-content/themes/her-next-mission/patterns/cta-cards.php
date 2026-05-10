<?php
/**
 * Title: Audience CTA Cards
 * Slug: her-next-mission/cta-cards
 * Categories: hnm, featured
 * Description: Three cards with anchor IDs — beneficiaries, sponsors, donors. All CTAs route to mailto.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$cta_intake  = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('explore-programs') : '#';
$cta_sponsor = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('sponsor-deck')      : '#';
$cta_donor   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('donor-inquiry')     : '#';
?>
<!-- wp:group {"className":"hnm-section hnm-section--cards","backgroundColor":"paper","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--cards has-paper-background-color has-background" style="padding:6rem 1.5rem">

    <!-- wp:html -->
    <p class="has-text-align-center" style="text-align:center;margin:0 auto 0.5rem"><span class="hnm-eyebrow">Three ways forward</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"spacing":{"margin":{"bottom":"3.5rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="margin-bottom:3.5rem">Find your role in her next mission.</h2>
    <!-- /wp:heading -->

    <!-- wp:columns {"className":"hnm-cards","style":{"spacing":{"blockGap":{"top":"2rem","left":"2rem"}}}} -->
    <div class="wp-block-columns hnm-cards">

        <!-- wp:column {"className":"hnm-card-col"} -->
        <div class="wp-block-column hnm-card-col">
            <!-- wp:group {"className":"hnm-card hnm-card--beneficiary","backgroundColor":"paper","style":{"spacing":{"padding":{"top":"2.5rem","right":"2rem","bottom":"2.5rem","left":"2rem"}},"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group hnm-card hnm-card--beneficiary has-paper-background-color has-background" id="beneficiaries" style="min-height:100%;padding:2.5rem 2rem">
                <!-- wp:html -->
                <p style="margin:0 0 0.75rem"><span class="hnm-eyebrow">For women in transition</span></p>
                <!-- /wp:html -->
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">Find your next mission.</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>Coaching, bootcamps, retreats, and a community of women who've stood where you stand. Built to move you forward — fast.</p>
                <!-- /wp:paragraph -->
                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"className":"is-style-fill"} -->
                    <div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($cta_intake); ?>">Explore Programs</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"className":"hnm-card-col"} -->
        <div class="wp-block-column hnm-card-col">
            <!-- wp:group {"className":"hnm-card hnm-card--sponsor","backgroundColor":"navy","textColor":"cream","style":{"spacing":{"padding":{"top":"2.5rem","right":"2rem","bottom":"2.5rem","left":"2rem"}},"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group hnm-card hnm-card--sponsor has-cream-color has-navy-background-color has-text-color has-background" id="sponsors" style="min-height:100%;padding:2.5rem 2rem">
                <!-- wp:html -->
                <p style="margin:0 0 0.75rem;color:#E8C870;font-size:0.78rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">For sponsors</p>
                <!-- /wp:html -->
                <!-- wp:heading {"level":3,"textColor":"cream"} -->
                <h3 class="wp-block-heading has-cream-color has-text-color">Stand with these women.</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>Tiered partnerships from Featured to Mission Partner — naming rights at live events, podcast features, and corporate consulting tracks.</p>
                <!-- /wp:paragraph -->
                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"backgroundColor":"gold","textColor":"navy-deep"} -->
                    <div class="wp-block-button"><a class="wp-block-button__link has-navy-deep-color has-gold-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url($cta_sponsor); ?>">Request Sponsor Deck</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"className":"hnm-card-col"} -->
        <div class="wp-block-column hnm-card-col">
            <!-- wp:group {"className":"hnm-card hnm-card--donor","backgroundColor":"paper","style":{"spacing":{"padding":{"top":"2.5rem","right":"2rem","bottom":"2.5rem","left":"2rem"}},"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group hnm-card hnm-card--donor has-paper-background-color has-background" id="donors" style="min-height:100%;padding:2.5rem 2rem">
                <!-- wp:html -->
                <p style="margin:0 0 0.75rem"><span class="hnm-eyebrow">For donors</span></p>
                <!-- /wp:html -->
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">Move her mission forward.</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>Recurring giving, scholarships, and named gifts that fund coaching, retreats, and the Summit. Every dollar moves a woman through her transition.</p>
                <!-- /wp:paragraph -->
                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"className":"is-style-fill"} -->
                    <div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($cta_donor); ?>">Give Now</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
