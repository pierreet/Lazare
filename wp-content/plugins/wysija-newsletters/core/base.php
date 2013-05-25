<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'constants.php');
defined('WYSIJA') or die('Restricted access');
global $wysija_msg;
global $wysija_wpmsg;
if(!$wysija_msg) $wysija_msg=array();
$wysija_wpmsg=array();
class WYSIJA_object{

    function WYSIJA_object(){

    }


    function get_version() {
        static $version=false;
        if($version) return $version;
        if ( ! function_exists( 'get_plugins' ) )   {
            if(file_exists(ABSPATH . 'wp-admin'.DS.'includes'.DS.'plugin.php')){
                require_once( ABSPATH . 'wp-admin'.DS.'includes'.DS.'plugin.php' );
            }
        }
        if (function_exists( 'get_plugins' ) )  {
            $plugin_data = get_plugin_data( WYSIJA_FILE );
            $version = $plugin_data['Version'];
        }else{
            $version='undefined';
        }
        return $version;
    }

    function wp_get_userdata($field=false){
        /*WordPress globals be careful there*/
        global $current_user;
        if($field){
            if(isset($current_user->$field))
                return $current_user->$field;
            elseif(isset($current_user->data->$field))
               return $current_user->data->$field;
            else return $current_user;
        }
        return $current_user;
    }

    function wp_notice($msg){
        global $wysija_wpmsg;

        /* add the hook only once */
        if(!$wysija_wpmsg) add_action('admin_notices', array($this,'wp_msgs'));

        /* record msgs */
        $wysija_wpmsg['updated'][]=$msg;
    }

    function wp_error($msg){
        global $wysija_wpmsg;

        /* add the hook only once */
        if(!$wysija_wpmsg) add_action('admin_notices', array($this,'wp_msgs'));

        /* record msgs */
        $wysija_wpmsg['error'][]=$msg;
    }

    function wp_msgs() {
        global $wysija_wpmsg;
        foreach($wysija_wpmsg as $keymsg => $wp2){
            $msgs= "<div class='".$keymsg." fade'>";
            foreach($wp2 as $mymsg)
                $msgs.= "<p><strong>Wysija</strong> : ".$mymsg."</p>";
            $msgs.= "</div>";
        }

        echo $msgs;
    }

    function error($msg,$public=false,$global=false){
        $status="error";
        if($global) $status="g-".$status;
        $this->setInfo($status,$msg,$public);
    }

    function notice($msg,$public=true,$global=false){
        $status="updated";
        if($global) $status="g-".$status;
        $this->setInfo($status,$msg,$public);
    }

    function setInfo($status,$msg,$public=false){
        global $wysija_msg;
        if(!$public) {

            if(!isset($wysija_msg['private'][$status])){
                $wysija_msg['private']=array();
                $wysija_msg['private'][$status]=array();
            }
            array_push($wysija_msg['private'][$status], $msg);
        }else{
            if(!isset($wysija_msg[$status]))  $wysija_msg[$status]=array();
            array_push($wysija_msg[$status], $msg);
        }

    }

    function getMsgs(){
        global $wysija_msg;

        if(isset($wysija_msg["private"]["error"])){
            $wysija_msg["error"][]=str_replace(array("[link]","[/link]"),array('<a class="showerrors" href="javascript:;">',"</a>"),__("An error occured. [link]Show more details.[/link]",WYSIJA));
        }

        if(isset($wysija_msg["private"]["updated"])){
            $wysija_msg["updated"][]=str_replace(array("[link]","[/link]"),array('<a class="shownotices" href="javascript:;">',"</a>"),__("[link]Show more details.[/link]",WYSIJA));
        }
        if(isset($wysija_msg["private"])){
            $prv=$wysija_msg["private"];
            unset($wysija_msg["private"]);
            if(isset($prv['error']))    $wysija_msg["xdetailed-errors"]=$prv['error'];
            if(isset($prv['updated']))    $wysija_msg["xdetailed-updated"]=$prv['updated'];
        }
        return $wysija_msg;
    }
}


class WYSIJA_help extends WYSIJA_object{
    var $controller=null;
    function WYSIJA_help(){

        if(!defined('DOING_AJAX')){
            add_action('init', array($this, 'register_scripts'), 1);

        }

        add_action('widgets_init', array($this, 'widgets_init'), 1);
    }

    function widgets_init() {
        register_widget('WYSIJA_NL_Widget');
    }

    function register_scripts(){
        if(defined('WPLANG') && WPLANG!=''){
            $locale=explode("_",WPLANG);
            $wplang=$locale[0];
        }else{
            $wplang='en';
        }

        if(file_exists(WYSIJA_DIR."js".DS."validate".DS."languages".DS."jquery.validationEngine-".$wplang.".js")){
            wp_register_script('wysija-validator-lang',WYSIJA_URL."js/validate/languages/jquery.validationEngine-".$wplang.".js", array( 'jquery' ),WYSIJA::get_version(),true );
        }else{
            wp_register_script('wysija-validator-lang',WYSIJA_URL."js/validate/languages/jquery.validationEngine-en.js", array( 'jquery' ),WYSIJA::get_version(),true );
        }
        wp_register_script('wysija-validator',WYSIJA_URL."js/validate/jquery.validationEngine.js", array( 'jquery' ),WYSIJA::get_version(),true );
        wp_register_script('wysija-front-subscribers', WYSIJA_URL."js/front-subscribers.js", array( 'jquery' ),WYSIJA::get_version(),true);


        wp_register_script('wysija-form', WYSIJA_URL."js/forms.js", array( 'jquery' ),WYSIJA::get_version());
        wp_register_style('validate-engine-css',WYSIJA_URL."css/validationEngine.jquery.css",array(),WYSIJA::get_version());
        wp_register_script('wysija-admin-ajax', WYSIJA_URL."js/admin-ajax.js",array(),WYSIJA::get_version());
        wp_register_script('wysija-admin-ajax-proto', WYSIJA_URL."js/admin-ajax-proto.js",array(),WYSIJA::get_version());

        if(defined('WYSIJA_SIDE') && WYSIJA_SIDE=='front')  wp_enqueue_style('validate-engine-css');

    }


    /**
     * when doing an ajax request in admin this is the first place where we come
     */
    function ajax() {

        $resultArray=array();
        if(!$_REQUEST || !isset($_REQUEST['controller']) || !isset($_REQUEST['task'])){
            $resultArray=array("result"=>false);
        }else{
            $wysijapp="wysija-newsletters";
            if(isset($_REQUEST['wysijaplugin'])) $wysijapp=$_REQUEST['wysijaplugin'];

            $this->controller=&WYSIJA::get($_REQUEST['controller'],"controller", false, $wysijapp);

            if(method_exists($this->controller, $_REQUEST['task'])){
                $resultArray["result"]=$this->controller->$_REQUEST['task']();
            }else{
                $this->error("Method '".$_REQUEST['task']."' doesn't exist for controller:'".$_REQUEST['controller']."'.");
            }
            /*if(!check_ajax_referer('wysija_ajax','_wpnonce')){
                die("security error");
            }else{

            }*/


        }
        //dbg($resultArray);
        //if(isset($resultArray['']))
        $resultArray["msgs"]=$this->getMsgs();

        header('Content-type: application/json');
        $response=json_encode($resultArray);

        //in some case scenario our client will have jquery forcing the jsonp so we need to adapt ourselves
        if(isset($_REQUEST['callback'] ))   $response=$_REQUEST['callback'].'('.$response.')';
        echo $response;
        die();
    }

}


