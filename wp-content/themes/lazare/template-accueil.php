<?php
/*
 *
 * Template Name: template-accueil
 */
 
 get_header();
 global $post;
 
 $page_id = $post->ID;
?>
<div id="primary">
	<div role="main" id="content">		
		<div class="colonnes quatre-colonnes">
			<ul class="access access1"><li><a href="<?php echo get_category_link(14); ?>">Témoignages</a></li></ul>
			<?php
			$i = 0;
			 query_posts( 'showposts=1&cat=14&orderby=rand' );
			 if (have_posts()) :
			 while (have_posts()) : the_post();
			 ?>
			 	<?php the_content();?>
			<?php $i++;?>
			<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<?php $page_data = get_page( $page_id ); ?> 
		<?php echo apply_filters('the_content', $page_data->post_content);?>
	</div>
</div>
<?php get_footer(); ?>