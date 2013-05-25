<?php
/**
* Help using lazare events
* 
*	Help using lazare events
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different tools for the entire plugin
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_events
{
	function displayEvents(){
		if (class_exists('EM_Events')) {
		$return = '
		<div class="colonnes deux-colonnes-un-tiers">'.em_get_events().'		
		</div>
		<div class="colonnes deux-colonnes-deux-tiers">
		<div class="events_container">
		<p id="bt-events-prev" class="nav-item"><a href="#" class="bt-prev-2"><img src="'.get_theme_root_uri().'/lazare/images/lazare-arrows.png" alt="Image précédente" class="button"></a></p>
		<p id="bt-events-next" class="nav-item"><a href="#" class="bt-next-2"><img src="'.get_theme_root_uri().'/lazare/images/lazare-arrows.png" alt="Image suivante" class="button"></a></p>
		<div id="events-slideshow" class="events-cycle">
		';  
			
			$events = EM_Events::get();
			$i=1;
			foreach ($events as $event){
				$url=get_home_url().'/events/'.$event->event_slug;
				$title=$event->post_title;
				$content=$event->post_excerpt;
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $event->post_id ), 'single-post-thumbnail' );
				$image_url = '';
				if(!empty($image)) $image_url = $image[0];
				$return .='
				<div id="slide-'.$i.'"> 
					<div class="colonnes">
						<a href="" title="'.$title.'">
							<img src="'.$image_url.'" alt="'.$title.'" class="smooth_slider_thumbnail" >
						</a>
					</div>
					<div class="colonnes">
						<h2 class="title">'.$title.'</h2>
				  	  	<p class="text">'.$content.'</p>
				  	  	<p class="more"><a href="'.$url.'" title="'.$title.'">En savoir plus</a></p>
				  	</div>                 
				</div> ';
				$i++;
			}
			$return .='</div></div></div><script type="text/javascript">initEventsSlider('.count($events).');</script>';
		}
		if($i == 1) return '<p>Aucun &eacute;v&egrave;nement n\'est programm&eacute; actuellement.</p>';
		return $return;
	}
}