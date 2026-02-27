<?php
get_header();
?>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-content">
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<article class="entry">
			<h2 class="entry-title"><?php esc_html_e('Постов пока нет.', 'bcc-project'); ?></h2>
		</article>
	<?php endif; ?>


<?php
get_footer();
