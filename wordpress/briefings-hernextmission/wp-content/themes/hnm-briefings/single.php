<?php get_header(); ?>
<section class="section section--tight">
  <div class="wrap wrap--narrow prose">
    <?php while (have_posts()) : the_post(); ?>
      <article>
        <h1 style="font-size:clamp(2rem,4.2vw,3.4rem)"><?php the_title(); ?></h1>
        <p style="color:var(--ink-mute);font-size:.9rem;text-transform:uppercase;letter-spacing:.12em"><?php echo esc_html(get_the_date()); ?></p>
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </div>
</section>
<?php get_footer(); ?>
