<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
  <div class="wrap site-header__row">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Her Next Mission home">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="" width="64" height="64">
      <span class="brand__text">
        <strong>HER NEXT MISSION</strong>
        <span>From Service to Success</span>
      </span>
    </a>

    <button class="nav__toggle" data-nav-toggle aria-expanded="false" aria-label="Open menu">☰</button>

    <nav class="nav" data-nav aria-label="Primary">
      <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'items_wrap' => '%3$s',
                'fallback_cb' => false,
                'depth' => 1,
            ]);
            echo '<a href="' . esc_url(home_url('/')) . '">Blog</a>';
        } else {
            hnm_default_primary_links();
        }
      ?>
    </nav>
  </div>
</header>
<main>
