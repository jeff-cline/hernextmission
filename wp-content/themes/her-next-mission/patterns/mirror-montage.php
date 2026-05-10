<?php
/**
 * Title: Mirror Montage — uniform / civilian pairs
 * Slug: her-next-mission/mirror-montage
 * Categories: hnm, featured
 * Description: Editorial echo of the founder's mirror photo — pairs of women across services (military, fire, police, EMS) in uniform alongside their civilian counterparts. Same emotional beat, multiplied for the audience.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$pairs = [
    [
        'label'   => 'Military',
        'uniform' => $theme_uri . '/assets/images/uniform/ml-04.jpg',
        'biz'     => $theme_uri . '/assets/images/civilian/biz-04.jpg',
        'alt_u'   => 'Female U.S. Marine in camouflage uniform',
        'alt_b'   => 'Woman Veteran in business attire',
    ],
    [
        'label'   => 'Fire',
        'uniform' => $theme_uri . '/assets/images/uniform/ff-04.jpg',
        'biz'     => $theme_uri . '/assets/images/civilian/biz-05.jpg',
        'alt_u'   => 'Female firefighter in full gear',
        'alt_b'   => 'Woman in business attire after the uniform',
    ],
    [
        'label'   => 'Law Enforcement',
        'uniform' => $theme_uri . '/assets/images/uniform/po-01.jpg',
        'biz'     => $theme_uri . '/assets/images/civilian/biz-02.jpg',
        'alt_u'   => 'Female police officer in uniform',
        'alt_b'   => 'Woman in business attire — next chapter',
    ],
    [
        'label'   => 'EMS',
        'uniform' => $theme_uri . '/assets/images/uniform/em-02.jpg',
        'biz'     => $theme_uri . '/assets/images/civilian/biz-06.jpg',
        'alt_u'   => 'Female paramedic in uniform',
        'alt_b'   => 'Woman in business attire holding her future',
    ],
];
?>
<!-- wp:group {"className":"hnm-section hnm-mirror","layout":{"type":"constrained","contentSize":"1320px"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group hnm-section hnm-mirror" style="padding:7rem 1.5rem">

    <!-- wp:html -->
    <p style="text-align:center;margin:0 0 0.75rem"><span class="hnm-eyebrow">Then · Now · Next</span></p>
    <!-- /wp:html -->

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2.25rem, 4.4vw, 3.5rem)","fontWeight":"500","lineHeight":"1.05"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
    <h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(2.25rem, 4.4vw, 3.5rem);font-weight:500;line-height:1.05;margin-bottom:1rem">The woman in the uniform is still in the mirror.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.65","maxWidth":"720px"},"spacing":{"margin":{"left":"auto","right":"auto","bottom":"4rem"}}},"textColor":"ink-soft"} -->
    <p class="has-text-align-center has-ink-soft-color has-text-color" style="font-size:1.125rem;line-height:1.65;max-width:720px;margin-left:auto;margin-right:auto;margin-bottom:4rem">Across military, fire, law enforcement, and EMS — the woman she was hasn't gone anywhere. The uniform changed. The mission did too.</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="hnm-mirror__grid">
        <?php foreach ($pairs as $p): ?>
            <figure class="hnm-mirror__pair">
                <span class="hnm-mirror__cell hnm-mirror__cell--uniform">
                    <img src="<?php echo esc_url($p['uniform']); ?>" alt="<?php echo esc_attr($p['alt_u']); ?>" loading="lazy" />
                </span>
                <span class="hnm-mirror__divider" aria-hidden="true"></span>
                <span class="hnm-mirror__cell hnm-mirror__cell--biz">
                    <img src="<?php echo esc_url($p['biz']); ?>" alt="<?php echo esc_attr($p['alt_b']); ?>" loading="lazy" />
                </span>
                <figcaption class="hnm-mirror__label"><?php echo esc_html($p['label']); ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->

</div>
<!-- /wp:group -->