class WYSIJA extends WYSIJA_object{

    function WYSIJA(){

    }
    function get_permalink($pageid,$params=array(),$simple=false){

        $post = &get_post($pageid);

        $url=get_permalink($post);
        if(!$url){
            //we need to recreate the subscription page
            $values=array();
            $helperInstall=&WYSIJA::get("install",'helper');
            $helperInstall->createPage($values);

            $modelConf=&WYSIJA::get("config","model");
            $modelConf->save($values);
            $post = &get_post($values['confirm_email_link']);
            $url=get_permalink($post);
            if(!$url) $this->error('Error with the wysijap subscription confirmation page.');
        }

        $paramsquery=parse_url($url);
        if($params!==false) $params[$post->post_type]=$post->post_name;
        //make a simple url to the home
        if($simple){

            $url=site_url();
            if(array_pop(str_split($url))!='/') $url.='/';
        }

        if(isset($paramsquery['query'])){
            $myparams=explode('&',$paramsquery['query']);
            //get the param from the url obtain in permalink and transfer it to our url
            foreach($myparams as $paramvalu){
                $splitkeyval=explode('=',$paramvalu);
                $params[$splitkeyval[0]]=$splitkeyval[1];
            }
        }


        //$params['wysijap']=str_replace(basename($url)."?wysijap=","",$url);
        if($params){
            if (strpos($url, '?') !== false) $charStart='&';
            else $charStart='?';
            $url.=$charStart;
            $paramsinline=array();
            foreach($params as $k => $v){
                if(is_array($v))    $v = http_build_query(array($k => $v));
                $paramsinline[]=$k."=".$v;
            }
            $url.=implode('&',$paramsinline);
        }

        return $url;

    }

    function load_lang($extendedplugin=false){
        static $extensionloaded = false;

        if(!$extendedplugin) return $extensionloaded;

        if(!$extensionloaded){

            add_action('init', array("WYSIJA","load_lang_init"));
        }
        /*load the language file*/
        if ( !$extensionloaded || !isset($extensionloaded[$extendedplugin])) {

            switch($extendedplugin){
                case "wysija-newsletters":
                    $transstring=WYSIJA;
                    break;
                case "wysijashop":
                    $transstring=WYSIJASHOP;
                    break;
                case "wysijacrons":
                    $transstring=WYSIJACRONS;
                    break;
                case "get_all":
                    return $extensionloaded;
            }
            /*
             * Who commented that line ? I don't remember doing it.(Ben)
             * Leave it be please
             */
            if(!isset($extensionloaded[$extendedplugin]))    load_plugin_textdomain( $transstring, false, $extendedplugin . DS.'languages' );
            $extensionloaded[$extendedplugin] = $transstring;
            $config=&WYSIJA::get('config','model');

            //TODO I don't remember why do we load_lang_init twice I think it has to do with qTranslate compatibility ....
            if(!((int)$config->getValue('debug_new')>1))    WYSIJA::load_lang_init();
        }

    }

    /**
     * this function exists just to fix the issue with qtranslate :/ (it only fix it partially)
     * @param type $extendedplugin
     */
    function load_lang_init($extendedplugin=false){
        $loadtranslation=true;
        //to allow wysija team mebers to work in english mode if debug is activated
        $config=&WYSIJA::get('config','model');

        if((int)$config->getValue('debug_new')>1){
            global $current_user;

            if($current_user && strpos($current_user->data->user_email, '@wysija.com')!==false || strpos($current_user->data->user_email, 'bencaubere.com')!==false){
                $loadtranslation=false;
            }

        }
        if(!$loadtranslation)    return;

        $extensionloaded=WYSIJA::load_lang('get_all');

        foreach($extensionloaded as $extendedplugin => $transstring){

            load_plugin_textdomain( $transstring, false, $extendedplugin . DS.'languages' );
        }
    }

    /**
     * function to generate objects of different types, managing file requiring in order to be the most efficient
     * @staticvar array $arrayOfObjects
     * @param type $name
     * @param type $type
     * @return type
     */
    public static function get($name,$type,$forceside=false,$extendedplugin='wysija-newsletters'){
        static $arrayOfObjects;

        WYSIJA::load_lang($extendedplugin);

        /*store all the objects made so that we can reuse them accross the application*/
        if(isset($arrayOfObjects[$extendedplugin][$type.$name])) {
            return $arrayOfObjects[$extendedplugin][$type.$name];
        }
        if($forceside)  $side=$forceside;
        else    $side=WYSIJA_SIDE;

        if($extendedplugin=='wysija-newsletters'){
            $extendeconstant=strtoupper("wysija");
            if(!defined($extendeconstant)) define($extendeconstant,$extendeconstant);
            $extendedpluginname='wysija';
        }else{
           $extendeconstant=strtoupper($extendedplugin);
            if(!defined($extendeconstant)) define($extendeconstant,$extendeconstant);
            $extendedpluginname=$extendedplugin;
        }

        //security to protect against ./../ includes
        $name = preg_replace('#[^a-z0-9_]#i','',$name);
        switch($type){
            case 'controller':
                $ctrdir=WYSIJA_PLG_DIR.$extendedplugin.DS.'controllers'.DS;
                /*require the parent class necessary*/
                require_once(WYSIJA_CORE.'controller.php');/*require the common controller file*/
                if(defined('DOING_AJAX')) {
                    $classpath=$ctrdir.'ajax'.DS.$name.'.php';
                }else {
                    $classpath=$ctrdir.$side.DS.$name.'.php';
                    require_once(WYSIJA_CTRL.$side.'.php');/*require the side specific controller file*/
                }
                $classname = strtoupper($extendedpluginname).'_control_'.$side.'_'.$name;
                break;
            case 'view':
                $viewdir=WYSIJA_PLG_DIR.$extendedplugin.DS.'views'.DS;
                $classpath=$viewdir.$side.DS.$name.".php";
                $classname = strtoupper($extendedpluginname).'_view_'.$side.'_'.$name;
                require_once(WYSIJA_CORE.'view.php');/*require the common view file*/
                require_once(WYSIJA_VIEWS.$side.'.php');/*require the side specific view file*/
                break;
            case 'helper':
                $helpdir=WYSIJA_PLG_DIR.$extendedplugin.DS.'helpers'.DS;

                $classpath=$helpdir.$name.'.php';
                $classname = strtoupper($extendedpluginname).'_help_'.$name;

                break;
            case 'model':
                $modeldir=WYSIJA_PLG_DIR.$extendedplugin.DS.'models'.DS;
                $classpath=$modeldir.$name.'.php';
                $classname = strtoupper($extendedpluginname).'_model_'.$name;
                /*require the parent class necessary*/
                require_once(WYSIJA_CORE.'model.php');
                break;
            default:
                WYSIJA::setInfo('error','WYSIJA::get does not accept this type of file "'.$type.'" .');
                return false;
        }

        if(!file_exists($classpath)) {
            WYSIJA::setInfo('error','file has not been recognised '.$classpath);
            return;
        }

        require_once($classpath);
        return $arrayOfObjects[$extendedplugin][$type.$name]=new $classname($extendedpluginname);

    }

