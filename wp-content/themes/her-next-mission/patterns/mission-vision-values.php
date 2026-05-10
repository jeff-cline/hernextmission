<?php
/**
 * Title: Mission, Vision, Values
 * Slug: her-next-mission/mission-vision-values
 * Categories: hnm, featured
 * Description: Editorial 2-column section with a bold image alongside Mission/Vision/Values copy. Visual feature, like the podcast section.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri  = get_stylesheet_directory_uri();
$photo_main = esc_url($theme_uri . '/assets/images/uniform/ml-04.jpg');
$photo_a    = esc_url($theme_uri . '/assets/images/uniform/ff-04.jpg');
$photo_b    = esc_url($theme_uri . '/assets/images/uniform/po-01.jpg');

$cta_intake = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('beneficiary-intake') : '#';
$cta_call   = function_exists('hnm_cta_mailto') ? hnm_cta_mailto('book-a-call')        : '#';
?>
<!-- wp:group {"className":"hnm-section hnm-mission","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-mission" style="padding:7rem 1.5rem">
    <!-- wp:html -->
    <div class="hnm-mission__row">
        <div class="hnm-mission__art">
            <figure class="hnm-mission__photo hnm-mission__photo--main">
                <img src="<?php echo $photo_main; ?>" alt="Woman Veteran in uniform — proud, ready for the next mission" loading="lazy" />
            </figure>
            <figure class="hnm-mission__photo hnm-mission__photo--a">
                <img src="<?php echo $photo_a; ?>" alt="Female firefighter in full gear" loading="lazy" />
            </figure>
            <figure class="hnm-mission__photo hnm-mission__photo--b">
                <img src="<?php echo $photo_b; ?>" alt="Female police officer" loading="lazy" />
            </figure>
        </div>

        <div class="hnm-mission__copy">
            <p class="hnm-mission__eyebrow"><span class="hnm-eyebrow">Our Mission</span></p>
            <h2 class="hnm-mission__title">SHE STILL HAS A MISSION.</h2>
            <p class="hnm-mission__lede">Her Next Mission empowers female Veterans and first responders transitioning out of service to reclaim their identity, rebuild their confidence, and discover their next mission — through coaching, community, fitness, and transformative experiences that honor their service and fuel their future.</p>

            <div class="hnm-mission__threeup">
                <div>
                    <h3>Mission</h3>
                    <p>Walk every woman through the transition no one prepared her for — until she finds the next mission worth running toward.</p>
                </div>
                <div>
                    <h3>Vision</h3>
                    <p>A world where every woman who served never has to navigate her transition alone — and her next chapter is as purposeful as the one she closed.</p>
                </div>
                <div>
                    <h3>Values</h3>
                    <ul class="hnm-mission__values">
                        <li><strong>Sister-led, never solo.</strong> Women who walked it, walking with you.</li>
                        <li><strong>Whole-woman healing.</strong> Mind, body, breath — somatic and nervous-system tools that meet the body where talk can't.</li>
                        <li><strong>Identity reclaimed.</strong> When the uniform comes off, who you were doesn't.</li>
                        <li><strong>Direct, never soft.</strong> Plain language. No fluff. No pity. Real work.</li>
                        <li><strong>Access without gates.</strong> Scholarships built in. Cost is never the wall.</li>
                        <li><strong>Excellence, on her terms.</strong> She gave the best of herself. She gets the best back.</li>
                    </ul>
                </div>
            </div>

            <div class="hnm-mission__ctas">
                <a class="hnm-btn hnm-btn--gold" href="<?php echo esc_url($cta_intake); ?>">For Women in Transition</a>
                <a class="hnm-btn hnm-btn--ghost" href="<?php echo esc_url($cta_call); ?>">Book a Call</a>
            </div>
        </div>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
