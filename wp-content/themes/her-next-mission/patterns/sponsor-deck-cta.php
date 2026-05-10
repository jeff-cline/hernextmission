<?php
/**
 * Title: Sponsor Deck CTA
 * Slug: her-next-mission/sponsor-deck-cta
 * Categories: hnm
 * Description: Final CTA inviting corporate sponsors to request the deck. mailto-driven.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$cta_deck = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('sponsor-deck') : '#';
?>
<!-- wp:group {"className":"hnm-section hnm-section--deck","layout":{"type":"constrained","contentSize":"960px"},"style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--deck" style="padding:6rem 1.5rem">

    <!-- wp:html -->
    <p style="text-align:center;margin:0 0 0.75rem"><span class="hnm-eyebrow" style="color:#0E1530">Become a Mission Partner</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"textColor":"navy-deep","style":{"typography":{"fontSize":"clamp(2.25rem, 4.2vw, 3.25rem)","fontWeight":"500","lineHeight":"1.08"},"spacing":{"margin":{"bottom":"1.5rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center has-navy-deep-color has-text-color" style="font-size:clamp(2.25rem, 4.2vw, 3.25rem);font-weight:500;line-height:1.08;margin-bottom:1.5rem">Stand with these women.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","textColor":"navy-deep","style":{"typography":{"fontSize":"1.1875rem","lineHeight":"1.65"},"spacing":{"margin":{"bottom":"2.5rem"}}}} -->
    <p class="has-text-align-center has-navy-deep-color has-text-color" style="font-size:1.1875rem;line-height:1.65;margin-bottom:2.5rem">Featured Sponsor ($25k) · Lead Sponsor ($50k) · Mission Partner ($250k naming rights). Request the full deck for tier benefits, audience reach, and live-event activation.</p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
    <div class="wp-block-buttons">
        <!-- wp:button {"backgroundColor":"navy-deep","textColor":"cream"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-cream-color has-navy-deep-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url($cta_deck); ?>">Request Sponsor Deck</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->

</div>
<!-- /wp:group -->
