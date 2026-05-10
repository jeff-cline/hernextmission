<?php
/**
 * Title: True North — compass feature
 * Slug: her-next-mission/true-north
 * Categories: hnm, featured
 * Description: Featured section honoring the brass compass given to Krystalore by Melissa, framed as our "True North."
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri   = get_stylesheet_directory_uri();
$compass_url = esc_url($theme_uri . '/assets/images/compass.jpg');
?>
<!-- wp:group {"className":"hnm-section hnm-true-north","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-true-north" style="padding:7rem 1.5rem">

    <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"3rem","left":"5rem"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center">

        <!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%">
            <!-- wp:html -->
            <figure class="hnm-true-north__compass">
                <img src="<?php echo $compass_url; ?>" alt="Brass compass with engraving: SMSgt Krystalore Crews Retired 2022 — Congratulations! You sacrificed, overcame, and conquered. A true leader and inspiration. Love, Melissa" />
            </figure>
            <!-- /wp:html -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%">
            <!-- wp:html -->
            <p style="margin:0 0 1rem"><span class="hnm-eyebrow">True North</span></p>
            <!-- /wp:html -->

            <!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"clamp(2.25rem, 4.2vw, 3.25rem)","fontWeight":"500","lineHeight":"1.08"}}} -->
            <h2 class="wp-block-heading" style="font-size:clamp(2.25rem, 4.2vw, 3.25rem);font-weight:500;line-height:1.08">Even when the map is gone, the direction is still inside you.</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.1875rem","lineHeight":"1.7"},"spacing":{"margin":{"top":"1.5rem"}}},"textColor":"ink-soft"} -->
            <p class="has-ink-soft-color has-text-color" style="font-size:1.1875rem;line-height:1.7;margin-top:1.5rem">When SMSgt Krystalore Crews retired in 2022, a sister in service — Melissa — gave her a brass compass. Inscribed inside the cover are these words:</p>
            <!-- /wp:paragraph -->

            <!-- wp:quote {"className":"hnm-true-north__inscription","style":{"typography":{"fontSize":"1.25rem","fontFamily":"var(--wp--preset--font-family--display)","fontStyle":"italic","lineHeight":"1.5"},"spacing":{"padding":{"left":"1.5rem"},"margin":{"top":"1.5rem","bottom":"1.5rem"}},"border":{"left":{"color":"#C9A04A","width":"3px"}}}} -->
            <blockquote class="wp-block-quote hnm-true-north__inscription has-border-color" style="border-left-color:#C9A04A;border-left-width:3px;padding-left:1.5rem;margin-top:1.5rem;margin-bottom:1.5rem;font-family:var(--wp--preset--font-family--display);font-size:1.25rem;font-style:italic;line-height:1.5"><p>"You sacrificed, overcame, and conquered. A true leader and inspiration."</p><cite>— Love, Melissa</cite></blockquote>
            <!-- /wp:quote -->

            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.0625rem","lineHeight":"1.7"},"spacing":{"margin":{"top":"1.5rem"}}}} -->
            <p style="font-size:1.0625rem;line-height:1.7;margin-top:1.5rem">That compass is Her Next Mission's True North — the reminder we offer every woman who walks through this door. You haven't lost the mission. You're between missions. Let's find the next one.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
