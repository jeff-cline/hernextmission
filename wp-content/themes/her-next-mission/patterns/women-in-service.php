<?php
/**
 * Title: Women in Service — wall
 * Slug: her-next-mission/women-in-service
 * Categories: hnm, featured
 * Description: Edge-to-edge banner of women in uniform across services — military, fire, police, EMS. Designed to scale to 8+ photos so the page reads rich.
 * Inserter: yes
 *
 * @package HerNextMission
 */

$theme_uri = get_stylesheet_directory_uri();

$cells = [
    ['src' => $theme_uri . '/assets/images/service-military.svg',         'alt' => 'Woman in military uniform',          'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/service-firefighter.svg',      'alt' => 'Woman firefighter',                  'wide' => true,  'double' => false],
    ['src' => $theme_uri . '/assets/images/service-police.svg',           'alt' => 'Woman police officer',               'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/service-paramedic.svg',        'alt' => 'Woman paramedic',                    'wide' => false, 'double' => true],
    ['src' => $theme_uri . '/assets/images/service-called-to-serve.jpg',  'alt' => 'Woman in Air Force uniform',         'wide' => false, 'double' => false],
    ['src' => $theme_uri . '/assets/images/service-collage.jpg',          'alt' => 'Air National Guard service career',  'wide' => true,  'double' => false],
    ['src' => $theme_uri . '/assets/images/service-stadium.jpg',          'alt' => 'NCO in dress uniform',               'wide' => false, 'double' => false],
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
