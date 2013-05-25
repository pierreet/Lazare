<?php
/*
 *
 * Template Name: actualite-ville
 */
 
 get_header(); // Inclut votre header
 global $post;
 
 $page_id = $post->ID;
?>
<div id="primary">
	<div role="main" id="content">		
		<div class="colonnes deux-tiers-colonnes">
			<h1>Actualité de <?php the_title();?></h1>
			<div class='news_container'>
				<a href="#" class="nav-previous"><span id="prev"><span class="meta-nav">←</span> Précédent</span></a>
				<a href="#" class="nav-next"><span id="next">Suivant <span class="meta-nav">→</span></span></a>
				<div id="news-slideshow" class="cycle">
					<?php
					$i = 0;
					$term = get_term_by('name', 'Actualité de '.get_the_title(), 'category');
					$id = $term->term_id;
					 query_posts( 'showposts=5&cat='.$id.'&order=DESC&orderby=date' );
					 if (have_posts()) :
					 while (have_posts()) : the_post();
					 ?>
					 	<div id="news-slide-<?php echo $i;?>">
							<h2><?php the_title(); ?></h2>
							<?php the_content(); ?>
						</div>
					<?php $i++;?>
					<?php endwhile; ?>
					<?php endif; ?>
				</div>
				<div class="cycle-controls">
				  <div class="news-cycle-pager"><!-- content written from js --></div>
				</div> 
			</div>
			<script type="text/javascript">
				initNewsSlider(<?php echo $i?>);
			</script>
		</div>
		<div class="colonnes un-tier-colonnes">
		<?php $page_data = get_page( $page_id ); ?> 
		<?php echo apply_filters('the_content', $page_data->post_content);?>
		</div>
	</div>
</div>
<?php get_footer(); ?>