    function log($key='default',$data='empty'){
        $config=&WYSIJA::get('config','model');
        if((int)$config->getValue('debug_new')>1){
            $optionlog=get_option('wysija_log');
            if ( false === $optionlog ){
                add_option( 'wysija_log', array() ,'','no');
                $optionlog=array();
            }

            $optionlog[time().$key]=$data;
            update_option('wysija_log', $optionlog);
        }
        return false;
    }

    /**
     * the filter to add option to the cron frequency instead of being stuck with hourly, daily and twicedaily...
     * @param type $param
     * @return type
     */
    function filter_cron_schedules( $param ) {
        $frequencies=array(
            'one_min' => array(
                'interval' => 60,
                'display' => __( 'Once every minutes',WYSIJA)
                ),
            'two_min' => array(
                'interval' => 120,
                'display' => __( 'Once every two minutes',WYSIJA)
                ),
            'five_min' => array(
                'interval' => 300,
                'display' => __( 'Once every five minutes',WYSIJA)
                ),
            'ten_min' => array(
                'interval' => 600,
                'display' => __( 'Once every ten minutes',WYSIJA)
                ),
            'fifteen_min' => array(
                'interval' => 900,
                'display' => __( 'Once every fifteen minutes',WYSIJA)
                ),
            'thirty_min' => array(
                'interval' => 1800,
                'display' => __( 'Once every thirty minutes',WYSIJA)
                ),
            'two_hours' => array(
                'interval' => 7200,
                'display' => __( 'Once every two hours',WYSIJA)
                ),
            'eachweek' => array(
                'interval' => 2419200,
                'display' => __( 'Once a week',WYSIJA)
                ),
            'each28days' => array(
                'interval' => 604800,
                'display' => __( 'Once every 28 days',WYSIJA)
                ),
            );

        return array_merge($param, $frequencies);
    }

    /**
     * cron where the frequency is decided by the administrator
     */
    function croned_queue() {
        /* create the automatic post notifications email if there is any*/
        $autoNL=&WYSIJA::get('autonews','helper');
        $autoNL->checkPostNotif();

        /* queue the scheduled newsletter also if there are any*/
        $autoNL->checkScheduled();

        $config=&WYSIJA::get("config","model");
        $premium=$config->getValue('premium_key');
        $subscribers=(int)$config->getValue('total_subscribers');

        if($subscribers<2000 || ($premium && $subscribers>=2000) ){

            $helperQ=&WYSIJA::get("queue","helper");
            $helperQ->report=false;
            WYSIJA::log('croned_queue process',true);
            $helperQ->process();

        }
    }

    /**
     * cron where the frequency is decided by the administrator
     */
    function croned_bounce() {
        /*bounce handling*/
        $config = &WYSIJA::get('config','model');
        if(!$config->getValue("bounce_process_auto")) return false;

        $bounceClass = &WYSIJA::get('bounce','helper');
        $bounceClass->report = false;
        if(!$bounceClass->init()){
                $res['result']=false;
                return $res;
        }
        if(!$bounceClass->connect()){
                $bounceClass->error($bounceClass->getErrors());
                $res['result']=false;
                return $res;
        }
        $bounceClass->notice(sprintf('Successfully connected to %1$s',$config->getValue('bounce_login')));
        $nbMessages = $bounceClass->getNBMessages();


        if(empty($nbMessages)){
            $bounceClass->error('There are no messages',true);
            $res['result']=false;
            return $res;
        }else{
            $bounceClass->notice(sprintf('There are %1$s messages in your mailbox',$nbMessages));
        }


        $bounceClass->handleMessages();
        $bounceClass->close();
    }


    /**
     * remove temporary files
     */
    function croned_daily() {
        @ini_set('max_execution_time',0);
        /*user refresh count total*/
        $helperU=&WYSIJA::get('user','helper');
        $helperU->refreshUsers();

        /*clear temporary folders*/
        $helperF=&WYSIJA::get('file','helper');
        $helperF->clear();

        /*clear queue from unsubscribed*/
        $helperQ=&WYSIJA::get('queue','helper');
        $helperQ->clear();

        /* send daily report about emails sent */
        $modelC=&WYSIJA::get('config','model');
        if($modelC->getValue('emails_notified_when_dailysummary')){
            $helperS=&WYSIJA::get('stats','helper');
            $helperS->sendDailyReport();
        }
    }

    function croned_weekly() {
        @ini_set('max_execution_time',0);

        /* send daily report about emails sent */
        $modelC=&WYSIJA::get('config','model');
        /* if premium let's do a licence check */
        if($modelC->getValue('premium_key')){
            $helperS=&WYSIJA::get('licence','helper');
            $helperS->check();
        }

    }


    function croned_monthly() {
        @ini_set('max_execution_time',0);

        /* send daily report about emails sent */
        $modelC=&WYSIJA::get('config','model');
        if($modelC->getValue('sharedata')){
            $helperS=&WYSIJA::get('stats','helper');
            $helperS->share();
        }

    }

    function deactivate() {
        wp_clear_scheduled_hook('wysija_cron_queue');
        wp_clear_scheduled_hook('wysija_cron_bounce');
        wp_clear_scheduled_hook('wysija_cron_daily');
        wp_clear_scheduled_hook('wysija_cron_weekly');
        wp_clear_scheduled_hook('wysija_cron_monthly');
    }



    function redirect($redirectTo){
         /* save the messages */
        global $wysija_msg,$wysija_queries;
        WYSIJA::update_option('wysija_msg',$wysija_msg);
        WYSIJA::update_option('wysija_queries',$wysija_queries);
        wp_redirect($redirectTo);
        exit;
    }

