<?php
/**
 * Title: Podcast feature
 * Slug: her-next-mission/podcast-feature
 * Categories: hnm, featured
 * Description: Podcast section with the v2 cover art.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri   = get_stylesheet_directory_uri();
$podcast_url = esc_url($theme_uri . '/assets/images/podcast-cover-v2.png');

$cta_listen   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('newsletter')     : '#';
$cta_guest    = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('podcast-guest')  : '#';
?>
<!-- wp:group {"className":"hnm-section hnm-podcast","textColor":"cream","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-podcast has-cream-color has-text-color" style="padding:7rem 1.5rem">

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

            <!-- wp:html -->
            <p style="margin:0 0 1rem;color:#E8C870;font-size:0.78rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">The Podcast · From Service to Success</p>
            <!-- /wp:html -->

            <!-- wp:heading {"level":2,"textColor":"paper","style":{"typography":{"fontSize":"clamp(2.25rem, 4.2vw, 3.25rem)","fontWeight":"500","lineHeight":"1.08"}}} -->
            <h2 class="wp-block-heading has-paper-color has-text-color" style="font-size:clamp(2.25rem, 4.2vw, 3.25rem);font-weight:500;line-height:1.08">Her voice. Her story. Her next mission.</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.1875rem","lineHeight":"1.7"},"spacing":{"margin":{"top":"1.5rem","bottom":"2rem"}}}} -->
            <p style="font-size:1.1875rem;line-height:1.7;margin-top:1.5rem;margin-bottom:2rem">Hosted by Krystalore Crews — conversations on transition, wellness, business, leadership, and retreats. Now accepting stories. Watch interviews. Walk the path with women who've stood where you stand.</p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"style":{"spacing":{"blockGap":{"top":"0.75rem","left":"0.75rem"}}}} -->
            <div class="wp-block-buttons">
                <!-- wp:button {"backgroundColor":"gold","textColor":"navy-deep"} -->
                <div class="wp-block-button"><a class="wp-block-button__link has-navy-deep-color has-gold-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url($cta_listen); ?>">Get Episode Updates</a></div>
                <!-- /wp:button -->

                <!-- wp:button {"textColor":"paper","className":"is-style-outline","style":{"border":{"color":"#FFFFFF","width":"1px","radius":"999px"}}} -->
                <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-paper-color has-text-color has-border-color wp-element-button" href="<?php echo esc_url($cta_guest); ?>" style="border-color:#FFFFFF;border-width:1px;border-radius:999px">Submit Your Story</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->

        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
