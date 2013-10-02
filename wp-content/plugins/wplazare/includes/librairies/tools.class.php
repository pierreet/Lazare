<?php
/**
* Define the different tools for the entire plugin
* 
*	Define the different tools for the entire plugin
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different tools for the entire plugin
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_tools
{
	
	/**
	*	Return a variable with some basic treatment
	*
	*	@param mixed $varToSanitize The variable we want to treat for future use
	*	@param mixed $varDefaultValue The default value to set to the variable if the different test are not successfull
	*	@param string $varType optionnal The type of the var for better verification
	*
	*	@return mixed $sanitizedVar The var after treatment
	*/
	public static function varSanitizer($varToSanitize, $varDefaultValue = '', $varType = '')
	{
		$sanitizedVar = (trim(strip_tags(stripslashes($varToSanitize))) != '') ? trim(strip_tags(stripslashes(($varToSanitize)))) : $varDefaultValue ;

		return $sanitizedVar;
	}

	/**
	*	Allows to create recursiv directory
	*
	*	@see changeAccesAuthorisation
	*	@param string $directory The complete path we want to create
	*/
    public static function createDirectory($directory)
	{
		$directoryComponent = explode('/',$directory);
		$str = '';
		foreach($directoryComponent as $k => $component)
		{
			if((trim($component) != '') && (trim($component) != '..') && (trim($component) != '.'))
			{
				$str .= '/' . trim($component);
				if(long2ip(ip2long($_SERVER["REMOTE_ADDR"])) == '127.0.0.1')
				{
					if(!is_dir(substr($str,1)) && (!is_file(substr($str,1)) ) )
					{
						mkdir( substr($str,1) );
					}
				}
				else
				{
					if(!is_dir($str) && (!is_file($str) ) )
					{
						mkdir( $str );
					}
				}
			}
		}
		wplazare_tools::changeAccesAuthorisation($directory);
	}

	/**
	*	Allows to change authorisation acces on a complete directory
	*
	*	@param string $directory The complete path we want to change authorisation
	*
	*/
    public static function changeAccesAuthorisation($directory)
	{
		$tab=explode('/',$directory);
		$str='';
		foreach($tab as $k => $v )
		{
			if((trim($v)!=''))
			{
				$str.='/'.trim($v);
				if( (trim($v)!='..') &&(trim($v)!='.') )
				{
					if(!is_dir(substr($str,1)) && (!is_file(substr($str,1)) ) )
					{
						chmod(str_replace('//','/',$str), 0755);
					}
				}
			}
		}
	}

	/**
	*	Allows to copy an entire directory to another path
	*
	*	@see createDirectory
	*	@param string $sourceDirectory The complete path we want to copy in an another path
	*	@param string $destinationDirectory The destination path that will receive the cpied content
	*
	*/
    public static function copyEntireDirectory($sourceDirectory, $destinationDirectory)
	{
		if(is_dir($sourceDirectory))
		{
			if(!is_dir($destinationDirectory))
			{
				wplazare_tools::createDirectory($destinationDirectory);
			}
			$hdir = opendir($sourceDirectory);
			while($item = readdir($hdir))
			{
				if(is_dir($sourceDirectory . '/' . $item) && ($item != '.') && ($item != '..')  && ($item != '.svn') )
				{
					wplazare_tools::copyEntireDirectory($sourceDirectory . '/' . $item, $destinationDirectory . '/' . $item);
				}
				elseif(is_file($sourceDirectory . '/' . $item))
				{
					copy($sourceDirectory . '/' . $item, $destinationDirectory . '/' . $item);
				} 
			}
			closedir( $hdir );
		}
	}

	/**
	*	Return a form field type from a database field type
	*
	*	@param string $dataFieldType The database field type we want to get the form field type for
	*
	*	@return string $type The form input type to use for the given field
	*/
    public static function defineFieldType($dataFieldType)
	{
		$type = 'text';
		if(($dataFieldType == 'char') || ($dataFieldType == 'varchar') || ($dataFieldType == 'int'))
		{
			$type = 'text';
		}
		elseif($dataFieldType == 'text')
		{
			$type = 'textarea';
		}
		elseif($dataFieldType == 'enum')
		{
			$type = 'select';
		}

		return $type;
	}

	/**
	*	Transform a given text with a specific pattern, send by the second parameter
	*
	*	@param string $toSlugify The string we want to "clean" for future use
	*	@param array|string $slugifyType The type of cleaning we are going to do on the input text
	*
	*	@return string $slugified The input string that was slugified with the selected method
	*/
    public static function slugify($toSlugify, $slugifyType)
	{
		$slugified = '';

		if($toSlugify != '')
		{
			$slugified = $toSlugify;
			foreach($slugifyType as $type)
			{
				if($type == 'noAccent')
				{
					$pattern = array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/");
					$rep_pat = array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U","e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U");
				}
				elseif($type == 'noSpaces')
				{
					$pattern = array('/\s/');
					$rep_pat = array('_');
					$slugified = trim($slugified);
				}
				elseif($type == 'lowerCase')
				{
					$slugified = strtolower($slugified);
				}

				if(is_array($pattern) && is_array($rep_pat))
				{
					$slugified = preg_replace($pattern, $rep_pat, utf8_decode($slugified));
				}
			}
	  }
	  
	  return $slugified;
	}
	
	/**
	* getResponsables($habitant_id)
	* return the habitant_id,appartement_id,responsable_Appart_id,responsable_Maison_id for each given $habitant_id
	* 
	* $habitant_id may be a integer or an array or false
	* if false -> return all the responsables of appart that are occupied
	*/
    public static function getResponsables($locataire_id)
	{
		global $wpdb;

		$res = "";
		if ($locataire_id)
		{
			$query = 	"SELECT WPLOCATIONS.user AS habitant_id, ".
		 	"WPLOCATIONS.appartement AS appartement_id, ".
			"WPAPPARTS.responsable AS responsable_Appart_id, ".
			"WPMAISONS.responsable AS responsable_Maison_id, ".
			"WPASSOS.responsable AS responsable_Association_id ".
			"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
			"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
			"LEFT JOIN ".wplazare_maisons::getDbTable()." AS WPMAISONS ON WPAPPARTS.maison=WPMAISONS.id ".
			"LEFT JOIN ".wplazare_associations::getDbTable()." AS WPASSOS ON WPAPPARTS.association=WPASSOS.id ".
			"WHERE (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin LIKE '0000-00-00') AND WPLOCATIONS.status LIKE 'valid'  ";
					
			if (is_array($locataire_id))
			{
				$matches = implode(',', $locataire_id);				
				$query .= "AND WPLOCATIONS.user IN ($matches) ";
				$query_prep = $wpdb->prepare($query);
				$res = $wpdb->get_results($query_prep);
			}
			else
			{
				$query .= "AND WPLOCATIONS.user = $locataire_id ";
				$query_prep = $wpdb->prepare($query);
				$res = $wpdb->get_row($query_prep);			
			}
		}

		return $res;
	}
	
	/**
	* getResponsablesForSelect()
	* return les id et nom des utilisateurs avec le rôle responsables, utilisables dans un select
	* 
	*/
    public static function getResponsablesForSelect(){
		$elements = array(); 
		$elements['0'] = '- Pas de responsable -';
		$blogusers = get_users('role=Benevole');
		
	    foreach ($blogusers as $user) {
	        $resp_name = wplazare_tools::getUserName($user->ID);
			$elements[$user->ID] = $resp_name;
	    }
	    
		$blogusers = get_users('role=Administrator');
		
	    foreach ($blogusers as $user) {
	        $resp_name = wplazare_tools::getUserName($user->ID);
			$elements[$user->ID] = $resp_name;
	    }
	    
		return $elements;
	}
	
	/**
	* getTresoriersForSelect()
	* return les id et nom des utilisateurs avec le role tresorier, utilisables dans un select
	* 
	*/
    public static function getTresoriersForSelect(){
		$elements = array(); 
		$elements['0'] = '- Pas de tr&eacute;sorier -';
		$blogusers = get_users('role=Benevole');
		
	    foreach ($blogusers as $user) {
	        $resp_name = wplazare_tools::getUserName($user->ID);
			$elements[$user->ID] = $resp_name;
	    }
	    
		$blogusers = get_users('role=Administrator');
		
	    foreach ($blogusers as $user) {
	        $resp_name = wplazare_tools::getUserName($user->ID);
			$elements[$user->ID] = $resp_name;
	    }
	    
		return $elements;
	}
	
	/**
	* getAssociationsForSelect()
	* return les id et nom des associations, utilisables dans un select
	* 
	*/
    public static function getAssociationsForSelect(){
		$elements = array(); 
		$elements['0'] = '- Pas d\'association -';
		
	    global $wpdb;
		
		$query = 	"SELECT WPASSOCIATIONS.* ".
					"FROM ".wplazare_associations::getDbTable()." AS WPASSOCIATIONS ";
		
		$query_prep = $wpdb->prepare($query);
		foreach ($wpdb->get_results($query_prep) as $association){
			$elements[$association->id] = $association->nom;
		}
		return $elements;
	}
	
	/**
	* getMaisonsForSelect()
	* return les id et nom des maisons, utilisables dans un select
	* 
	*/
    public static function getMaisonsForSelect(){
		$elements = array(); 
		$elements['0'] = '- Pas d\'association -';
		
	    global $wpdb;
		
		$query = 	"SELECT WPMAISONS.* ".
					"FROM ".wplazare_maisons::getDbTable()." AS WPMAISONS ";
		
		$query_prep = $wpdb->prepare($query);
		foreach ($wpdb->get_results($query_prep) as $maison){
			$elements[$maison->id] = $maison->nom;
		}
		return $elements;
	}
	
	/**
	* getUserName($id)
	* return le nom d'un user en fonction de son id
	* 
	*/
    public static function getUserName($id){
		if($id<=0) return '';
		
		$name = ucfirst(get_user_meta($id,'first_name',true))." ".ucfirst(get_user_meta($id,'last_name',true));
		if($name=='') $name = ucfirst(get_user_meta($id,'nickname',true));
	    
		return $name;
	}
	
	/**
	* getUserName($id)
	* return le nom d'un user en fonction de son id
	* 
	*/
    public static function getFirstName($id){
		if($id<0) return '';
		
		$first_name = get_user_meta($id,'first_name',true);
		if($first_name=='') $first_name = get_user_meta($id,'nickname',true);
	    
		return ucfirst($first_name);
	}
	
	/**
	* getLocataires()
	* return les différentes locataires I.E. users de role benevole ou personne_accueillie
	* 
	*/
    public static function getLocataires(){
		$locataires = array();
		$benevoles = get_users('role='.WPLAZARE_ROLE_BENEVOLE);
	    foreach ($benevoles as $locataire) {
	        $locataires[$locataire->ID] = wplazare_tools::getUserName($locataire->ID);
	    }
		$personnes_accueillies = get_users('role='.WPLAZARE_ROLE_PERSONNE_ACCUEILLIE);
	    foreach ($personnes_accueillies as $locataire) {
	        $locataires[$locataire->ID] = wplazare_tools::getUserName($locataire->ID);
	    }
		$postulants = get_users('role='.WPLAZARE_ROLE_POSTULANT);
	    foreach ($postulants as $locataire) {
	        $locataires[$locataire->ID] = wplazare_tools::getUserName($locataire->ID);
	    }
		$postulants = get_users('role=administrator');
	    foreach ($postulants as $locataire) {
	        $locataires[$locataire->ID] = wplazare_tools::getUserName($locataire->ID);
	    }
		return $locataires;
		
	}
	
	/**
	* getRole($user_id)
	* return le role de l'utilisateur dont l'id est en paramètre
	* 
	*/
    public static function getRole($user_id){
		$user = new WP_User( $user_id );
		$user_roles = $user->roles;

		$user_role = array_shift($user_roles);
	
		return $user_role;
	}
	
	/**
	* getRoles()
	* return tous les roles
	* 
	*/
    public static function getRoles(){
		return array(WPLAZARE_ROLE_PERSONNE_ACCUEILLIE
		,WPLAZARE_ROLE_BENEVOLE,WPLAZARE_ROLE_PARTENAIRE_SOCIAL
		,WPLAZARE_ROLE_POSTULANT,WPLAZARE_ROLE_DONATEUR,WPLAZARE_ROLE_EVENT
		,WPLAZARE_ROLE_ORIENTER);
	}
	
	/**
	* getUserCountByRole($role)
	* return Le nombre d'utilisateur ayant le role en parametre.
	* 
	*/
    public static function getUserCountByRole($role){
		$results = '';
		if($role == WPLAZARE_ROLE_DONATEUR){
			global $wpdb;
			$query = $wpdb->prepare(
			"SELECT DISTINCT O.user_email,O.user_firstname,O.user_lastname,O.user_phone,O.user_adress,O.user_birthday
			FROM " . wplazare_users::getOrdersDbTable() . " AS O WHERE O.user_firstname NOT LIKE '' AND O.user_lastname NOT LIKE ''"
			);
	
			$results = $wpdb->get_results($query);
		}
		else{
			if($role == "attente"){
				$results = get_users("role=".WPLAZARE_ROLE_BENEVOLE);
				$res = array();
				foreach($results as $result){
					if(wplazare_tools::getLocation($result->ID) == ''){
						$res[] = $result;
					}
				}
				$results = $res;
			}
			else{
				$results = get_users("role=$role");
			}
		} 		
		return count($results);
	}
	
	/**
	* getAppartement($user_id)
	* return l'id de l'appartement de l'utilisateur si il en a un
	* 
	*/
    public static function getAppartement($user_id){
		global $wpdb;
		
		$query = 	"SELECT WPLOCATIONS.appartement ".
					"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
					"WHERE (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin LIKE '0000-00-00')".
					"AND WPLOCATIONS.user=$user_id AND WPLOCATIONS.status LIKE 'valid' ";
		
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_results($query_prep);
		if(count($res)>0) return $res[0]->appartement;
		else return '';
	}

    /**
     * getLocation($user_id)
     * return l'id de la location de l'utilisateur si il en a un
     *
     */
    public static function getLocation($user_id){
        global $wpdb;

        $query = 	"SELECT * ".
            "FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
            "WHERE (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin LIKE '0000-00-00')".
            "AND WPLOCATIONS.user=$user_id AND WPLOCATIONS.status LIKE 'valid' ";

        $query_prep = $wpdb->prepare($query);
        $res = $wpdb->get_results($query_prep);
        if(count($res)>0) return $res[0];
        else return '';
    }
	
    /**
	* getLocationByAppart($appart_id)
	* return les location_id liées à l'appart
	* 
	* $appart_id l'id de l'appart
	*/
    public static function getLocationByAppart($appart_id)
	{
		global $wpdb;

		$query = 	"SELECT WPLOCATIONS.id AS id ".
					"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
					"WHERE WPLOCATIONS.appartement=$appart_id";
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_results($query_prep);

		return $res;
	}

    public static function getAdresseAssociation($asso){
		if(strtolower($asso) == 'lazare'){
			return '1 rue du Pl&acirc;tre, 75004 PARIS';
		}
		elseif(strtolower($asso) == 'apa'){
			return '';
		}
		return '';
	}	
	/**
	* isResponsable($user_id)
	* return l'id de l'appartement de l'utilisateur si il en est responsable, '' sinon
	* 
	*/
    public static function isResponsable($user_id){
		global $wpdb;
		
		$query = 	"SELECT WPAPPARTS.id ".
					"FROM ".wplazare_apparts::getDbTable()." AS WPAPPARTS ".
					"WHERE WPAPPARTS.responsable=$user_id";
		
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_results($query_prep);
		if(count($res)>0) return $res[0]->id;
		else return '';
	}
	
	/**
	* getLocatairesByAppart($appart_id)
	* return les user_id des locataires
	* 
	* $appart_id l'id de l'appart
	*/
    public static function getLocatairesByAppart($appart_id)
	{
		global $wpdb;

		$query = 	"SELECT DISTINCT WPLOCATIONS.user AS id ".
					"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
					"WHERE (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin LIKE '0000-00-00') ".
					"AND WPLOCATIONS.appartement=$appart_id  AND WPLOCATIONS.status LIKE 'valid' ";
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_results($query_prep);

		return $res;
	}
	
	/**
	* getUserLink($user_id, $label)
	* return une balise <a> vers les informations de l'utilisateur dont l'id est en param�tre.
	* 
	*/
    public static function getUserLink($user_id, $label){
		return '<a href="'.admin_url('admin.php?page=' . wplazare_users::getEditionSlug() . '&amp;action=edit&amp;id=' . $user_id).'">'.$label.'</a>';
	}
	
	/**
	 * 
	 * In case you need to sort an associative array by one of its keys
	 * @param array $array
	 * @param string $key cl� de recherche
	 * @param boolean $asc ordre croissant ou d�croissant
	 */
    public static function sortByOneKey(array $array, $key, $asc = true) {
	    $result = array();
	        
	    $values = array();
	    foreach ($array as $id => $value) {
	        $values[$id] = isset($value[$key]) ? $value[$key] : '';
	    }
	        
	    if ($asc) {
	        asort($values);
	    }
	    else {
	        arsort($values);
	    }
	        
	    foreach ($values as $key => $value) {
	        $result[$key] = $array[$key];
	    }
	        
	    return $result;
	}
	
	/**
	 * 
	 * getLocatairesEtAppartsOccupes() retourne tous les apparts occup�s
	 */
    public static function getLocatairesEtAppartsOccupes()
	{
		global $wpdb;

		$query = "SELECT WPLOCATIONS.user AS locataire_id, ".
		"WPAPPARTS.id AS appart_id ".
		"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
		"WHERE (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin LIKE '0000-00-00') AND WPLOCATIONS.status LIKE 'valid' ORDER BY appart_id ";
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_results($query_prep);
					
		return $res;
	}

    public static function subscribeNewsletter($role,$firstname,$lastname,$email,$user_id = ''){
		global $wpdb;
		
		if($email == '') return '';
		
		$query = $wpdb->prepare( "INSERT IGNORE INTO `wp_wysija_user` (`user_id` ,`wpuser_id` ,`email` ,`firstname` ,`lastname` ,`ip` ,`keyuser` ,`created_at` ,`status`)
VALUES (NULL ,  '$user_id',  '$email',  '$firstname',  '$lastname',  '',  '', NULL ,  '0')");
		
		if( $wpdb->query($query) )
		{
			$requestResponse = 'done';
		}
		else
		{
			$requestResponse = 'error';
		}
		
		$id = $wpdb->insert_id;
		
		$query = $wpdb->prepare( "SELECT list_id FROM `wp_wysija_list` WHERE  `namekey` LIKE  '".wplazare_tools::prepareForListName(strtolower($role))."'");
		
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_row($query_prep);
		if($res == null)
			return $requestResponse. ' error';
		$list_id = $res->list_id;
		
		if($list_id != '0'){
			$query = $wpdb->prepare( "INSERT IGNORE INTO `wp_wysija_user_list` (`list_id` ,`user_id` ,`sub_date` ,`unsub_date`)
	VALUES ('$list_id',  '$id',  '0',  '0')");
			
			if( $wpdb->query($query) )
			{
				$requestResponse .= ' done';
			}
			else
			{
				$requestResponse .= ' error';
			}
		}
		
		return $requestResponse;
	}

    public static function unSubscribeNewsletter($user_id){
		global $wpdb;
		
		$query = $wpdb->prepare( "SELECT user_id FROM `wp_wysija_user` WHERE   `wp_wysija_user`.`wpuser_id` =  '$user_id'");
		
		$user_id = '';
		$query_prep = $wpdb->prepare($query);
		$res = $wpdb->get_row($query_prep);
		if($res == null)
			return 'error';
		$user_id = $res->user_id;
		
		$query = $wpdb->prepare( "DELETE FROM `lazare`.`wp_wysija_user` WHERE `wp_wysija_user`.`user_id` = $user_id;");
		
		if( $wpdb->query($query) )
		{
			$requestResponse = 'done';
		}
		else
		{
			$requestResponse = 'error';
		}
		
		$query = $wpdb->prepare( "DELETE FROM `lazare`.`wp_wysija_user_list` WHERE `wp_wysija_user_list`.`user_id` = $user_id;");
		
		if( $wpdb->query($query) )
		{
			$requestResponse = 'done';
		}
		else
		{
			$requestResponse = 'error';
		}
		
		return $requestResponse;
		
	}

    public static function synchroList(){
		$mktime=time();
		
		global $wpdb;

		$query = "INSERT IGNORE INTO `wp_wysija_user` (`wpuser_id`,`email`,`firstname`,`created_at` ,`status` ) 
		SELECT `ID`,`user_email`,`display_name`,$mktime,1 FROM wp_users INNER JOIN wp_usermeta ON ( wp_users.ID = wp_usermeta.user_id ) WHERE wp_usermeta.meta_key = 'wp_capabilities'";

		$prepared_query = $wpdb->prepare($query);
		
		$wpdb->query($prepared_query);
		
		$query = "SELECT * FROM wp_wysija_user WHERE created_at IN ('$mktime') ";
		
		$prepared_query = $wpdb->prepare($query);
		
		$res = $wpdb->get_results($prepared_query);
		
		foreach ($res as $result){
			$role = wplazare_tools::getRole($result->wpuser_id);
			
			$query2 = "SELECT list_id FROM wp_wysija_list WHERE name LIKE '".__($role,'wplazare')."'";
			$prepared_query2 = $wpdb->prepare($query2);
			$res2 = $wpdb->get_row($prepared_query2);
			
			if($res2->list_id != ''){
				$query3 = "INSERT IGNORE INTO `wp_wysija_user_list` (`user_id`,`list_id`,`sub_date`) VALUES ($result->user_id, $res2->list_id, $mktime)";
				$prepared_query3 = $wpdb->prepare($query3);
				$wpdb->query($prepared_query3);
			}
		} 
	}

    public static function lastLogin($user_login) {
		$user = get_user_by('login', $user_login);
	    $last_login = get_user_meta( $user->ID, 'last_login', true ); 
	    if($last_login != '') update_user_meta($user->ID, 'last_login', current_time('mysql'));
	    else add_user_meta($user->ID, 'last_login', date('Y-m-d H:i:s'),true);
	}

    public static function getLastLogin($user_id = '') {
		if($user_id == '') $user_id = get_current_user_id();
	    $last_login = get_user_meta($user_id, 'last_login', true);
	    /*$date_format = get_option('date_format') . ' ' . get_option('time_format');
	    $the_last_login = mysql2date($date_format, $last_login, false);*/
	    return $last_login;
	}

    public static function my_profile_update( $user_id ) {
        wplazare_tools::unSubscribeNewsletter($user_id);
        //wplazare_tools::subscribeNewsletter($_POST['role'], $_POST['first_name'], $_POST['last_name'], $_POST['email'],$user_id);        
    }

    public static function prepareForListName($string){
		if($string == 'subscriber') return 'abonne';
		return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ_',
	'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY-');
	}

    public static function check_rib($cbanque, $cguichet, $nocompte, $clerib) {
        $tabcompte = "";
        $len = strlen($nocompte);
        if ($len != 11) {
                return false;
        }
        for ($i = 0; $i < $len; $i++) {
                $car = substr($nocompte, $i, 1);
                if (!is_numeric($car)) {
                        $c = ord($car) - (ord('A') - 1);
                        $b = (($c + pow ( 2, ($c - 10) / 9 )) % 10) + (($c > 18) ? 1 : 0);
                        $tabcompte .= $b;
                }
                else {
                        $tabcompte .= $car;
                }
        }
        $int = $cbanque . $cguichet . $tabcompte . $clerib;
        return (strlen($int) >= 21 && bcmod($int, 97) == 0);
	}

    public static function check_iban($iban){
	 
	        $charConversion = array("A" => "10","B" => "11","C" => "12","D" => "13","E" => "14","F" => "15","G" => "16","H" => "17",
	"I" => "18","J" => "19","K" => "20","L" => "21","M" => "22","N" => "23","O" => "24","P" => "25","Q" => "26","R" => "27",
	"S" => "28","T" => "29","U" => "30","V" => "31","W" => "32","X" => "33","Y" => "34","Z" => "35");
	 
	        // Déplacement des 4 premiers caractères vers la droite et conversion des caractères
	        $tmpiban = strtr(substr($iban,4,strlen($iban)-4).substr($iban,0,4),$charConversion);
	 
	        // Calcul du Modulo 97 par la fonction bcmod et comparaison du reste à 1
	        return (intval(bcmod($tmpiban,"97")) == 1);
	}

    public static function addSpaceOnPhone($phone_number){
		return chunk_split (str_replace(" ", "", $phone_number), 2, "&nbsp;");
	}

    public static function updateAttendeeRole(){
		$people = array();
		$EM_Events = EM_Events::get(array('scope'=>'past' ));
		foreach ( $EM_Events as $EM_Event ) {
			$lister = $EM_Event->get_bookings();
			$bookerList = $lister->bookings;
			
			foreach($bookerList as $EM_Booking){
			   	echo "user id:".$EM_Booking->person->ID."<br/>";
				$u = new WP_User( $EM_Booking->person->ID );
				
				$userdata = array();
				$userdata['ID'] = $EM_Booking->person->ID;
				$userdata['role'] = 'attendee';
				wp_update_user($userdata);;
			}
		}
	$EM_Events = EM_Events::get(array('scope'=>'future' ));
		foreach ( $EM_Events as $EM_Event ) {
			$lister = $EM_Event->get_bookings();
			$bookerList = $lister->bookings;
			
			foreach($bookerList as $EM_Booking){
			   	echo "user id:".$EM_Booking->person->ID."<br/>";
				$u = new WP_User( $EM_Booking->person->ID );
				
				$userdata = array();
				$userdata['ID'] = $EM_Booking->person->ID;
				$userdata['role'] = 'attendee';
				wp_update_user($userdata);;
			}
		}
	}
}