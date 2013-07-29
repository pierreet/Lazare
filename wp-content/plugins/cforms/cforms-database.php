<?php
###
### Please see cforms.php for more information
###

### DB settings
global $wpdb;
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### Check Whether User Can Manage Database
check_access_priv('track_cforms');

### New global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');
$plugindir   = $cformsSettings['global']['plugindir'];

$cforms_root = $cformsSettings['global']['cforms_root'];

### check if pre-9.0 update needs to be made
if( $cformsSettings['global']['update'] )
	require_once (dirname(__FILE__) . '/update-pre-9.php');

### if all data has been erased quit
if ( check_erased() )
	return;

### check for abspath.php
abspath_check();

wp_enqueue_script('jquery-ui-dialog');
 wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css'); 

?>
<div class="wrap" id="top">
	<div id="icon-cforms-tracking" class="icon32"><br/></div><h2><?php _e('Gestion des inscriptions','cforms')?></h2>

	<!--<p><?php _e('All your recorded form submissions are listed below. View individual entries or a whole bunch and download as XML, TAB or CSV formatted file. Attachments can be accessed in the details section (<strong>View records</strong>). When deleting entries, associated attachments will be removed, too! ', 'cforms') ?></p>

	<p class="ex" style="margin-bottom:30px;"><?php _e('If you want to select <strong>ALL</strong> entries, e.g. for download, simply don\'t select any particular row. When <strong>viewing records</strong>: Fields with a <em>grey background</em> can be clicked on and edited!', 'cforms') ?></p>
-->
	<div id="ctrlmessage"></div>
	<div class="bborderx"><table id="flex1" style="display:none"><tr><td></td></tr></table></div>
	<div id="entries"></div>
	<div id="geturl" title="<?php echo $cforms_root; ?>/js/include/"></div>

	<?php
	### if called from dashboard
	$dashboard = '';
    if ( $_GET['d-id'] ){
	    $dashboard = "qtype: 'id', query: '".$_GET['d-id']."',";
	}
	?>

<script type="text/javascript">

jQuery('#flex1').dblclick( function (e) {
 
	target = jQuery(e.target); 
	while(target.get(0).tagName != "TR"){ 
		target = target.parent(); 
	} 

	target.addClass('trSelected');

	jQuery('.add').click();
});

jQuery("#flex1").flexigrid ( {
	url: '<?php echo $cforms_root.'/js/include/lib_database_overview.php'; ?>',
	dataType: 'xml',
	colModel : [
		{display: '#', name : 'id', width : 40, sortable : true, align: 'center'},
		//{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id', width : 240, sortable : true, align: 'center'},
		{display: '<?php _e('Nom','cforms'); ?>', name : 'last_name', width : 170, sortable : true, align: 'center'},
		{display: '<?php _e('Prénom','cforms'); ?>', name : 'first_name', width : 170, sortable : true, align: 'center'},
		{display: '<?php _e('Adresse e-mail','cforms'); ?>', name : 'email', width : 170, sortable : true, align: 'center'},
		{display: '<?php _e('Statut','cforms'); ?>', name : 'state', width : 110, sortable : true, align: 'center'},
		{display: '<?php _e('Note(s)','cforms'); ?>', name : 'note', width : 140, sortable : true, align: 'center'},
		{display: '<?php _e('Date d&rsquo;inscription','cforms'); ?>', name : 'sub_date', width : 140, sortable : true, align: 'center'},
		//{display: '<?php _e('IP','cforms'); ?>', name : 'ip', width : 100, sortable : true, align: 'center'}
		],
	buttons : [
		{name: '<?php _e('Détails','cforms'); ?>', bclass: 'add', onpress : cf_tracking_view},
		{name: '<?php _e('Supprimer','cforms'); ?>', bclass: 'delete', onpress : function (){jQuery('#cf_delete_dialog').jqmShow();} },
		{name: '<?php _e('Télécharger','cforms'); ?>', bclass: 'dl', onpress : function (){jQuery('#cf_dl_dialog').jqmShow();}},
		{separator: true}
		],
	searchitems : [
		//{display: '<?php _e('# Number(s)','cforms'); ?>', name : 'id'},
		//{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id'},
		{display: '<?php _e('Nom','cforms'); ?>', name : 'last_name', isdefault: true},
		{display: '<?php _e('Prénom','cforms'); ?>', name : 'first_name'},
		{display: '<?php _e('Adresse e-mail','cforms'); ?>', name : 'email'},
		{display: '<?php _e('Date','cforms'); ?>', name : 'sub_date'},
		{display: '<?php _e('Statut','cforms'); ?>', name : 'state'},
		{display: '<?php _e('Note(s)','cforms'); ?>', name : 'note'}
		//{display: '<?php _e('IP','cforms'); ?>', name : 'ip'}
		],<?php echo $dashboard; ?>
	sortname: "id",
	sortorder: "desc",
	singleSelect: true,
	usepager: true,
	onDragCol: true,
	title: '<?php _e('Liste des inscriptions','cforms'); ?>',
	errormsg: '<?php _e('Erreur de connexion','cforms'); ?>',
	pagestat: '<?php _e('Affichage de {from} à {to} sur {total} entrées','cforms'); ?>',
	procmsg: '<?php _e('Chargement ...','cforms'); ?>',
	nomsg: '<?php _e('Aucune entrée','cforms'); ?>',
	outof: '<?php _e('de','cforms'); ?>',
	findtext: '<?php _e('Rechercher','cforms'); ?>',
	/* pageof: '<?php _e('Page {%1} of','cforms'); ?>', */
	useRp: true,
	blockOpacity: 0.9,
	rp: 30,
	rpOptions: [10,30,50,100,200],
	showTableToggleBtn: true,
	width: 1000,
	height: 300 });
