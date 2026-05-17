<?php get_header(); ?>

<section class="section" style="padding-top:clamp(3rem,6vw,5rem)">
  <div class="wrap wrap--narrow">
    <span class="eyebrow">Blog</span>
    <h1 style="font-size:clamp(2.2rem,4.5vw,4rem)">Field notes for her next mission.</h1>
    <p class="lede">Stories, resources, and transition guidance for women Veterans and first responders.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="wrap wrap--narrow">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <article style="margin-bottom:2.5rem;padding-bottom:1.75rem;border-bottom:1px solid var(--line)">
        <h2 style="font-size:clamp(1.6rem,3vw,2.3rem);margin-bottom:.4rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <p style="color:var(--ink-mute);font-size:.9rem;text-transform:uppercase;letter-spacing:.12em"><?php echo esc_html(get_the_date()); ?></p>
        <div><?php the_excerpt(); ?></div>
        <p><a class="btn btn--ghost" href="<?php the_permalink(); ?>">Read Post</a></p>
      </article>
    <?php endwhile; else : ?>
      <p>No posts published yet. Add your first post in WordPress Admin.</p>
    <?php endif; ?>

    <div>
      <?php the_posts_pagination(); ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
