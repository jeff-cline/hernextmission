<?php
/**
 * Title: Hero — rocket logo + 3 CTAs
 * Slug: her-next-mission/hero
 * Categories: hnm, featured
 * Description: Homepage hero. Logo "launches" up from below on load. Photo right (Krystalore mirror), 3 CTAs left.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();
$logo_url  = esc_url($theme_uri . '/assets/images/logo.svg');
$hero_url  = esc_url($theme_uri . '/assets/images/hero-krystalore.jpg');
?>
<!-- wp:cover {"customOverlayColor":"#0A2540","minHeight":92,"minHeightUnit":"vh","className":"hnm-hero","style":{"spacing":{"padding":{"top":"3rem","bottom":"3rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-cover hnm-hero" style="padding:3rem 1.5rem;min-height:92vh">
    <span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim" style="background-color:#0A2540"></span>
    <div class="wp-block-cover__inner-container">

        <!-- wp:html -->
        <div class="hnm-rocket-stage" data-hnm-rocket aria-hidden="true">
            <img class="hnm-rocket" src="<?php echo $logo_url; ?>" alt="" />
            <span class="hnm-rocket-trail"></span>
        </div>
        <!-- /wp:html -->

        <!-- wp:columns {"verticalAlignment":"center","className":"hnm-hero__row","style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
        <div class="wp-block-columns are-vertically-aligned-center hnm-hero__row">

            <!-- wp:column {"verticalAlignment":"center","width":"55%","className":"hnm-hero__copy"} -->
            <div class="wp-block-column is-vertically-aligned-center hnm-hero__copy" style="flex-basis:55%">
                <!-- wp:paragraph {"className":"hnm-eyebrow","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.22em","textTransform":"uppercase","fontWeight":"700"}},"textColor":"gold"} -->
                <p class="hnm-eyebrow has-gold-color has-text-color" style="font-size:0.85rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">From Service to Success</p>
                <!-- /wp:paragraph -->

                <!-- wp:heading {"level":1,"className":"hnm-hero__title","style":{"typography":{"fontSize":"clamp(2.75rem, 6.5vw, 5.5rem)","fontWeight":"500","lineHeight":"1.0","letterSpacing":"-0.02em"}},"textColor":"paper"} -->
                <h1 class="wp-block-heading hnm-hero__title has-paper-color has-text-color" style="font-size:clamp(2.75rem, 6.5vw, 5.5rem);font-weight:500;line-height:1.0;letter-spacing:-0.02em">It's her turn.</h1>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"className":"hnm-hero__lede","style":{"typography":{"fontSize":"clamp(1.125rem, 1.6vw, 1.375rem)","lineHeight":"1.55","fontWeight":"400"},"spacing":{"margin":{"top":"1.25rem","bottom":"2.25rem"}}},"textColor":"cream"} -->
                <p class="hnm-hero__lede has-cream-color has-text-color" style="font-size:clamp(1.125rem, 1.6vw, 1.375rem);line-height:1.55;font-weight:400;margin-top:1.25rem;margin-bottom:2.25rem">For female veterans and first responders transitioning out of service — coaching, community, and clarity for the next mission.</p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"className":"hnm-hero__ctas","style":{"spacing":{"blockGap":{"top":"0.75rem","left":"0.75rem"}}}} -->
                <div class="wp-block-buttons hnm-hero__ctas">
                    <!-- wp:button {"backgroundColor":"gold","textColor":"paper","className":"is-style-fill"} -->
                    <div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-paper-color has-gold-background-color has-text-color has-background wp-element-button" href="#beneficiaries">For Women in Transition</a></div>
                    <!-- /wp:button -->

                    <!-- wp:button {"textColor":"paper","className":"is-style-outline","style":{"border":{"color":"#FFFFFF","width":"1px","radius":"999px"}}} -->
                    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-paper-color has-text-color has-border-color wp-element-button" href="#sponsors" style="border-color:#FFFFFF;border-width:1px;border-radius:999px">For Sponsors</a></div>
                    <!-- /wp:button -->

                    <!-- wp:button {"textColor":"paper","className":"is-style-outline","style":{"border":{"color":"#FFFFFF","width":"1px","radius":"999px"}}} -->
                    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-paper-color has-text-color has-border-color wp-element-button" href="#donors" style="border-color:#FFFFFF;border-width:1px;border-radius:999px">Donate</a></div>
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
<!-- /wp:cover -->