    function create_post_type() {

        //$modelC=&WYSIJA::get('config','model');
        //$rewritewysijap=array("slug"=>"wysijap");

        //by default there is url rewriteing on wysijap custom post, though in one client case I had to deactivate it.
        //as this is rare we just need to set this setting to activate it
        //if($modelC->getValue('no_rewrite_wysijap'))$rewritewysijap=false;

        //by default let's deactivate the url rewriting of the wysijap confirmation page because it is breaking in some case.
        $rewritewysijap=false;

        register_post_type( 'wysijap',
            array(
                    'labels' => array(
                            'name' => __( 'Wysija page' ),
                            'singular_name' => __( 'Wysija page' )
                    ),
            'public' => true,
            'has_archive' => false,
            'show_ui' =>false,
            'show_in_menu' =>false,
            'rewrite' => $rewritewysijap,
            'show_in_nav_menus'=>false,
            'can_export'=>false,
            'publicly_queryable'=>true,
            'exclude_from_search'=>true,
            )
        );

        if(!get_option('wysija_post_type_updated')) {
            $modelPosts=new WYSIJA_model();
            $modelPosts->tableWP=true;
            $modelPosts->table_prefix='';
            $modelPosts->table_name='posts';
            $modelPosts->noCheck=true;
            $modelPosts->pk='ID';
            if($modelPosts->exists(array('post_type'=>'wysijapage'))){
                $modelPosts->update(array('post_type'=>'wysijap'),array('post_type'=>'wysijapage'));
                flush_rewrite_rules( false );
            }
            WYSIJA::update_option('wysija_post_type_updated',time());
        }


        if(!get_option('wysija_post_type_created')) {
            flush_rewrite_rules( false );
            WYSIJA::update_option('wysija_post_type_created',time());
        }

        /*
        $rewritewysija=array('slug'=>'wysija');
        register_post_type( 'wysija',
            array(
                    'labels' => array(
                            'name' => 'Wysija Archives',
                            'singular_name' => 'Wysija archives'
                    ),
            'public' => true,
            'has_archive' => false,
            'show_ui' =>false,
            'show_in_menu' =>false,
            'rewrite' => $rewritewysija,
            'show_in_nav_menus'=>false,
            'can_export'=>false,
            'publicly_queryable'=>true,
            'exclude_from_search'=>true,
            )
        );

        if(!get_option('wysija_post_type2_created')) {
            flush_rewrite_rules( false );
            WYSIJA::update_option('wysija_post_type2_created',time());
        }*/
    }


    function update_option($option_name,$newvalue){
        if ( get_option( $option_name ) != $newvalue ) {
            update_option( $option_name, $newvalue );
        } else {
            add_option( $option_name, $newvalue, '', 'no' );
        }
    }

    function hook_add_WP_subscriber($user_id) {
        $data=get_userdata($user_id);

        //check first if a subscribers exists if it doesn't then let's insert it
        $modelUser=&WYSIJA::get('user','model');
        $subscriber_exists=$modelUser->getOne(array('user_id'),array('email'=>$data->user_email));
        $modelUser->reset();
        if($subscriber_exists){
            $uid=$subscriber_exists['user_id'];
        }else{
            $modelUser->noCheck=true;

            $firstname=$data->first_name;
            $lastname=$data->last_name;
            if(!$data->first_name && !$data->last_name) $firstname=$data->display_name;

            $uid=$modelUser->insert(array('email'=>$data->user_email,'wpuser_id'=>$data->ID,'firstname'=>$firstname,'lastname'=>$lastname,'status'=>1));
        }

        $modelConf=&WYSIJA::get('config','model');
        $modelUL=&WYSIJA::get('user_list','model');
        $modelUL->insert(array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));

