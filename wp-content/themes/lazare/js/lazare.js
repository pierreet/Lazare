function unchecke(id_element,montant,option) {
	//modifier la phrase
	var valeur_don = 0
	if (option==undefined) {
		valeur_don = jQuery("#"+id_element.id).val();
		var don_prix =((valeur_don*0.25));
		if(valeur_don > 521)
			don_prix =(((valeur_don-521)*0.34)+521*0.25);
	} 
	else {
		valeur_don = montant;
		var don_prix =((valeur_don*0.25));
		if(valeur_don > 521)
			don_prix =(((valeur_don-521)*0.34)+521*0.25);
	}
	jQuery("#simulation").val(valeur_don);
	jQuery("#result").val(Math.round(don_prix*100)/100);
	jQuery('#don_selectionne').val(id_element.id);
	jQuery('#valeur').val(valeur_don);

	var clef = 1;
	//vider les inputs
	while (clef < 3) {
		if ('offre_libre_'+clef != id_element.id) {
			if (document.getElementById('offre_libre_'+clef)) {
				var caseAcocher = document.getElementById('offre_libre_'+clef);
				caseAcocher.value = "";
			}
		}
		clef ++;
	}
	clef = 1;
	//vider les radios
	while (clef < 9) {
		if ('offre_'+clef != id_element.id) {
			if (document.getElementById('offre_'+clef)) {
				var caseAcocher = document.getElementById('offre_'+clef);
				caseAcocher.checked = false;
			}
		} else {
			if (document.getElementById('offre_'+clef)) {
				var caseAcocher = document.getElementById('offre_'+clef);
				caseAcocher.checked = true;
			}
		}
		clef ++;
	}
}

var maxHeight = 0;
function setHeight(column) {
    column = jQuery(column);
    column.each(function() {       
        if(jQuery(this).height() > maxHeight) {
            maxHeight = jQuery(this).height();;
        }
    });
    column.height(maxHeight);
}

function initDon(){
	jQuery(document).ready(function () {

		jQuery('.list-wrap2 div').hide();
		jQuery('.list-wrap2 div:first').show();
		jQuery('ul.tabs2 li a:first').addClass('active');
		jQuery('ul.tabs2 li a').click(function(){
			jQuery('ul.tabs2 li a').removeClass('active');
			jQuery(this).addClass('active');
			var currentTab = jQuery(this).attr('href');
			jQuery('.list-wrap2 div').hide();
			jQuery(currentTab).show();
			return false;
		});
		setHeight('.colonnes');

	});
}

function pagerFactory(idx, slide) {
    var s = idx > 2 ? ' style="display:none"' : '';
    return '<a href="#slide-'+(idx+1)+'">'+(idx+1)+'</a>';
};

function initHeaderSlider(){
	jQuery(document).ready(function () {
		jQuery('#slideshow').cycle({
			fx:     'fade', 
			timeout: 4000,
			delay:  1000,
	        prev:    '#bt-prev',
	        next:    '#bt-next',
	        pager:   '.cycle-pager',
	        pagerAnchorBuilder: pagerFactory,
	        fastOnEvent: true 
	    });
	
		jQuery('#bt-playpause').click(function() {
		    var obj = jQuery(this);
		    if (obj.hasClass('pause')) {
		        obj.removeClass('pause').addClass('play').text('Play');
		        jQuery('#slideshow').cycle('pause'); 
		    } else if (obj.hasClass('play')) {
		        obj.removeClass('play').addClass('pause').text('Pause');
		        jQuery('#slideshow').cycle('resume');
		    }
		});
		
		jQuery('#slideshow .infos').show();
	});
}

function initEventsSlider(eventsNumber){
	jQuery(document).ready(function () {
		jQuery('#events-slideshow').cycle({
			fx:     'fade', 
			timeout: 4000,
			delay:  1000,
	        prev:    '#bt-events-prev',
	        next:    '#bt-events-next'
	    });
		if(eventsNumber < 2){
			jQuery('#bt-events-prev').css('display','none');
			jQuery('#bt-events-next').css('display','none');
		}
	});
}

function newsPagerFactory(idx, slide) {
    var s = idx > 2 ? ' style="display:none"' : '';
    return '<a href="#news-slide-'+(idx+1)+'">'+(idx+1)+'</a>';
};

function initNewsSlider(eventsNumber){
	jQuery(window).load(function () {
		jQuery('#news-slideshow').cycle({
			fx:     'fade', 
			delay:  1000,
			timeout:  0,
			prev:    '#prev',
	        next:    '#next',
	        after: onAfter,
			pager:   '.news-cycle-pager',
	        pagerAnchorBuilder: newsPagerFactory,
	        fastOnEvent: true 
	    });
	});
}


function onAfter(curr, next, opts, fwd) {
	var index = opts.currSlide;
	jQuery('#prev')[index == 0 ? 'hide' : 'show']();
	jQuery('#next')[index == opts.slideCount - 1 ? 'hide' : 'show']();
	//get the height of the current slide
	var jQueryht = jQuery(this).height();
	//set the container's height to that of the current slide
	jQuery(this).parent().animate({height: jQueryht});
}


function dispChxRecu(){
	if(jQuery('#chxRecu').is(':checked'))
		jQuery('#choixRecu').show();
	else jQuery('#choixRecu').hide();
}

function openPopup(){
	jQuery('.open_popup').each(function(){
		var popID = this;

		jQuery(popID).fadeIn().css({
			'width': '500px'
		})
		.prepend('');

		var popMargTop = (jQuery(popID).height() + 80) / 2;
		var popMargLeft = (jQuery(popID).width() + 80) / 2;

		jQuery(popID).css({
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});

		jQuery('body').append('');
		jQuery('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

		jQuery('a.close, #fade,.open_popup button').live('click', function() { 
			jQuery('#fade , .popup_block').fadeOut();
			return false;
		});
	});
}

jQuery(document).ready(function(){
	if(jQuery('.init_don').length > 0){
		initDon();
	}
	if(jQuery('.open_popup').length > 0){
		openPopup();
	}
	jQuery('#registerform').addClass('validate');
	if(jQuery('.validate').length > 0){
		jQuery('.validate').validate();
	}
});