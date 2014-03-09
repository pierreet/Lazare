/*	Define the plugin jquery var in order to avoid conflict with other plugin and scripts	*/
var wplazare = jQuery.noConflict();

wplazare(document).ready(function() {	
	wplazare.extend(wplazare.validator.messages, {
		required: "Ce champ est requis",
		remote: "Veuillez v&eacute;rifier ce champ.",
		email: "Veuillez saisir une adresse mail valide.",
		url: "Veuillez saisir une URL valide.",
		date: "Veuillez saisir une date valide.",
		number: "Veuillez saisir un nombre.(ex.: 12,-12.5,-1.3e-2)",
		integer: "Veuillez saisir un nombre sans decimales.",
		digits: "Ce champ n'accepte que des chiffres.",
		creditcard: "Veuillez saisir un num&eacute;ro de carte de cr&eacute;dit valide.",
		equalTo: "Veuillez resaisir la meme valeur.",
		maxlength: wplazare.validator.format("Veuillez ne pas saisir plus de {0} caract&egrave;res."),
		minlength: wplazare.validator.format("Veuillez saisir au moins {0} caract&egrave;res."),
		datetime : "Veuillez saisir une date/heure valide.(aaaa-mm-jjThh:mm:ssZ)",
		'datetime-local': "Veuillez saisir une date/heure locale valide.(aaaa-mm-jjThh:mm:ss)",
		time : "Veuillez saisir une heure valide (hh:mm).",
		color: "Veuillez saisir une couleur valide. (nomm&eacute;e, hexadecimale ou rvb)",
		week:"Veuillez saisir une ann&eacute;e et une semaine. (ex.: 1974-W43)",
		month:"Veuillez saisir une ann&eacute;e et un mois. (ex.: 1974-03)",
		alphabetic:"Veuillez ne saisir que des lettres.",
		alphanumeric : "Veuillez ne saisir que des lettres, soulign&eacute; et chiffres.",
		max: wplazare.validator.format("Veuillez saisir une valeur inf&eacute;rieure ou &eacute;gale &agrave;  {0}."),
		min: wplazare.validator.format("Veuillez saisir une valeur sup&eacute;rieure ou &eacute;gale &agrave;  {0}.")
	});
	wplazare(".jquery_date_picker").datepicker({
        dateFormat : "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: 'c-100:c+10'//Show up to 100 years before and ten years after in the dropdown
     });
	if(wplazare('.tablesorter').length > 0 && wplazare('.tablesorter').find('tr').length > 3){
		var pagerOptions = {

				// target the pager markup - see the HTML block below
				container: wplazare(".pager"),

				// output string - default is '{page}/{totalPages}'
				// possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
				output: '{startRow} &agrave; {endRow} ({totalRows})',

				// apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
				updateArrows: true,

				page: 0,

				// Number of visible rows - default is 10
				size: 20,

				// if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
				// table row set to a height to compensate; default is false
				fixedHeight: true,

				// remove rows from the table to speed up the sort of large tables.
				// setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
				removeRows: false,

				// css class names of pager arrows
				cssNext: '.next', // next page arrow
				cssPrev: '.prev', // previous page arrow
				cssFirst: '.first', // go to first page arrow
				cssLast: '.last', // go to last page arrow
				cssGoto: '.gotoPage', // select dropdown to allow choosing a page

				cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
				cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option

				// class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
				cssDisabled: 'disabled' // Note there is no period "." in front of this class name

			};
		
		wplazare('.tablesorter').tablesorter({
			theme: 'blue',
			widgets: ['zebra','filter']
		});
	}
	else{
		wplazare('.pager').hide();
	}
	
	if(wplazare('.combobox').length > 0){
		wplazare('.combobox').combobox();
	}
	if(wplazare('#prelevement_form').length > 0){
		validatePrelevementForm();
	}
});

function validatePrelevementForm(){
	var rulesTmp = {
        "wp_lazare_forms[banque_bic]": {
            required: true,
            maxlength: 11,
            minlength: 8
        },
        "wp_lazare_forms[banque_iban]": {
            required: true,
            maxlength: 34,
            minlength: 6
        }
		};
	if(wplazare('#prelevement_form').hasClass('locataireCharge')){
		rulesTmp['location'] = { required: true	};
		rulesTmp['valeur'] = { required: true };
	}
	
	wplazare('#banque_iban').change(function(){
		var iban = wplazare(this).val();
		iban = iban.replace(/\s/g, "");
		iban = iban.toLowerCase();
		if(iban.length >= 27){
			if(wplazare('#banque_code').val() == ""){
				wplazare('#banque_code').val(iban.substring(4,9));
			}
			if(wplazare('#banque_code_guichet').val() == ""){
				wplazare('#banque_code_guichet').val(iban.substring(9,14));
			}
			if(wplazare('#banque_code_numero_compte').val() == ""){
				wplazare('#banque_code_numero_compte').val(iban.substring(14,25));
			}
			if(wplazare('#banque_code_cle_rib').val() == ""){
				wplazare('#banque_code_cle_rib').val(iban.substring(25,27));
			}
		}
	});
	
	wplazare("#prelevement_form").validate({
		rules: rulesTmp,
	 	submitHandler: function(form) {
	 		wplazare(form).submit();
		}
	});
}
/* Define the different behavior for the main interface	*/
function wplazareMainInterface(currentType, confirmCancelMessage, listingSlugUrl){
	wplazare("#" + currentType + "_form input, #" + currentType + "_form textarea").keypress(function(){
		wplazare("#" + currentType + "_form_has_modification").val("yes");
	});
	wplazare("#" + currentType + "_form select").change(function(){
		wplazare("#" + currentType + "_form_has_modification").val("yes");
	});
	wplazare("#save").click(function(){
		wplazare("#" + currentType + "_form").attr("action", listingSlugUrl);
		wplazare("#" + currentType + "_form").submit();
	});
	wplazare("#add").click(function(){
		wplazare("#" + currentType + "_form").submit();
	});
	wplazare("#saveandcontinue").click(function(){
		wplazare("#" + currentType + "_action").val(wplazare("#" + currentType + "_action").val() + "andcontinue");
		wplazare("#" + currentType + "_form").submit();
	});
	wplazare(".wplazareCancelButton").click(function(){
		if((wplazare("#" + currentType + "_form_has_modification").val() == "yes")){
			if(!confirm(wplazareConvertAccentTojs(confirmCancelMessage))){
				return false;
			}
		}
	});
}

/*	Allows to output special characters into javascript	*/
function wplazareConvertAccentTojs(text){
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/&Eacute;/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/&eacute;/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}

function initEvents(){
	wplazare('#slideshow').cycle({
		fx:     'fade', 
		timeout: 4000,
		delay:  1000,
        prev:    '#bt-prev',
        next:    '#bt-next',
        pager:   '.cycle-pager',
        pagerAnchorBuilder: pagerFactory,
        fastOnEvent: true 
    });
}

function locataireSort(a, b) {          
    return (a.innerHTML > b.innerHTML) ? 1 : -1;
};