        $helperUser=&WYSIJA::get('user','helper');
        $helperUser->sendAutoNl($uid,$data,'new-user');
        return true;
    }

    function hook_edit_WP_subscriber($user_id) {
        $data=get_userdata($user_id);

        //check first if a subscribers exists if it doesn't then let's insert it
        $modelUser=&WYSIJA::get('user','model');
        $modelConf=&WYSIJA::get('config','model');
        $modelUL=&WYSIJA::get('user_list','model');

        $subscriber_exists=$modelUser->getOne(array('user_id'),array('email'=>$data->user_email));

        $modelUser->reset();

        $firstname=$data->first_name;
        $lastname=$data->last_name;
        if(!$data->first_name && !$data->last_name) $firstname=$data->display_name;

        if($subscriber_exists){
            $uid=$subscriber_exists['user_id'];

            $modelUser->update(array('email'=>$data->user_email,'firstname'=>$firstname,'lastname'=>$lastname),array('wpuser_id'=>$data->ID));

            $result=$modelUL->getOne(false,array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));
            $modelUL->reset();
            if(!$result)
                $modelUL->insert(array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));
        }else{
            /*chck that we didnt update the email*/
            $subscriber_exists=$modelUser->getOne(false,array('wpuser_id'=>$data->ID));

            if($subscriber_exists){
                $uid=$subscriber_exists['user_id'];

                $modelUser->update(array('email'=>$data->user_email,'firstname'=>$firstname,'lastname'=>$lastname),array('wpuser_id'=>$data->ID));

                $result=$modelUL->getOne(false,array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));
                $modelUL->reset();
                if(!$result)
                    $modelUL->insert(array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));
            }else{
                $modelUser->noCheck=true;
                $uid=$modelUser->insert(array('email'=>$data->user_email,'wpuser_id'=>$data->ID,'firstname'=>$firstname,'lastname'=>$lastname,'status'=>1));

                $modelUL->insert(array('user_id'=>$uid,'list_id'=>$modelConf->getValue('importwp_list_id')));
            }


        }

        return true;
    }


    function hook_del_WP_subscriber($user_id) {

        $modelConf=&WYSIJA::get("config","model");
        $modelUser=&WYSIJA::get("user","model");
        $data=$modelUser->getOne(array("email",'user_id'),array("wpuser_id"=>$user_id));
        $modelUser->delete(array("email"=>$data['email']));
        $modelUser=&WYSIJA::get("user_list","model");
        $modelUser->delete(array("user_id"=>$data['user_id'],"list_id"=>$modelConf->getValue("importwp_list_id")));

        //WYSIJA::wp_notice(__("User has been removed from the <b>Synched</b> Wordpress user list.",WYSIJA));
    }

    function hook_postNotification_transition($new_status, $old_status, $post) {

        WYSIJA::log('pn_transition_post',array('postID'=>$post->ID,'postID'=>$post->post_title,'old_status'=>$old_status,'new_status'=>$new_status));
        if($post->post_type=='post' && $new_status=='publish' && $old_status!=$new_status){
            $modelEmail =& WYSIJA::get('email', 'model');
            $emails = $modelEmail->get(false, array('type' => 2, 'status' => array(1, 3, 99)));

            foreach($emails as $key => $email) {
                if($email['params']['autonl']['event'] === 'new-articles' && $email['params']['autonl']['when-article'] === 'immediate') {
                    $modelEmail->reset();
                    $modelEmail->giveBirth($email, $post->ID);
                }
            }
        }


        return true;
    }

    /*function hook_postNotification_now($id) {
        //WYSIJA::log('pn_now',$id);
        return WYSIJA::hook_postNotification($id, 'now');
    }

    function hook_postNotification_future($id) {
        //WYSIJA::log('pn_future',$id);
        return WYSIJA::hook_postNotification($id, 'future');
    }

    function hook_postNotification($id, $when) {
        // don't resend updated post
        $post = get_post($id);
        //WYSIJA::log('pn_data',$post);
        if($when === 'now' && $post->post_modified !== $post->post_date) {
            //WYSIJA::log('pn_condition_out','we\'re out $post->post_modified !== $post->post_date');
            return;
        }

        $modelEmail =& WYSIJA::get('email', 'model');
        $emails = $modelEmail->get(false, array('type' => 2, 'status' => array(1, 3, 99)));

        foreach($emails as $key => $email) {
            if($email['params']['autonl']['event'] === 'new-articles' && $email['params']['autonl']['when-article'] === 'immediate') {
                $modelEmail->reset();
                $modelEmail->giveBirth($email, $id);
            }
        }

        return $id;
    }*/

    function hook_subscriber_to_list( $details ) {

        $config=&WYSIJA::get('config','model');
        $modelUser=&WYSIJA::get('user','model');
        $userdata=$modelUser->getOne(false,array('user_id'=>$details['user_id']));
        $confirmed=true;

        /* do not send email if user is not confirmed*/
        if($config->getValue('confirm_dbleoptin') && (int)$userdata['status']!=1)   $confirmed=false;

        if($confirmed){
            /*check for auto nl and send if needed*/
            $helperU=&WYSIJA::get('user','helper');
            $helperU->sendAutoNl($details['user_id'],array(0=>$details));
        }

        return true;
    }


    function uninstall(){
        $helperUS=&WYSIJA::get("uninstall","helper");
        $helperUS->uninstall();
    }

    function activate(){
        $encoded_option=get_option("wysija");
        $installApp=false;
        if($encoded_option){
            $values=unserialize(base64_decode($encoded_option));
            if(isset($values['installed'])) $installApp=true;
        }

        /*test again for plugins on reactivation*/
        if($installApp){
            $importHelp=&WYSIJA::get("import","helper");
            $importHelp->testPlugins();

            /*resynch wordpress list*/
            $helperU=&WYSIJA::get("user","helper");
            $helperU->synchList($values['importwp_list_id']);

        }


    }

    function is_plugin_active($pluginName){
        $arrayactiveplugins=get_option('active_plugins');
        if(in_array($pluginName, $arrayactiveplugins)/*is_plugin_active($conflictPlug['file'])*/) {
            //plugin is activated
            return true;
        }
        return false;
    }

    function is_caching_active(){
        $checkPlugins=array(
            'wp-super-cache/wp-cache.php' ,
            'w3-total-cache/w3-total-cache.php',
            'quick-cache/quick-cache.php',
            'hyper-cache/plugin.php'
            );


        foreach($checkPlugins as $pluginFileName){
            if(WYSIJA::is_plugin_active($pluginFileName)){
                switch($pluginFileName){
                    case 'wp-super-cache/wp-cache.php':
                        global $cache_enabled, $super_cache_enabled;
                        if(!(WP_CACHE && $cache_enabled && $super_cache_enabled))   continue(2);
                        break;
                    case 'w3-total-cache/w3-total-cache.php':
                        $config = & w3_instance("W3_Config");
                        if(!(WP_CACHE && $config->get_boolean("pgcache.enabled")))   continue(2);

                        break;
                    case 'quick-cache/quick-cache.php':
                        if(!(WP_CACHE && $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"]))   continue(2);
                        break;
                    case 'hyper-cache/plugin.php':
                        if(!(WP_CACHE))   continue(2);
                        break;
                    default:
                        continue(2);
                }
                return true;
            }
        }

    }

    /*make sure that the current user has the good access rights corresponding to its role*/
    function update_user_caps(){
        global $current_user;

        if(empty($current_user)) get_currentuserinfo();
        if(empty($current_user)) return false;
        $current_user->get_role_caps();


        //make sure the current user has all of the capability he wants
        //$current_user

        //if(!current_user_can($capability)) return false;
        return true;
    }

    function current_user_can($capability){
        if(!$capability) return false;
        WYSIJA::update_user_caps();
        if(!current_user_can($capability)) return false;
        return true;
    }




}

/**
 * widget class for user registration
 */
class WYSIJA_NL_Widget extends WP_Widget {
    var $classid="";
    var $iFrame=false;


    function WYSIJA_NL_Widget($coreOnly=false) {
        static $scriptregistered;
        if(WYSIJA_SIDE=="front"){

            if(!$scriptregistered){
                if(!isset($_REQUEST['controller']) || (isset($_REQUEST['controller']) && $_REQUEST['controller']=="confirm" && isset($_REQUEST["wysija-key"]))){
                    $controller="subscribers";
                }else $controller=$_REQUEST['controller'];
                $siteurl=get_site_url();

                /*try to find the domain part in the site url*/
                if(strpos($siteurl, $_SERVER['HTTP_HOST'])===false){
                    //if we don't find it then we need to create a new siteadminurl
                    //by replacing the part between http// and the first slash with the one from request uri

                    $siteurlarray=explode("/",
                            str_replace(array("http://"),"",$siteurl)
                            );

                    $ajaxurl=str_replace($siteurlarray[0], $_SERVER['HTTP_HOST'], $siteurl);
                    /* old solution
                     * $homeurl=get_home_url();
                    $siteurlarr=explode("/",str_replace("http://","",$homeurl));
                    $homeurlarr=explode("/",str_replace("http://","",$homeurl));

                    if($homeurlarr[0]==$siteurlarr[0]){
                        $ajaxurl=$siteurl;
                    }else $ajaxurl=$homeurl;*/


                }else{
                    $ajaxurl=$siteurl;
                }

                //let's check if the current ajaxurl is https if so we need to make sure that also the url we're calling from is https
                if(strpos($ajaxurl, 'https://')!==false){
                    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on'){
                        //ok
                    }else{
                        $ajaxurl=str_replace('https://','http://',$ajaxurl);
                    }

                }

                $lastchar=substr($ajaxurl, -1);

                if($lastchar!="/")$ajaxurl.="/";
                $ajaxurl.='wp-admin/admin-ajax.php';
                $this->paramsajax=array(
                    'action' => 'wysija_ajax',
                    'controller' => $controller,
                    'ajaxurl'=>$ajaxurl,
                    'loadingTrans'  =>__('Loading...',WYSIJA)
                );




                if(is_user_logged_in()) $this->paramsajax['wysilog']=1;

                $scriptregistered=true;
            }

        }

        if($coreOnly) $this->coreOnly=true;
        $namekey='wysija';
        $title=__("Wysija Subscription",WYSIJA);
        $params=array( 'description' => __('Subscription form for your newsletters.',WYSIJA));
        $sizeWindow=array('width' => 400);

        $this->add_translated_default();

        if(defined('WP_ADMIN')){
            add_action('admin_menu', array($this,'add_translated_default'),96);
        }

        //add_action('init', array($this,'recordWysijaAjax'));
        $this->recordWysijaAjax();

        $this->classid=strtolower(str_replace(__CLASS__."_","",get_class($this)));
        //parent::__construct( $namekey, $title, $params,$sizeWindow );
        $this->WP_Widget( $namekey, $title, $params,$sizeWindow );

    }

    function recordWysijaAjax(){
        if(isset($this->paramsajax)){
            //$this->paramsajax['ajaxurl'] = apply_filters('wysijaAjaxURL', $this->paramsajax['ajaxurl']);
            wp_localize_script( 'wysija-front-subscribers', 'wysijaAJAX',$this->paramsajax );

        }
    }

    function add_translated_default(){
        $this->name=__('Wysija Subscription',WYSIJA);
        $this->widget_options['description']=__('Subscription form for your newsletters.',WYSIJA);

        $config=&WYSIJA::get('config','model');
        $this->successmsgconf=__('Check your inbox now to confirm your subscription.',WYSIJA);
        $this->successmsgsub=__("You've successfully subscribed.",WYSIJA);
        if($config->getValue('confirm_dbleoptin')){
            $successmsg=$this->successmsgconf;
        }else{
            $successmsg=$this->successmsgsub;
        }
        $this->fields=array(
            'title' =>array('label'=>__('Title:',WYSIJA),'default'=>__('Subscribe to our Newsletter',WYSIJA))
            ,'instruction' =>array('label'=>'','default'=>__('To subscribe to our dandy newsletter simply add your email below. A confirmation email will be sent to you!',WYSIJA))
            ,'lists' =>array("core"=>1,'label'=>__('Select a list:',WYSIJA),'default'=>array(1))
            ,'autoregister' =>array('core'=>1,'label'=>__('Let subscribers select their lists:',WYSIJA),'default'=>'not_auto_register')
            ,'customfields' =>array('core'=>1,'label'=>__('Ask for:',WYSIJA),'default'=>array('email'=>array('label'=>__('Email',WYSIJA))))
            ,'labelswithin'=>array('core'=>1,'default'=>true,'label'=>__('Display labels in inputs',WYSIJA),'hidden'=>1)
            ,'submit' =>array('core'=>1,'label'=>__('Button label:',WYSIJA),'default'=>__('Subscribe!',WYSIJA))
            ,'success'=>array('core'=>1,'label'=>__('Success message:',WYSIJA),'default'=>$successmsg)
            ,'iframe'=>array('core'=>1,'label'=>__('Get iframe version',WYSIJA))
            /*,"php"=>array("core"=>1,"label"=>__('Get php version',WYSIJA))*/
        );
    }


    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        /* check if custom fields are set in the new instance, it if is not then we remove it from the old instance */
        if(isset($instance['customfields']) && !isset($new_instance['customfields'])) unset($instance['customfields']);
        //if(isset($instance['labelswithin']) && !isset($new_instance['labelswithin'])) unset($instance['labelswithin']);

        /* for each new instance we update the current instance */
        foreach($new_instance as $key => $value) $instance[$key]=$value;


        /*get the custom fields*/
        $modelCustomF=&WYSIJA::get('user_field','model');
        $customs=$modelCustomF->get(false,array('type'=>'0'));

        /*set an array of custom fields easy to read*/
        $custombyid=array();
        foreach($customs as $customf)   $custombyid[$customf['column_name']]=$customf;

        if(!isset($instance['customfields']) && isset($instance['labelswithin']) && $instance['labelswithin']=='labels_within'){
            $instance['customfields']=array('email'=>array('label'=>__('Email',WYSIJA)));
        }


        /* if there were custom fields set in the previous instance*/
        if(isset($instance['customfields']) && $instance['customfields']){
            foreach($instance['customfields'] as $keycf => &$custom){
                /* make sure we remove the label data if the field is not selected anymore */
                if(!isset($custom['column_name']) && $keycf!="email") unset($instance['customfields'][$keycf]);
                else{
                    /*if a custom field is select but has no default label then we just set the default label for that field*/
                    if(!isset($custom['label']) || !$custom['label']) $custom['label']=$custombyid[$custom['column_name']]['name'];
                }
            }

        }

        return $instance;
    }

    function form( $instance ) {
        $formObj=&WYSIJA::get('forms','helper');

        $html='';
        $modelList=&WYSIJA::get('list','model');
        $lists=$modelList->get(array('name','list_id'),array('is_enabled'=>1));
        if(!$lists){
            echo '<p>'.__('Before creating a subscription widget you\'ll need to create at least one list to add your subscribers to.',WYSIJA).' <a href="admin.php?page=wysija_subscribers&action=addlist">'.__('Create a list.',WYSIJA).'</a></p>';
            return;
        }

        foreach($this->fields as $field => $fieldParams){
            $extrascriptLabel='';
            $valuefield="";
            if((isset($fieldParams['hidden']) && $fieldParams['hidden']) || (isset($this->coreOnly) && !isset($fieldParams['core']))) continue;
            if(isset($instance[$field]))  {

                if($field=="success" && $instance[$field]==$this->successmsgsub." ".$this->successmsgconf){
                    $config=&WYSIJA::get("config","model");
                    if(!$config->getValue("confirm_dbleoptin")){
                        $valuefield=$this->successmsgsub;
                    }else{
                        $valuefield=$instance[$field];
                    }
                }else   $valuefield=$instance[$field];
            }
            elseif(isset($fieldParams['default'])) $valuefield=$fieldParams['default'];

            $classDivLabel=$fieldHTML='';
            $styleDivSeparators='clear:both; max-height: 116px; overflow: auto; float: left;margin: 0 10px 10px 0;';
            switch($field){
                case 'lists':


                    $classDivLabel='style="float:left"';
                    $fieldHTML= '<div style="'.$styleDivSeparators.'">';

                    if(!$valuefield) {
                        $modelConfig=&WYSIJA::get("config","model");
                        $valuefield[]=$modelConfig->getValue("default_list_id");
                    }
                    foreach($lists as $list){
                        if(in_array($list['list_id'], $valuefield)) $checked=true;
                        else $checked=false;
                        $fieldHTML.= '<p style="margin:0 0 5px 0; float:left; margin-left:5px;"><label for="'.
                                $this->get_field_id($field.$list['list_id']).'">'.$formObj->checkbox( array('id'=>$this->get_field_id($field.$list['list_id']),
                                    'name'=>$this->get_field_name($field)."[]"),
                                        $list['list_id'],$checked).$list['name'].'</label></p>';
                        $fieldHTML.='<input type="hidden" name="'.$this->get_field_name($field.'_name')."[".$list['list_id']."]".'" value="'.$list['name'].'" />';
                    }

                    $fieldHTML .= '</div>';

                    break;
                case 'autoregister':
                    $classDivLabel=$styleDivSeparators;
                    $value='auto_register';
                    $checked=false;
                    if((isset($instance['autoregister']) && $instance['autoregister']=='auto_register')) $checked=true;

                    $id=str_replace("_",'-',$key).'-'.$value;
                    $fieldHTML.='<label for="'.$id.'">';
                    $fieldHTML.=$formObj->radio(array('id'=>$id,'name'=>$this->get_field_name('autoregister')),$value,$checked);
                    $fieldHTML.=__('Yes',WYSIJA).'</label>';

                    $value='not_auto_register';
                    $checked=false;
                    if(!isset($instance['autoregister']) || $instance['autoregister']!='auto_register') $checked=true;
                    $id=str_replace("_",'-',$key).'-'.$value;
                    $fieldHTML.='<label for="'.$id.'">';
                    $fieldHTML.=$formObj->radio(array("id"=>$id,'name'=>$this->get_field_name("autoregister")),$value,$checked);
                    $fieldHTML.=__('No',WYSIJA).'</label>';
                    $fieldHTML .= '</p>';

                    break;
                case 'customfields':

                    if(!isset($instance['labelswithin']) && !isset($instance['customfields'])){
                        $instance['customfields']=$fieldParams['default'];
                        $instance['labelswithin']='labels_within';
                    }

                    $modelCustomF=&WYSIJA::get('user_field','model');
                    $modelCustomF->orderBy('field_id','ASC');
                    $customs=$modelCustomF->get(false,array('type'=>"0"));

                    $custombyid=array();
                    $classDivLabel='style="float:left"';
                    $fieldHTML= '<div style="'.$styleDivSeparators.'">';

                    foreach($customs as $customf){
                        $custombyid[$customf['column_name']]=$customf;

                        if(is_array($valuefield) && isset($valuefield[$customf['column_name']])) $checked=true;
                        else $checked=false;

                        $fieldHTML.= '<p style="margin:0 0 5px 0; float:left; margin-left:5px;"><label for="'.$this->get_field_id($field.$customf['field_id']).'">'.
                                $formObj->checkbox( array('id'=>$this->get_field_id($field.$customf['field_id']),
                                    'name'=>$this->get_field_name($field)."[".$customf['column_name']."][column_name]"),
                                        $customf['column_name'],$checked).$customf['name'].'</label></p>';
                    }
                    $fieldHTML .= '</div>';



                    $fieldHTML.= '<p style="clear:both;margin: 0;">'.$this->fields['labelswithin']['label'].'</p>';
                    $value='labels_within';
                    $checked=true;
                    if(!isset($instance['labelswithin']) || $instance['labelswithin']!='labels_within') $checked=true;

                    $id=str_replace('_','-',$key).'-'.$value;
                    $fieldHTML.='<p style="'.$styleDivSeparators.'"><label for="'.$id.'">';
                    $fieldHTML.=$formObj->radio(array('id'=>$id,'name'=>$this->get_field_name('labelswithin')),$value,$checked);
                    $fieldHTML.=__('Yes',WYSIJA).'</label>';

                    $value='labels_out';
                    $checked=false;
                    if((isset($instance['labelswithin']) && $instance['labelswithin']=='labels_out')) $checked=true;
                    $id=str_replace('_','-',$key).'-'.$value;
                    $fieldHTML.='<label for="'.$id.'">';
                    $fieldHTML.=$formObj->radio(array('id'=>$id,'name'=>$this->get_field_name('labelswithin')),$value,$checked);
                    $fieldHTML.=__('No',WYSIJA).'</label>';
                    $fieldHTML .= '</p>';

                    $fieldParamsLabels['email']=array('core'=>1,
                            'label'=>__('Label for email:',WYSIJA),
                            'default'=>__('Email',WYSIJA));
                     $custombyid['email']['name']='email';

                    /*custom fields management for labels*/

                    if(isset($instance['customfields']) && $instance['customfields']){
                         /* set label as default value */
                        foreach($instance['customfields'] as $cf_id => $customfield){
                            $defaultvalue='';
                            if(isset($valuefield[$cf_id]['label'])) $defaultvalue=$valuefield[$cf_id]['label'];
                            if(!$defaultvalue) $defaultvalue=$custombyid[$cf_id]['name'];
                            $fieldParamsLabels[$cf_id]=array('core'=>1,
                                'label'=>sprintf(__('Label for %1$s:',WYSIJA),$custombyid[$cf_id]['name']),
                                'default'=>$defaultvalue);
                        }
                    }

                    if(isset($instance['customfields']) ){
                        $fieldHTML.="<div style='clear:both;'>";

                        foreach($fieldParamsLabels as $cfield_id => $customlabel){
                            $valuef='';
                            if(isset($valuefield[$cfield_id]['label'])) $valuef=$valuefield[$cfield_id]['label'];
                            if(!$valuef)    $valuef=$customlabel['default'];

                            if(count($fieldParamsLabels) == 1 && isset($instance['labelswithin']) && $instance['labelswithin']=='labels_within' || count($fieldParamsLabels) > 1){
                                $fieldHTML.= '<p><label for="'.$this->get_field_id($field.$cfield_id).'">'.$customlabel['label'];
                                $fieldHTML.= $formObj->input( array('id'=>$this->get_field_id($field.$cfield_id),'name'=>$this->get_field_name($field)."[".$cfield_id."][label]"),$valuef);
                                $fieldHTML.= '</label></p>';
                            }
                            else{
                                $fieldHTML.= $formObj->hidden( array('id'=>$this->get_field_id($field.$cfield_id),'name'=>$this->get_field_name($field)."[".$cfield_id."][label]"),$valuef);
                            }


                        }
                        $fieldHTML.="<div style='clear:both;'></div></div>";
                        //dbg($fieldHTML,0);
                    }



                    break;

                case 'instruction':
                case 'success':
                    $fieldHTML= $formObj->textarea( array('id'=>$this->get_field_id($field),'name'=>$this->get_field_name($field),'value'=>$valuefield,"cols"=>46,"rows"=>4,"style"=>'width:404px'),$valuefield);
                    break;
                case 'iframe':
                    $fieldHTML=$textareas=$labels='';
                    $fieldParams['nolabel']=1;
                    if(!empty($instance)){
                        $fieldstype=array('iframe'=>__('Get iFrame version',WYSIJA),'php'=>__('Get PHP version',WYSIJA));

                        $i=0;
                        foreach($fieldstype as $myfield=>$mytitle){
                            if($myfield=='iframe') {
                                $scriptCloseOther='document.getElementById(\''.$this->get_field_id('php').'\').style.display=\'none\';';
                                $valuefield=$this->genIframe($instance,true);
                            }
                            else{
                                $scriptCloseOther='document.getElementById(\''.$this->get_field_id('iframe').'\').style.display=\'none\';';
                                $valuefield=$this->genPhp($instance,true);
                            }
                            $scriptlabel=' style="color:#456465;text-decoration:underline;" onClick="'.$scriptCloseOther.'document.getElementById(\''.$this->get_field_id($myfield).'\').style.display = (document.getElementById(\''.$this->get_field_id($myfield).'\').style.display != \'none\' ? \'none\' : \'block\' );" ';
                            $labels.='<label for="'.$this->get_field_id($myfield).'" '.$scriptlabel.'>'.$mytitle.'</label>';
                            if($i<=0)$labels.=' | ';
                            $textareas.= $formObj->textarea( array('id'=>$this->get_field_id($myfield),'class'=>'disabled hidden','name'=>'dummyname','value'=>$valuefield,'readonly'=>'readonly',"cols"=>46,"rows"=>4,"style"=>'display:none;width:404px;'),$valuefield);
                            //$fieldHTML.='<a href="javascript:;" onClick="alert(\'hello\')">'.$fieldParams['label'].'</a></div>';
                            $i++;
                        }
                        $fieldHTML='<div>'.$labels.'</div>'.$textareas;


                    }

                    break;
                default:
                    $fieldHTML= $formObj->input( array('id'=>$this->get_field_id($field),'name'=>$this->get_field_name($field)),$valuefield ,' size="40" ');
                    break;
            }

            $html.='<div style="margin:10px 0;">';
            if(!isset($fieldParams['nolabel'])){

                $html.='<div '.$classDivLabel.'><label for="'.$this->get_field_id($field).'" '.$extrascriptLabel.'>'.$fieldParams['label'].'</label></div>';
            }
            $html.=$fieldHTML;
            $html.='<div style="clear:both;"></div></div>';

        }

        echo $html;

    }

    function genIframe($instance,$externalsite=false){
        $now=time();

        $encodedForm=base64_encode(json_encode($instance));

        $paramsurl=array(
                'wysija-page'=>1,
                'controller'=>"subscribers",
                'action'=>"wysija_outter",
                );

        if(isset($this->number) && $this->number >0) {
            $paramsurl['widgetnumber']=$this->number;
            $idframe=$this->number;
        }
        else{
           $paramsurl['fullWysijaForm']=$encodedForm;
           $idframe=  rand(45000, 99999);
        }

        $modelConf=&WYSIJA::get("config","model");
        $onloadattr='';
        if($externalsite){
            $paramsurl['external_site']=1;

        }else{
            $onloadattr='onload="jQuery.WYSIJA_iframeloadhandler(this);"';
        }

        /*if(WYSIJA::is_plugin_active('wp-super-cache/wp-cache.php')){
            global $cache_page_secret;
            $paramsurl['donotcachepage']=$cache_page_secret;
        }*/

        //the final tru allow for shorter url
        $fullurl=WYSIJA::get_permalink($modelConf->getValue('confirm_email_link'),$paramsurl,true);


        //return '<iframe width="100%" scrolling="no" frameborder="0" src="'.$fullurl.'" name="wysija-'.$now.'" class="iframe-wysija" id="wysija-'.$idframe.'" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 330px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="'.__('Subscription Wysija',WYSIJA).'"></iframe>';
        return '<iframe '.$onloadattr.' width="100%" scrolling="no" frameborder="0" src="'.$fullurl.'" name="wysija-'.$now.'" class="iframe-wysija" id="wysija-'.$idframe.'" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 330px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="'.__('Subscription Wysija',WYSIJA).'"></iframe>';
        //$fieldHTML='<div class="widget-control-actions">';
    }

    function genPhp($instance,$externalsite=false){

        $instance2=$instance;
        $instance2['widget_id']=$this->id.'-php';
        $phpcode='$widgetdata='.var_export($instance2,true).';'."\n";
        $phpcode.='$widgetNL=new WYSIJA_NL_Widget(1);'."\n";
        $phpcode.='$subscriptionForm= $widgetNL->widget($widgetdata,$widgetdata);'."\n";
        $phpcode.='echo $subscriptionForm;'."\n";

        return $phpcode;
        //$fieldHTML='<div class="widget-control-actions">';
    }

    function widget($args, $instance) {
        extract($args);
        $config=&WYSIJA::get("config","model");
        //if(!$config->getValue("sending_emails_ok")) return;
        foreach($this->fields as $field => $fieldParams){
            if(isset($this->coreOnly) && !isset($fieldParams['core'])) continue;
            if($field=="success" && $instance[$field]==$this->successmsgsub." ".$this->successmsgconf){
                if(!$config->getValue("confirm_dbleoptin")){
                    $instance[$field]=$this->successmsgsub;
                }
            }
        }

        $instance['id_form']=str_replace('_','-',$args['widget_id']);

        if(!isset($this->coreOnly)) $title = apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
        //dbg($before_title);
        /* some worpress weird thing for widgets management */
        if(!isset($before_widget)) $before_widget="";
        if(!isset($after_widget)) $after_widget="";
        if(!isset($before_title)) $before_title="";
        if(!isset($after_title)) $after_title="";

        $glob= $before_widget;
        if ( !isset($this->coreOnly) && $title ) $title=$before_title . $title . $after_title;
        else $title="";


        $view=&WYSIJA::get("widget_nl","view","front");
        /*if a cache plugin is active let's load the plugin in an iframe*/

        /*if(!is_admin() && !$this->iFrame && (WYSIJA::is_plugin_active('wp-super-cache/wp-cache.php') || WYSIJA::is_plugin_active('w3-total-cache/w3-total-cache.php'))){*/
        /*if(!is_admin() && !$this->iFrame && WYSIJA::is_caching_active()){
            $view->addScripts();
            $glob.=$title.$this->genIframe($instance);
        }else{*/
            $glob.=$view->display($title,$instance,false,$this->iFrame);
        //}
        $glob.= $after_widget;

        if($this->iFrame){
            $glob=$view->wrap($glob);
        }

        if(isset($this->coreOnly) && $this->coreOnly) return $glob;
        else echo $glob;
    }
}