</script>
<?php

### if called from dashboard
if ( $_GET['d-id'] ){
	$_POST['showids'] = $_GET['d-id'].',';
	include_once( 'js/include/lib_database_getentries.php' );
}

cforms_footer();
?>
</div> <!-- wrap -->

<?php
add_action('admin_footer', 'insert_cfmodal_tracking');
function insert_cfmodal_tracking(){
	global $cforms_root,$noDISP;

	### Temp storage for download data
	$tempfile = dirname(__FILE__)."/js/include/data.tmp";
?>
	<div class="jqmWindow" id="cf_delete_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Confirmation','cforms'); ?></div>
		<div class="cf_ed_main">
			<form action="" name="deleteform" method="post">
				<div id="cf_target_del"><?php _e('Etes-vous sûr de vouloir supprimer cette entrée ?','cforms'); ?></div>
				<div class="controls"><a href="#" id="okDelete" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
	<div class="jqmWindow" id="cf_dl_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Télécharger','cforms'); ?></div>
		<div class="cf_ed_main">
			<form action="" name="downloadform" method="post" id="downloadform">
				<?php if( is_writable($tempfile) ) : ?>
				<div id="cf_target_dl">
                    <select id="pickDLformat" name="format">
                        <option value="xml">&nbsp;&nbsp;&nbsp;XML&nbsp;&nbsp;&nbsp;</option>
                        <option value="csv">&nbsp;&nbsp;&nbsp;CSV&nbsp;&nbsp;&nbsp;</option>
                        <option value="tab">&nbsp;&nbsp;&nbsp;TAB&nbsp;&nbsp;&nbsp;</option>
                    </select><label for="pickDLformat"><?php echo sprintf(__('Choissisez un format ! (CSV pour Excel)','cforms')); ?></label>
                    <br />
                    <input type="radio" class="chkBoxW" id="enc-utf8" name="enc" value="utf-8"/><label for="enc-utf8"><?php echo sprintf(__('UTF-8','cforms')); ?></label>
                    <input type="radio" class="chkBoxW" id="enc-iso" name="enc" value="iso" checked="checked"/><label for="enc-iso"><?php echo sprintf(__('ISO-8859-1','cforms')); ?></label>
                    <br />
                    <input type="checkbox" class="chkBoxW" id="header" name="header" value="true"/><label for="header"><?php echo sprintf(__('Inclure l&rsquo;en-tête','cforms')); ?></label><br />
                    <input type="checkbox" class="chkBoxW" id="addip" name="addip" value="true"/><label for="addip"><?php echo sprintf(__('Inclure l&rsquo;adresse IP','cforms')); ?></label><br />
                    <input type="checkbox" class="chkBoxW" id="addurl" name="addurl" value="true"/><label for="addurl"><?php echo sprintf(__('Ajouter l&rsquo;URL pour les fichiers uploadés','cforms')); ?></label>
				</div>
                <?php else :
                    echo '<p><strong>'.sprintf( __('File (data.tmp) in %s not writable! %sPlease adjust its file permissions/ownership!','cforms'),'<br />&nbsp;&nbsp;&nbsp;<code>'.dirname(__FILE__).'/js/include</code><br />','<br />').'</strong></p>';
                    echo '<p><strong>'.sprintf( __('...and reload this page afterwards.','cforms')).'</strong></p>';
                    endif; ?>
				<div class="controls"><?php if( is_writable($tempfile) ) : ?><a href="#" id="okDL" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><?php endif; ?><a href="#" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
<?php
}
?>