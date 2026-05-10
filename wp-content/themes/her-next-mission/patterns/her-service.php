<?php
/**
 * Title: Her Service — gallery
 * Slug: her-next-mission/her-service
 * Categories: hnm, featured
 * Description: Gallery honoring the founder's lived service career, plus women across uniformed services. 12-photo grid.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$photos = [
    ['src' => $theme_uri . '/assets/images/service-called-to-serve.jpg',  'alt' => 'Krystalore in Air Force uniform — Called to Serve',         'caption' => 'Air Force'],
    ['src' => $theme_uri . '/assets/images/service-collage.jpg',          'alt' => 'Service career collage — Air National Guard',               'caption' => 'Air National Guard'],
    ['src' => $theme_uri . '/assets/images/service-stadium.jpg',          'alt' => 'Krystalore in uniform on the field',                        'caption' => 'In Uniform'],
    ['src' => $theme_uri . '/assets/images/uniform/ml-04.jpg',            'alt' => 'Female U.S. Marine in camouflage uniform',                  'caption' => 'Military'],
    ['src' => $theme_uri . '/assets/images/uniform/ff-01.jpg',            'alt' => 'Smiling firefighter in full gear',                          'caption' => 'Fire'],
    ['src' => $theme_uri . '/assets/images/uniform/po-01.jpg',            'alt' => 'Confident female police officer in uniform',                'caption' => 'Law Enforcement'],
    ['src' => $theme_uri . '/assets/images/uniform/em-01.jpg',            'alt' => 'Female paramedic standing beside ambulance',                'caption' => 'EMS'],
    ['src' => $theme_uri . '/assets/images/uniform/ml-02.jpg',            'alt' => 'Woman Veteran in camouflage uniform',                       'caption' => 'Veteran'],
    ['src' => $theme_uri . '/assets/images/uniform/ff-04.jpg',            'alt' => 'Firefighter in full protective gear before fire truck',     'caption' => 'First Responder'],
    ['src' => $theme_uri . '/assets/images/uniform/po-02.jpg',            'alt' => 'Smiling policewoman in uniform on sunny street',            'caption' => 'Officer'],
    ['src' => $theme_uri . '/assets/images/uniform/ml-05.jpg',            'alt' => 'Woman in military uniform during training',                 'caption' => 'In Service'],
    ['src' => $theme_uri . '/assets/images/uniform/em-02.jpg',            'alt' => 'Female paramedic in uniform with stethoscope',              'caption' => 'Paramedic'],
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
    <p class="has-text-align-center has-ink-soft-color has-text-color" style="font-size:1.125rem;line-height:1.65;max-width:700px;margin-left:auto;margin-right:auto;margin-bottom:4rem">SMSgt Krystalore Crews served 22 years in the U.S. Air Force and Air National Guard, retiring in 2024 — and now stands alongside women across every uniformed service: military, fire, law enforcement, EMS, and beyond.</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="hnm-service-gallery__grid">
        <?php foreach ($photos as $p): ?>
            <figure class="hnm-service-gallery__cell">
                <img src="<?php echo esc_url($p['src']); ?>" alt="<?php echo esc_attr($p['alt']); ?>" loading="lazy" />
                <figcaption><?php echo esc_html($p['caption']); ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