/*user synch moved*/
add_action('user_register', array("WYSIJA", 'hook_add_WP_subscriber'), 1);
add_action('profile_update', array("WYSIJA", 'hook_edit_WP_subscriber'), 1);
add_action('delete_user', array("WYSIJA", 'hook_del_WP_subscriber'), 1);

/**/
add_action('transition_post_status', array("WYSIJA", 'hook_postNotification_transition'), 1, 3);
//add_action('publish_post', array("WYSIJA", 'hook_postNotification_now'), 1, 1);
//add_action('publish_future_post', array("WYSIJA", 'hook_postNotification_future'), 1, 1);
add_action('wysijaSubscribeTo', array("WYSIJA", 'hook_subscriber_to_list'), 1);


/*add image size for emails*/
add_image_size( 'wysija-newsletters-max', 600, 99999 );

/* some processing for cron management */
add_filter( 'cron_schedules', array( "WYSIJA", 'filter_cron_schedules' ) );
add_action( 'wysija_cron_queue', array( "WYSIJA", 'croned_queue' ) );
add_action( 'wysija_cron_bounce', array( "WYSIJA", 'croned_bounce' ) );
add_action( 'wysija_cron_daily', array( "WYSIJA", 'croned_daily' ) );
add_action( 'wysija_cron_weekly', array( "WYSIJA", 'croned_weekly' ) );
add_action( 'wysija_cron_monthly', array( "WYSIJA", 'croned_monthly' ) );

