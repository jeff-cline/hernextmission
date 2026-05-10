<?php
/**
 * Title: Hero — rocket logo + 3 CTAs
 * Slug: her-next-mission/hero
 * Categories: hnm, featured
 * Description: Homepage hero. Logo "rockets up" from below on load and
 * lands above the hero block. The mirror photo stays untouched on the
 * right. The True North compass floats across the boundary into the
 * next section.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri      = get_stylesheet_directory_uri();
$logo_url       = esc_url($theme_uri . '/assets/images/logo-mark.svg');
$hero_url       = esc_url($theme_uri . '/assets/images/hero-krystalore.jpg');
$compass_top    = esc_url($theme_uri . '/assets/images/compass-true-north.png');

$cta_intake  = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('beneficiary-intake') : '#';
$cta_sponsor = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('sponsor-inquiry')   : '#';
$cta_donor   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('donor-inquiry')     : '#';
?>
<!-- wp:group {"className":"hnm-hero","layout":{"type":"constrained","contentSize":"1320px"}} -->
<div class="wp-block-group hnm-hero">
    <div class="hnm-hero__inner">

        <!-- wp:html -->
        <div class="hnm-rocket-stage" data-hnm-rocket aria-hidden="true">
            <span class="hnm-rocket-trail"></span>
            <img class="hnm-rocket" src="<?php echo $logo_url; ?>" alt="" />
        </div>
        <!-- /wp:html -->

        <!-- wp:columns {"verticalAlignment":"center","className":"hnm-hero__row","style":{"spacing":{"blockGap":{"left":"3.5rem"}}}} -->
        <div class="wp-block-columns are-vertically-aligned-center hnm-hero__row">

            <!-- wp:column {"verticalAlignment":"center","width":"55%","className":"hnm-hero__copy"} -->
            <div class="wp-block-column is-vertically-aligned-center hnm-hero__copy" style="flex-basis:55%">

                <!-- wp:html -->
                <span class="hnm-hero__eyebrow">From Service to Success</span>
                <!-- /wp:html -->

                <!-- wp:html -->
                <h1 class="hnm-hero__title">It's <em>her</em> turn.</h1>
                <!-- /wp:html -->

                <!-- wp:paragraph {"className":"hnm-hero__lede"} -->
                <p class="hnm-hero__lede">For female veterans and first responders transitioning out of service — coaching, community, and clarity for the next mission.</p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"className":"hnm-hero__ctas","style":{"spacing":{"blockGap":{"top":"0.75rem","left":"0.75rem"}}}} -->
                <div class="wp-block-buttons hnm-hero__ctas">
                    <!-- wp:button {"className":"is-style-fill"} -->
                    <div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($cta_intake); ?>">For Women in Transition</a></div>
                    <!-- /wp:button -->

                    <!-- wp:button {"textColor":"navy-deep","className":"is-style-outline","style":{"border":{"color":"#1F2A52","width":"1px","radius":"999px"}}} -->
                    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-navy-deep-color has-text-color has-border-color wp-element-button" href="<?php echo esc_url($cta_sponsor); ?>" style="border-color:#1F2A52;border-width:1px;border-radius:999px">For Sponsors</a></div>
                    <!-- /wp:button -->

                    <!-- wp:button {"textColor":"navy-deep","className":"is-style-outline","style":{"border":{"color":"#1F2A52","width":"1px","radius":"999px"}}} -->
                    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-navy-deep-color has-text-color has-border-color wp-element-button" href="<?php echo esc_url($cta_donor); ?>" style="border-color:#1F2A52;border-width:1px;border-radius:999px">Donate</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"verticalAlignment":"center","width":"45%","className":"hnm-hero__media"} -->
            <div class="wp-block-column is-vertically-aligned-center hnm-hero__media" style="flex-basis:45%">
                <!-- wp:html -->
                <figure class="hnm-hero__photo">
                    <img src="<?php echo $hero_url; ?>" alt="Krystalore Crews — facing herself in the mirror, Air Force uniform reflected on the left, business attire on the right" loading="eager" />
                </figure>
                <!-- /wp:html -->
            </div>
            <!-- /wp:column -->

        </div>
        <!-- /wp:columns -->

    </div>
</div>
<!-- /wp:group -->

<!-- wp:html -->
<figure class="hnm-compass-float" aria-hidden="true">
    <img src="<?php echo $compass_top; ?>" alt="" />
</figure>
<!-- /wp:html -->
