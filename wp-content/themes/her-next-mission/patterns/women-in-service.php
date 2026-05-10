<?php
/**
 * Title: Women in Service — wall
 * Slug: her-next-mission/women-in-service
 * Categories: hnm, featured
 * Description: Edge-to-edge banner of women in uniform across services — military, fire, police, EMS — using royalty-free Pexels stock photos.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$cells = [
    ['src' => $theme_uri . '/assets/images/uniform/ml-01.jpg',  'alt' => 'Woman veteran in camouflage uniform — close-up portrait',          'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/uniform/ff-02.jpg',  'alt' => 'Female firefighter beside fire truck',                              'wide' => true,  'double' => false],
    ['src' => $theme_uri . '/assets/images/uniform/po-03.jpg',  'alt' => 'Female police officer outdoors in uniform',                         'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/uniform/em-03.jpg',  'alt' => 'Female paramedic in EMS uniform',                                   'wide' => false, 'double' => true],
    ['src' => $theme_uri . '/assets/images/uniform/ml-03.jpg',  'alt' => 'Female Marine in camouflage uniform',                               'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/uniform/ff-05.jpg',  'alt' => 'Firefighter in uniform and helmet by fire truck',                   'wide' => true,  'double' => false],
    ['src' => $theme_uri . '/assets/images/uniform/po-04.jpg',  'alt' => 'Policewoman in blue uniform on the street',                         'wide' => false, 'double' => false],
];
?>
<!-- wp:group {"className":"hnm-wall","layout":{"type":"constrained","contentSize":"100%"},"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-group hnm-wall" style="padding:0">
    <!-- wp:html -->
    <div class="hnm-wall__grid">
        <?php foreach ($cells as $c):
            $cls = 'hnm-wall__cell';
            if (!empty($c['wide']))   { $cls .= ' hnm-wall__cell--wide'; }
            if (!empty($c['double'])) { $cls .= ' hnm-wall__cell--double'; }
        ?>
            <figure class="<?php echo esc_attr($cls); ?>">
                <img src="<?php echo esc_url($c['src']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" loading="lazy" />
            </figure>
        <?php endforeach; ?>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