if(!wp_next_scheduled('wysija_cron_daily')) wp_schedule_event( time() , 'daily', 'wysija_cron_daily' );



if(!wp_next_scheduled('wysija_cron_queue')){
    $modelConf=&WYSIJA::get("config","model");

    wp_schedule_event( $modelConf->getValue('last_save') , $modelConf->getValue('sending_emails_each'), 'wysija_cron_queue' );
}

if(!wp_next_scheduled('wysija_cron_bounce')){
    $modelConf=&WYSIJA::get("config","model");

    wp_schedule_event( $modelConf->getValue('last_save') , $modelConf->getValue('bouncing_emails_each'), 'wysija_cron_bounce' );
}

if(!wp_next_scheduled('wysija_cron_weekly')){
    $modelConf=&WYSIJA::get("config","model");
    wp_schedule_event( $modelConf->getValue('last_save') , 'eachweek', 'wysija_cron_weekly' );
}

if(!wp_next_scheduled('wysija_cron_monthly')){
    $modelConf=&WYSIJA::get("config","model");
    wp_schedule_event( $modelConf->getValue('last_save') , 'each28days', 'wysija_cron_monthly' );
}


register_deactivation_hook(WYSIJA_FILE, array( "WYSIJA", 'deactivate' ));
register_activation_hook(WYSIJA_FILE, array( "WYSIJA", 'activate' ));
//register_uninstall_hook(WYSIJA_FILE,array("WYSIJA",'uninstall'));
add_action( 'init', array('WYSIJA','create_post_type') );



$helper=&WYSIJA::get(WYSIJA_SIDE,"helper");
