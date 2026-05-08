<?php
/**
 * Title: Podcast feature
 * Slug: her-next-mission/podcast-feature
 * Categories: hnm, featured
 * Description: Podcast section linking to /podcast/ with the cover art.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri   = get_stylesheet_directory_uri();
$podcast_url = esc_url($theme_uri . '/assets/images/podcast-cover.png');
?>
<!-- wp:group {"className":"hnm-section hnm-section--podcast","backgroundColor":"navy","textColor":"cream","layout":{"type":"constrained","contentSize":"1280px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--podcast has-cream-color has-navy-background-color has-text-color has-background" style="padding:7rem 1.5rem">

    <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"3rem","left":"4rem"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center">

        <!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
            <!-- wp:html -->
            <figure class="hnm-podcast__cover">
                <img src="<?php echo $podcast_url; ?>" alt="Her Next Mission podcast cover — From Service to Success with Krystalore Crews" />
            </figure>
            <!-- /wp:html -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">

            <!-- wp:paragraph {"className":"hnm-eyebrow","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.22em","textTransform":"uppercase","fontWeight":"700"}},"textColor":"gold"} -->
            <p class="hnm-eyebrow has-gold-color has-text-color" style="font-size:0.85rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">The Podcast · From Service to Success</p>
            <!-- /wp:paragraph -->

            <!-- wp:heading {"level":2,"textColor":"paper","style":{"typography":{"fontSize":"clamp(2rem, 4vw, 3rem)","fontWeight":"500","lineHeight":"1.1"}}} -->
            <h2 class="wp-block-heading has-paper-color has-text-color" style="font-size:clamp(2rem, 4vw, 3rem);font-weight:500;line-height:1.1">Her voice. Her story. Her next mission.</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.1875rem","lineHeight":"1.7"},"spacing":{"margin":{"top":"1.5rem","bottom":"2rem"}}}} -->
            <p style="font-size:1.1875rem;line-height:1.7;margin-top:1.5rem;margin-bottom:2rem">Hosted by Krystalore Crews — conversations on transition, wellness, business, leadership, and retreats. Now accepting stories. Watch interviews. Walk the path with women who've stood where you stand.</p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"style":{"spacing":{"blockGap":{"top":"0.75rem","left":"0.75rem"}}}} -->
            <div class="wp-block-buttons">
                <!-- wp:button {"backgroundColor":"gold","textColor":"paper"} -->
                <div class="wp-block-button"><a class="wp-block-button__link has-paper-color has-gold-background-color has-text-color has-background wp-element-button" href="/podcast/">Listen to the Podcast</a></div>
                <!-- /wp:button -->

                <!-- wp:button {"textColor":"paper","className":"is-style-outline","style":{"border":{"color":"#FFFFFF","width":"1px","radius":"999px"}}} -->
                <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-paper-color has-text-color has-border-color wp-element-button" href="/podcast/submit-story/" style="border-color:#FFFFFF;border-width:1px;border-radius:999px">Submit Your Story</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->

        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
