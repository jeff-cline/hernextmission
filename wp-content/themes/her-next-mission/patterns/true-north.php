<?php
/**
 * Title: True North — compass feature
 * Slug: her-next-mission/true-north
 * Categories: hnm, featured
 * Description: A featured section honoring the compass she received as a gift, framed as our "True North."
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri    = get_stylesheet_directory_uri();
$compass_url  = esc_url($theme_uri . '/assets/images/compass-placeholder.svg');
?>
<!-- wp:group {"className":"hnm-section hnm-section--true-north","backgroundColor":"navy-deep","textColor":"cream","layout":{"type":"constrained","contentSize":"1280px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--true-north has-cream-color has-navy-deep-background-color has-text-color has-background" style="padding:7rem 1.5rem">

    <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"3rem","left":"5rem"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center">

        <!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%">
            <!-- wp:html -->
            <figure class="hnm-true-north__compass">
                <img src="<?php echo $compass_url; ?>" alt="A brass compass — placeholder for the True North gift" />
            </figure>
            <!-- /wp:html -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%">
            <!-- wp:paragraph {"className":"hnm-eyebrow","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.18em","textTransform":"uppercase","fontWeight":"600"}},"textColor":"gold"} -->
            <p class="hnm-eyebrow has-gold-color has-text-color" style="font-size:0.85rem;font-weight:600;letter-spacing:0.18em;text-transform:uppercase">True North</p>
            <!-- /wp:paragraph -->

            <!-- wp:heading {"level":2,"textColor":"cream","style":{"typography":{"fontSize":"clamp(2rem, 4vw, 3rem)","fontWeight":"500","lineHeight":"1.1"}}} -->
            <h2 class="wp-block-heading has-cream-color has-text-color" style="font-size:clamp(2rem, 4vw, 3rem);font-weight:500;line-height:1.1">A compass for what comes next.</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.1875rem","lineHeight":"1.7"},"spacing":{"margin":{"top":"1.5rem"}}}} -->
            <p style="font-size:1.1875rem;line-height:1.7;margin-top:1.5rem">A small brass compass — given to our founder by a sister in service — sits at the heart of this work. It's a reminder that even when the map is gone, the direction is still inside you. Her Next Mission helps women find that bearing again.</p>
            <!-- /wp:paragraph -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.0625rem","fontStyle":"italic","fontFamily":"var(--wp--preset--font-family--display)"},"spacing":{"margin":{"top":"1.5rem"}}},"textColor":"gold-soft"} -->
            <p class="has-gold-soft-color has-text-color" style="font-family:var(--wp--preset--font-family--display);font-size:1.0625rem;font-style:italic;margin-top:1.5rem">"You haven't lost the mission. You're between missions. Let's find the next one."</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
