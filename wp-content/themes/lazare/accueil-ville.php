<?php
/*
 *
 * Template Name: accueil-ville
 */
 
 get_header(); // Inclut votre header
 global $post;
 
 $page_id = $post->ID;
?>
<div id="primary">
	<div role="main" id="content">		
		<div class="colonnes">
		<?php $page_data = get_page( $page_id ); ?> 
		<?php echo apply_filters('the_content', $page_data->post_content);?>
		</div>
	</div>
</div>
<?php get_footer(); ?>