<?php
/**
 * Title: Her Service — gallery
 * Slug: her-next-mission/her-service
 * Categories: hnm, featured
 * Description: Gallery honoring the founder's lived service career, plus a wider band of women across uniformed services. Designed for ~12 image slots so the page reads rich, even before the client adds their own photos.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$photos = [
    ['type' => 'img', 'src' => $theme_uri . '/assets/images/service-called-to-serve.jpg', 'alt' => 'Krystalore in Air Force uniform — Called to Serve',                'caption' => 'Air Force'],
    ['type' => 'img', 'src' => $theme_uri . '/assets/images/service-collage.jpg',         'alt' => 'Service career collage — Air National Guard',                   'caption' => 'Air National Guard'],
    ['type' => 'img', 'src' => $theme_uri . '/assets/images/service-stadium.jpg',         'alt' => 'Krystalore in uniform on the field',                            'caption' => 'In Uniform'],
    ['type' => 'svg', 'src' => $theme_uri . '/assets/images/service-military.svg',        'alt' => 'Woman in military uniform',                                    'caption' => 'Military'],
    ['type' => 'svg', 'src' => $theme_uri . '/assets/images/service-firefighter.svg',     'alt' => 'Woman firefighter',                                            'caption' => 'Fire'],
    ['type' => 'svg', 'src' => $theme_uri . '/assets/images/service-police.svg',          'alt' => 'Woman police officer',                                         'caption' => 'Law Enforcement'],
    ['type' => 'svg', 'src' => $theme_uri . '/assets/images/service-paramedic.svg',       'alt' => 'Woman paramedic',                                              'caption' => 'EMS'],
    ['type' => 'img', 'src' => $theme_uri . '/assets/images/service-called-to-serve.jpg', 'alt' => 'Air Force noncommissioned officer at attention',               'caption' => 'NCO Corps'],
];
?>
<!-- wp:group {"className":"hnm-section hnm-section--her-service","backgroundColor":"cream","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-section--her-service has-cream-background-color has-background" style="padding:7rem 1.5rem">

    <!-- wp:html -->
    <p style="text-align:center;margin:0 0 0.75rem"><span class="hnm-eyebrow">Her Service · Their Service</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2.25rem, 4.2vw, 3.25rem)","fontWeight":"500","lineHeight":"1.08"},"spacing":{"margin":{"bottom":"1.5rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(2.25rem, 4.2vw, 3.25rem);font-weight:500;line-height:1.08;margin-bottom:1.5rem">She walked the path first.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.65","maxWidth":"700px"},"spacing":{"margin":{"left":"auto","right":"auto","bottom":"4rem"}}},"textColor":"ink-soft"} -->
    <p class="has-text-align-center has-ink-soft-color has-text-color" style="font-size:1.125rem;line-height:1.65;max-width:700px;margin-left:auto;margin-right:auto;margin-bottom:4rem">SMSgt Krystalore Crews served 22 years in the U.S. Air Force and Air National Guard, retiring in 2022 — and now stands alongside women across every uniformed service: military, fire, law enforcement, EMS, and beyond.</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="hnm-service-gallery__grid">
        <?php foreach ($photos as $p): ?>
            <figure class="hnm-service-gallery__cell">
                <?php if ($p['type'] === 'svg'): ?>
                    <img src="<?php echo esc_url($p['src']); ?>" alt="<?php echo esc_attr($p['alt']); ?>" loading="lazy" />
                <?php else: ?>
                    <img src="<?php echo esc_url($p['src']); ?>" alt="<?php echo esc_attr($p['alt']); ?>" loading="lazy" />
                <?php endif; ?>
                <figcaption><?php echo esc_html($p['caption']); ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
