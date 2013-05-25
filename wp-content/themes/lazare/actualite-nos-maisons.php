<?php
/*
 *
 * Template Name: actualite-nos-maisons
 */
 
 get_header(); // Inclut votre header
 global $post;
 
 $page_id = $post->ID;
?>
<div id="primary">
	<div role="main" id="content">		
		<div class="nos-maisons-news">
			<?php
			$term = get_term_by('name', 'Nos Maisons', 'category');
			$id = $term->term_id;
			 query_posts( 'showposts=1&cat='.$id.'&order=DESC&orderby=date' );
			 if (have_posts()) :
			 while (have_posts()) : the_post();
			 ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<?php $page_data = get_page( $page_id ); ?> 
		<?php echo apply_filters('the_content', $page_data->post_content);?>
	</div>
</div>
<?php get_footer(); ?>