<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>

			<div id="site-generator">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>mentions-legales" class="mentionslegales">Mentions Légales</a> - Lazare - 1 rue du Plâtre - 75004 Paris – <a href="<?php echo esc_url( home_url( '/' ) ); ?>contactez-nous" class="mentionslegales">Contact</a> - <a href="<?php echo esc_url( home_url( '/' ) ); ?>newsletter" class="newsletter"><img src="<?php echo get_theme_root_uri(); ?>/lazare/images/newsletter.png" alt="Newsletter" /></a> 
			</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>