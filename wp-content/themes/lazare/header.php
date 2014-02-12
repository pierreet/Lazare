<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_enqueue_script("jquery"); ?>

<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35710321-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<header id="branding" role="banner">
			
			<hgroup>
				<h1 id="site-title"><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
			</hgroup>
			
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if( get_the_ID() == 14 ):?>
					<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-lyon.png" alt="Lazare Lyon" id="bandeau-lazare" />
				<?php elseif( get_the_ID() == 19 ):?>
					<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-toulouse.png" alt="Lazare Toulouse" id="bandeau-lazare" />
				<?php elseif( get_the_ID() == 17 ):?>
					<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-toulon.png" alt="Lazare Toulon" id="bandeau-lazare" />
				<?php elseif( get_the_ID() == 21 ):?>
					<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-nantes.png" alt="Lazare Nantes" id="bandeau-lazare" />
                <?php elseif( get_the_ID() == 180 ):?>
                    <img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-paris.png" alt="Lazare Paris" id="bandeau-lazare" />
                <?php elseif( get_the_ID() == 1328 ):?>
                    <img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-lille.png" alt="Lazare Lille" id="bandeau-lazare" />
                <?php elseif( get_the_ID() == 1540 ):?>
                    <img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header-marseille.png" alt="Lazare Marseille" id="bandeau-lazare" />
				<?php else:?>
				 	<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/header.png" alt="Lazare" id="bandeau-lazare" />	
				<?php endif;?>			
			</a>
			<img src="<?php echo get_theme_root_uri(); ?>/lazare/images/lignes.png" alt="lignes" class="lignes" />
			
			<div class="cycle-container">
				<p id="bt-prev" class="nav-item"><a href="#" class="bt-prev-2"><img src="<?php echo get_theme_root_uri(); ?>/lazare/images/lazare-arrows.png" alt="Image précédente" class="button"></a></p>
				<p id="bt-next" class="nav-item"><a href="#" class="bt-next-2"><img src="<?php echo get_theme_root_uri(); ?>/lazare/images/lazare-arrows.png" alt="Image suivante" class="button"></a></p>
				<div id="slideshow" class="cycle">
					<?php 
						$images =& get_children( 'post_type=attachment&meta_key=_wp_attachment_is_custom_header&meta_value=' . get_option('stylesheet').'&orderby=none' );
						
						$i = 1;
						foreach( $images as $imageID => $imagePost ):
							$pieces = explode("\n", $imagePost->post_content);?>
							<div id="slide-<?php echo $i;?>">
								<a href="<?php echo $imagePost->post_excerpt; ?>">
									<div class="infos">  
										<p class="type"><?php if($pieces[0] != $imagePost->guid) echo $pieces[0]; ?></p>
							  	  	    <h2 class="title"><?php if(count($pieces)>1) echo $pieces[1]; ?></h2>
							  	  	    <p class="subtitle"><?php if(count($pieces)>2) echo $pieces[2]; ?></p>
							  	  	    <p class="location"><?php if(count($pieces)>3) echo $pieces[3]; ?></p>
									</div>                  
									<div class="img">
                                        <?php
                                        $image = wp_get_attachment_image_src($imageID, 'two' );

                                        if ($image) : ?>
                                            <img class="attachment-two" src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
                                        <?php endif; ?>
									</div>
								</a>
							</div> 
							<?php $i++;?>
						<?php endforeach; ?>    
				</div>
				<div class="cycle-controls">
				  <a id="bt-playpause" title="Pause" class="pause">Pause</a>
				  <div class="cycle-pager"><!-- content written from js --></div>
				</div>  
			</div>		
			
			<script type="text/javascript">
			initHeaderSlider();
			</script>
			
			
			<nav id="access" role="navigation">
				<h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3>
				<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
				<div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
				<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
				<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #access -->
	</header><!-- #branding -->


	<div id="main" class="one-column">