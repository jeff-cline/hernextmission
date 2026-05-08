<?php
/**
 * Title: Her Service — gallery
 * Slug: her-next-mission/her-service
 * Categories: hnm, featured
 * Description: Three-photo gallery honoring Krystalore's service career, framing the founder's lived experience.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();
$photo_1   = esc_url($theme_uri . '/assets/images/service-called-to-serve.jpg');
$photo_2   = esc_url($theme_uri . '/assets/images/service-collage.jpg');
$photo_3   = esc_url($theme_uri . '/assets/images/service-stadium.jpg');
?>
<!-- wp:group {"className":"hnm-section hnm-section--her-service","backgroundColor":"cream","layout":{"type":"constrained","contentSize":"1280px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--her-service has-cream-background-color has-background" style="padding:7rem 1.5rem">

    <!-- wp:paragraph {"align":"center","className":"hnm-eyebrow","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.22em","textTransform":"uppercase","fontWeight":"700"}},"textColor":"gold"} -->
    <p class="has-text-align-center hnm-eyebrow has-gold-color has-text-color" style="font-size:0.85rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">Her Service</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 4vw, 3rem)","fontWeight":"500","lineHeight":"1.1"},"spacing":{"margin":{"bottom":"1.5rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(2rem, 4vw, 3rem);font-weight:500;line-height:1.1;margin-bottom:1.5rem">She walked the path first.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.65","maxWidth":"640px"},"spacing":{"margin":{"left":"auto","right":"auto","bottom":"4rem"}}},"textColor":"ink-soft"} -->
    <p class="has-text-align-center has-ink-soft-color has-text-color" style="font-size:1.125rem;line-height:1.65;max-width:640px;margin-left:auto;margin-right:auto;margin-bottom:4rem">SMSgt Krystalore Crews served 22 years in the U.S. Air Force and Air National Guard, retiring in 2022. The transition from service was the hardest mission of her career — and the reason this work exists.</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="hnm-service-gallery__grid">
        <figure class="hnm-service-gallery__cell">
            <img src="<?php echo $photo_1; ?>" alt="Krystalore in Air Force uniform — Called to Serve" loading="lazy" />
        </figure>
        <figure class="hnm-service-gallery__cell">
            <img src="<?php echo $photo_2; ?>" alt="Service career collage — Air National Guard" loading="lazy" />
        </figure>
        <figure class="hnm-service-gallery__cell">
            <img src="<?php echo $photo_3; ?>" alt="Krystalore in uniform on the field" loading="lazy" />
        </figure>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
