<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_view_back_config extends WYSIJA_view_back{

    var $title="Settings";
    var $icon="icon-options-general";
    var $skip_header = true;

    function WYSIJA_view_back_support(){
        $this->title=__("Settings",WYSIJA);
        $this->WYSIJA_view_back();
    }
    function reinstall(){
        ?>
        <form name="wysija-settings" method="post" id="wysija-settings" action="" class="form-valid" autocomplete="off">
            <input type="hidden" value="doreinstall" name="action"/>
            <input type="hidden" value="reinstall" name="postedfrom"/>
            <h3><?php _e("If you confirm this, all your current Wysija data will be erased (newsletters, statistics, lists, subscribers, etc.).",WYSIJA); ?></h3>
            <p class="submit">
                <input type="submit" value="<?php _e("Confirm Reinstallation",WYSIJA)?>" class="button-secondary" id="submit" name="submit" />
                <?php $this->secure(array('action'=>"doreinstall")); ?>
            </p>
        </form>
        <?php

    }

    function fieldFormHTML_viewinbrowser($key,$value,$model,$paramsex){
        /*second part concerning the checkbox*/
        $formsHelp=&WYSIJA::get("forms","helper");
        $checked=false;
        if($this->model->getValue($key))   $checked=true;
        $field='<p><label for="'.$key.'">';
        $field.=$formsHelp->checkbox(array("id"=>$key,'name'=>'wysija['.$model.']['.$key.']','class'=>'activateInput'),1,$checked);
        $field.='</label>';
        $value=$this->model->getValue($key.'_linkname');

        $field.=$formsHelp->input(array("id"=>$key.'_linkname','name'=>'wysija['.$model.']['.$key.'_linkname]', 'size'=>'75'),$value).'</p>';

        return $field;
    }

    function fieldFormHTML_debugnew($key,$value,$model,$paramsex){
        /*second part concerning the checkbox*/
        $formsHelp=&WYSIJA::get("forms","helper");
        $selected=$this->model->getValue($key);
        if(!$selected)   $selected=0;
        $field='<p><label for="'.$key.'">';
        $options=array(0=>'off',1=>'SQL queries',2=>'&nbsp+log',3=>'&nbsp&nbsp+safe PHP errors',4=>'&nbsp&nbsp&nbsp+safe PHP errors wp-admin',99=>'&nbsp&nbsp&nbsp&nbsp+PHP errors wp-admin(to use carefully)');
        $field.=$formsHelp->dropdown(array('id'=>$key,'name'=>'wysija['.$model.']['.$key.']'),$options,$selected);
        $field.='</label></p>';

        return $field;
    }

    function fieldFormHTML_dkim($key,$value,$model,$paramsex){

        $field='';
        $keypublickey=$key.'_pubk';

        if(!$this->model->getValue($keypublickey)){
            //refresh the public key private key generation
            $helpersLi=&WYSIJA::get('licence','helper');
            $helpersLi->dkim_config();
        }else{
            WYSIJA::update_option('dkim_autosetup',false);
            $formsHelp=&WYSIJA::get("forms","helper");


            $realkey=$key.'_active';
            $checked=false;
            if($this->model->getValue($realkey))   $checked=true;
            $field.='<p>';
            $field.=$formsHelp->checkbox(array('id'=>$realkey,'name'=>'wysija['.$model.']['.$realkey.']','style'=>'margin-left:0px;','class'=>'activateInput'),1,$checked);
            $field.='</p>';

            $field.='<div id="'.$realkey.'_linkname" >';
            //$titlelink=str_replace(array('[link]','[\link]'), array('<a href="">','</a>'),'');
            $titlelink= __('Configure your DNS by adding a key/value record in TXT as shown below.',WYSIJA).' <a href="http://support.wysija.com/knowledgebase/guide-to-dkim-in-wysija/?utm_source=wpadmin&utm_campaign=settings" target="_blank">'.__('Read more',WYSIJA).'</a>';
            $field.='<fieldset style=" border: 1px solid #ccc;margin: 0;padding: 10px;"><legend>'.$titlelink.'</legend>';

            $field.='<label id="drlab" for="domainrecord">'.__('Key',WYSIJA).' <input readonly="readonly" id="domainrecord" style="margin-right:10px;" type="text" value="wys._domainkey"/></label><label id="drpub" for="dkimpub">'.__('Value',WYSIJA).' <input readonly="readonly" id="dkimpub" type="text" size="70" value="v=DKIM1;k=rsa;g=*;s=email;h=sha1;t=s;p='.$this->model->getValue($keypublickey).'"/>';
            $field.='</fieldset>';
            $realkey=$key.'_domain';
            $field.='<p><label class="dkim" for="'.$realkey.'">'.__('Domain',WYSIJA).'</label>';

            $field.=$formsHelp->input(array('id'=>$realkey,'name'=>'wysija['.$model.']['.$realkey.']'),$this->model->getValue($realkey));
            $field.='</p>';

            $field.='</div>';
        }

        return $field;
    }

    function fieldFormHTML_debug($key,$value,$model,$paramsex){
        /*second part concerning the checkbox*/
        $formsHelp=&WYSIJA::get("forms","helper");
        $checked=false;
        if($this->model->getValue($key))   $checked=true;
        $field='<p><label for="'.$key.'">';
        $field.=$formsHelp->checkbox(array("id"=>$key,'name'=>'wysija['.$model.']['.$key.']'),1,$checked);
        $field.='</label></p>';

        return $field;
    }

    function fieldFormHTML_capabilities($key,$value,$model,$paramsex){
        /*second part concerning the checkbox*/
        $formsHelp=&WYSIJA::get("forms","helper");

        $field='<table width="400" cellspacing="0" cellpadding="3" bordercolor="#FFFFFF" border="0" style="background-color:#FFFFFF" class="fixed">
    <thead>
        <tr>
<th class="rolestitle" style="width:200px">'.__('Roles and permissions',WYSIJA).'</th>';

        $wptools=&WYSIJA::get('wp_tools','helper');
        $editable_roles=$wptools->wp_get_roles();


        foreach($editable_roles as $role){
            $field.='<th class="rolestable" >'.$role['name'].'</th>';
        }

	$field.='</tr></thead><tbody>';

        $alternate=true;
        foreach($this->model->capabilities as $keycap=>$capability){
            $classAlternate='';
            if($alternate) $classAlternate=' class="alternate" ';
            $field.='<tr'.$classAlternate.'><td class="title"><p class="description">'.$capability['label'].'</p></td>';

                    foreach($editable_roles as $role){
                        $checked=false;
                        $keycheck='rolescap---'.$role['key'].'---'.$keycap;

                        //if($this->model->getValue($keycheck))   $checked=true;
                        $checkboxparams=array("id"=>$keycheck,'name'=>'wysija['.$model.']['.$keycheck.']');
                        if(in_array($role['key'], array('administrator','super_admin'))){
                            $checkboxparams['disabled']='disabled';
                        }

                        $roling = get_role( $role['key'] );

                        // add "organize_gallery" to this role object
                        if($roling->has_cap( 'wysija_'.$keycap )){
                            $checked=true;
                        }

                        $field.='<td class="rolestable" >'.$formsHelp->checkbox($checkboxparams,1,$checked).'</td>';
                    }

            $field.='</tr>';
            $alternate=!$alternate;
        }

        $field.='</tbody></table>';

        return $field;
    }



    function fieldFormHTML_email_notifications($key,$value,$model,$paramsex){
        /* first part concerning the field itself */
        $params=array();
        $params['type']="default";
        $field=$this->fieldHTML($key,$value,$model,$params);

        /*second part concerning the checkbox*/
        $threecheck=array(
            "_when_sub" =>__('When someone subscribes',WYSIJA)
            ,"_when_unsub"=>__('When someone unsubscribes',WYSIJA),
            "_when_dailysummary"=>__('Daily summary of emails sent',WYSIJA)
            //,"_when_bounce"=>__('When an email bounces',WYSIJA)
            );
        $formsHelp=&WYSIJA::get("forms","helper");
        foreach($threecheck as $keycheck => $checkobj){
            $checked=false;
            if($this->model->getValue($key.$keycheck))$checked=true;
            $field.='<p><label for="'.$key.$keycheck.'">';
            $field.=$formsHelp->checkbox(array("id"=>$key.$keycheck,'name'=>'wysija['.$model.']['.$key.$keycheck.']'),1,$checked);
            $field.=$checkobj.'</label></p>';
        }

        return $field;
    }


    function fieldFormHTML_selfsigned($key,$value,$model,$params){

        $formsHelp=&WYSIJA::get("forms","helper");

        $realvalue=$this->model->getValue($key);

        $value=0;
        $checked=false;
        if($value ==$realvalue) $checked=true;
        $id=str_replace("_",'-',$key).'-'.$value;
        $field='<label for="'.$id.'">';
        $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija['.$model.']['.$key.']'),$value,$checked);
        $field.=__('No',WYSIJA).'</label>';

        $value=1;
        $checked=false;
        if($value ==$realvalue) $checked=true;
        $id=str_replace("_",'-',$key).'-'.$value;
        $field.='<label for="'.$id.'">';
        $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija['.$model.']['.$key.']'),$value,$checked);
        $field.=__('Yes',WYSIJA).'</label>';

        return $field;
    }

    function tabs($current = 'basics') {
        $tabs = array(
            'basics' => __('Basics', WYSIJA),
            'emailactiv' => __('Activation Email', WYSIJA),
            'sendingmethod' => __('Sending Method', WYSIJA),
            'bounce' => __('Bounce Handling', WYSIJA),
            'advanced' => __('Advanced', WYSIJA),
            'premium' => __('Premium Upgrade', WYSIJA)
        );

        $modelC =& WYSIJA::get('config', 'model');
        // check whether the user is premium or not
        $is_premium = (bool)($modelC->getValue('premium_key'));

        if($is_premium) {
            // change premium tab label
            $tabs['premium'] = __('Premium Activated',WYSIJA);
        } else {
            // remove bounce tab
            unset($tabs['bounce']);
        }
        echo '<div id="icon-options-general" class="icon32"><br /></div>';
        echo '<h2 id="wysija-tabs" class="nav-tab-wrapper">';
        foreach($tabs as $tab => $name) {
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            $extra = ($tab === 'premium') ? ' tab-premium' : '';
            echo "<a class='nav-tab$class$extra' href='#$tab'>$name</a>";
        }
        echo '</h2>';
    }

    function innertabs($current = 'connection') {
        $tabs = array(
            'connection' => __('Settings', WYSIJA),
            'actions' => __('Actions & Notifications', WYSIJA)
        );

        echo '<h3 id="wysija-innertabs" class="nav-tab-wrapper">';
        foreach($tabs as $tab => $name) {
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='#$tab'>$name</a>";
        }
        echo '</h2>';
    }


    function main(){
        $modelC =& WYSIJA::get('config', 'model');
        $is_premium = (bool)($modelC->getValue('premium_key'));
        echo $this->messages();
        ?>
        <div id="wysija-config">
            <?php $this->tabs(); ?>
            <form name="wysija-settings" method="post" id="wysija-settings" action="" class="form-valid" autocomplete="off">
                <div id="basics" class="wysija-panel">
                    <?php $this->basics(); ?>
                    <p class="submit">
                    <input type="submit" value="<?php echo esc_attr(__('Save settings',WYSIJA)); ?>" class="button-primary wysija" />
                    </p>
                </div>
                <div id="emailactiv" class="wysija-panel">
                    <?php $this->emailactiv(); ?>
                    <p class="submit">
                    <input type="submit" value="<?php echo esc_attr(__('Save settings',WYSIJA)); ?>" class="button-primary wysija" />
                    </p>
                </div>
                <div id="sendingmethod" class="wysija-panel">
                    <?php $this->sendingmethod(); ?>
                    <p class="submit">
                    <input type="submit" value="<?php echo esc_attr(__('Save settings',WYSIJA)); ?>" class="button-primary wysija" />
                    </p>
                </div>

                <div id="bounce" class="wysija-panel">
                    <?php
                    $config=&WYSIJA::get("config","model");

                    if(!$config->getValue("premium_key")){
                        echo str_replace(array('[link]','[/link]'),array('<a class="premium-tab" href="javascript:;" title="'.__("See all Premium features",WYSIJA).'">','</a>'),__("Spam filters will notice when you send to invalid addresses. Let Wysija handle your invalid email addresses automatically. This feature is part of the [link]Premium features[/link]</strong>.",WYSIJA));
                    }else {
                        $this->bounce();
                    } ?>
                    <p class="submit">
                    <input type="submit" value="<?php echo esc_attr(__('Save settings',WYSIJA)); ?>" class="button-primary wysija" />
                    </p>
                </div>
                <div id="advanced" class="wysija-panel">
                    <?php $this->advanced(); ?>
                    <p class="submit">
                    <input type="submit" value="<?php echo esc_attr(__('Save settings',WYSIJA)); ?>" class="button-primary wysija" />
                    </p>
                </div>
                <div id="premium" class="wysija-panel">
                    <?php
                    $modelC=&WYSIJA::get("config","model");
                    if($modelC->getValue("premium_key")){
                       $this->premium_activated();
                    }else{
                       $this->premium();
                    }
                     ?>

                </div>

                <p class="submitee">
                    <?php $this->secure(array('action'=>"save")); ?>
                    <input type="hidden" value="save" name="action" />
                    <input type="hidden" value="" name="redirecttab" id="redirecttab" />
                </p>

            </form>
        </div>
        <?php
    }


    function basics(){
        $step=array();

        $step['company_address']=array(
            'type'=>'textarea',
            'label'=>__("Your company's address",WYSIJA),
            'desc'=>__("The address will be added to your newsletters' footer. This helps avoid spam filters.",WYSIJA),
            'rows'=>"3",
            'cols'=>"40",);

        $step['emails_notified']=array(
            'type'=>'email_notifications',
            'label'=>__('Email notifications',WYSIJA),
            'desc'=>__('Put in the emails of the person who should received all notifications, comma separated.',WYSIJA));

        $step['from_name']=array(
            'type'=>'fromname',
            'class'=>'validate[required]',
            'label'=>__('From name & email',WYSIJA),
            'desc'=>__("The plugin sends automated notifications, but also to your subscribers, like the subscription activation email. Put in the name that should show up in these emails.",WYSIJA));

        /* TODO add for rooster
        $step['sharedata']=array(
            'type'=>'debug',
            'label'=>__('Share your usage information ',WYSIJA),
            'desc'=>__('Help us improve Wysija by sharing information on how you use the plugin and get chance to win a Premium licence. [link]Find out more.[/link]',WYSIJA),
            'link'=>'<a href="http://support.wysija.com/knowledgebase/sharing-your-usage-data/" target="_blank" title="'.__("Find out more.",WYSIJA).'">');
        */

        $modelC=&WYSIJA::get('config','model');




        ?>
        <table class="form-table">
            <tbody>
                <?php
                echo $this->buildMyForm($step,$modelC->values,"config");
                ?>
            </tbody>
        </table>
        <?php
    }


    function emailactiv(){
        $step=array();
        $step['confirm_dbleoptin']=array(
            'type'=>'radio',
            'values'=>array(true=>__("Yes",WYSIJA),false=>__("No",WYSIJA)),
            'label'=>__('Send Activation Email',WYSIJA),
            'desc'=>__('Subscribers will not receive any emails until they activate their subscriptions. Keep this activated to stop fake subscriptions by humans and robots.',WYSIJA).' <a href="http://support.wysija.com/knowledgebase/why-you-should-enforce-email-activation/?utm_source=wpadmin&utm_campaign=activation email" target="_blank">'.__("Read more on support.wysija.com",WYSIJA)."</a>");

        $step['confirm_email_title']=array(
            'type'=>'input',
            'label'=>__('Email subject',WYSIJA),
            'rowclass'=>"confirmemail");

        $step['confirm_email_body']=array(
            'type'=>'textarea',
            'label'=>__('Email content',WYSIJA),
            'rowclass'=>"confirmemail");


        $modelU=&WYSIJA::get("user","model");
        $modelU->getFormat=OBJECT;

        $objUser=$modelU->getOne(false,array('wpuser_id'=>WYSIJA::wp_get_userdata('ID')));
        $step['subscribed_title']=array(
            'type'=>'input',
            'label'=>__('Confirmation page title',WYSIJA),
            'desc'=>__('When subscribers click on the activation link, they are redirected to this [link]confirmation page[/link]',WYSIJA),
            'link'=>'<a href="'.$modelU->getConfirmLink($objUser,"subscribe",false,true).'&demo=1" target="_blank" title="'.__("Preview page",WYSIJA).'">',
            'rowclass'=>"confirmemail");
        $step['subscribed_subtitle']=array(
            'type'=>'input',
            'label'=>__('Confirmation page content',WYSIJA),
            'rowclass'=>"confirmemail");

        ?>

        <table class="form-table">
            <tbody>
                <?php
                echo $this->buildMyForm($step,"","config");

                ?>
            </tbody>
        </table>
        <?php
    }

    function sendingmethod(){
        $key="sending_method";
        $realvalue=$this->model->getValue($key);
        $formsHelp=&WYSIJA::get("forms","helper");
        ?>
        <table class="form-table">
            <tbody>

                <tr class="methods">
                    <th scope="row">
                        <?php
                            $checked=false;
                            $value="site";
                            $id=str_replace("_",'-',$key).'-'.$value;
                            if($value ==$realvalue) $checked=true;
                            $field='<label for="'.$id.'" class="clearfix">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.='<h3>'.__('Your own website',WYSIJA).'</h3></label>';
                            $field.='<p>'.__('The simplest of all solutions for small lists. Your host sets the limit of emails per day.',WYSIJA).'</p>';
                            echo $field;
                        ?>
                    </th>
                    <th scope="row">
                        <?php
                            $checked=false;
                            $value="gmail";
                            $id=str_replace("_",'-',$key).'-'.$value;
                            if($value ==$realvalue) $checked=true;
                            $field='<label for="'.$id.'" class="clearfix">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.='<h3>Gmail</h3></label>';
                            $field.='<p>'.__("Easy to setup. Limited to 500 emails a day. We recommend that you open a dedicated Gmail account for this purpose.",WYSIJA).'</p>';
                            echo $field;
                        ?>
                    </th>
                    <th scope="row">
                        <?php
                            $checked = false;
                            $value = 'smtp';
                            if($value === $realvalue) $checked = true;

                            $id = str_replace('_', '-', $key).'-'.$value;
                            $field ='<label for="'.$id.'" class="clearfix">';
                            $field.= $formsHelp->radio(array('id' => $id, 'name' => 'wysija[config]['.$key.']'), $value, $checked);
                            $field.= '<h3>'.__('SMTP',WYSIJA).'</h3></label>';
                            $field.='<p>'.__('Perfect for sending with a professional SMTP provider, which we highly recommended for big and small lists. We negotiated promotional offers with a few providers for you.',WYSIJA).' <a href="http://support.wysija.com/knowledgebase/send-with-smtp-when-using-a-professional-sending-provider/?utm_source=wpadmin&utm_campaign=sending method" target="_blank">'.__('Read more on support.wysija.com',WYSIJA).'</a></p>';
                            echo $field;
                        ?>
                    </th>

                    <td>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-site">
                    <th scope="row">
                        <?php
                            $field=__('Delivery method',WYSIJA);
                            $field.='<p class="description">'.__('Send yourself some test emails to confirm which method works with your server.',WYSIJA).'</p>';
                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php
                            $key="sending_emails_site_method";
                            $checked=false;
                            $realvalue=$this->model->getValue($key);
                            $value="phpmail";
                            if($value ==$realvalue) $checked=true;

                            $id=str_replace("_",'-',$key).'-'.$value;
                            $field='<p class="title"><label for="'.$id.'">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.='PHP Mail</label><a class="button-secondary" id="send-test-mail-phpmail">'.__('Send a test mail',WYSIJA).'</a></p>';
                            $field.='<p class="description">'.__('This email engine works on 95% of servers',WYSIJA).'</p>';


                            $value="sendmail";
                            $checked=false;
                            if($value ==$realvalue) $checked=true;

                            $id=str_replace("_",'-',$key).'-'.$value;
                            $field.='<p class="title"><label for="'.$id.'">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.='Sendmail</label>
                                <a class="button-secondary" id="send-test-mail-sendmail">'.__('Send a test mail',WYSIJA).'</a></p>';
                            $field.='<p class="description">'.__('This method works on 5% of servers',WYSIJA).'</p>';

                            $id=str_replace("_",'-',$key).'-'.$value."-path";
                            $field.='<p class="title" id="p-'.$id.'"><label for="'.$id.'">';
                            $field.=__("Sendmail path",WYSIJA).'</label>'.$formsHelp->input(array("id"=>$id,'name'=>'wysija[config][sendmail_path]'),$this->model->getValue("sendmail_path")).'</p>';

                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp">
                    <th scope="row">
                        <?php
                            $key="smtp_host";
                            $id=str_replace("_",'-',$key);
                            $field='<label for="'.$id.'">'.__('SMTP Hostname',WYSIJA)."</label>";
                            $field.="<p class='description'>e.g.:smtp.mydomain.com</p>";
                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php
                            $value=$this->model->getValue($key);
                            $field=$formsHelp->input(array("id"=>$id,'name'=>'wysija[config]['.$key.']','size'=>'40'),$value,$checked);
                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp choice-sending-method-gmail">
                    <th scope="row">
                        <?php
                            $key="smtp_login";
                            $id=str_replace("_",'-',$key);
                            $field='<label for="'.$id.'">'.__('Login',WYSIJA)."</label>";

                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php
                            $value=$this->model->getValue($key);
                            $field=$formsHelp->input(array("id"=>$id,'name'=>'wysija[config]['.$key.']','size'=>'40'),$value,$checked);
                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp choice-sending-method-gmail">
                    <th scope="row">
                        <?php
                            $key="smtp_password";
                            $id=str_replace("_",'-',$key);
                            $field='<label for="'.$id.'">'.__('Password',WYSIJA)."</label>";
                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php
                            $value=$this->model->getValue($key);
                            $field=$formsHelp->input(array("type"=>"password","id"=>$id,'name'=>'wysija[config]['.$key.']','size'=>'40'),$value,$checked);
                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp">
                    <th scope="row">
                        <?php
                            $key="smtp_port";
                            $id=str_replace("_",'-',$key);
                            $field='<label for="'.$id.'">'.__('SMTP port',WYSIJA)."</label>";

                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php
                            $value=$this->model->getValue($key);
                            $field=$formsHelp->input(array("id"=>$id,'name'=>'wysija[config]['.$key.']','size'=>'40'),$value,$checked);
                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp">
                    <th scope="row">
                        <?php
                            $key="smtp_secure";
                            $id=str_replace("_",'-',$key);
                            $field='<label for="'.$id.'">'.__('Secure connection',WYSIJA)."</label>";
                            echo $field;
                        ?>
                    </th>
                    <td colspan="2">
                        <?php

                            $value=$this->model->getValue($key);

                            $field=$formsHelp->dropdown(array("name"=>'wysija[config]['.$key.']',"id"=>$id),array(false=>__("No"),"ssl"=>"SSL","tls"=>"TLS"),$value);
                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp">
                    <th scope="row">
                        <?php
                            $field=__('Authentication',WYSIJA);
                            echo $field.'<p class="description">'.__("Leave this option to Yes. Only a tiny portion of SMTP services ask Authentication to be turned off.",WYSIJA).'</p>';
                        ?>
                    </th>
                    <td colspan="2">
                        <?php

                            $key="smtp_auth";
                            $realvalue=$this->model->getValue($key);

                            $value=false;
                            $checked=false;
                            if($value ==$realvalue) $checked=true;
                            $id=str_replace("_",'-',$key).'-'.$value;
                            $field='<label for="'.$id.'">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.=__('No',WYSIJA).'</label>';

                            $value=true;
                            $checked=false;
                            if($value ==$realvalue) $checked=true;
                            $id=str_replace("_",'-',$key).'-'.$value;
                            $field.='<label for="'.$id.'">';
                            $field.=$formsHelp->radio(array("id"=>$id,'name'=>'wysija[config]['.$key.']'),$value,$checked);
                            $field.=__('Yes',WYSIJA).'</label>';



                            /*$key2=$key."_login";
                            $value=$this->model->getValue($key2);
                            $id=str_replace("_",'-',$key2).'-'.$value;
                            $field.="<p>".$formsHelp->input(array("default"=>__("Username",WYSIJA),"id"=>$id,'name'=>'wysija[config]['.$key2.']','size'=>'40'),$value,$checked)."</p>";

                            $key2=$key."_pass";
                            $value=$this->model->getValue($key2);
                            $id=str_replace("_",'-',$key2).'-'.$value;
                            $field.="<p>".$formsHelp->input(array("default"=>__("Password",WYSIJA),"id"=>$id,'name'=>'wysija[config]['.$key2.']','size'=>'40'),$value,$checked)."</p>";*/

                            echo $field;
                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp choice-sending-method-gmail">
                    <th scope="row">
                        <a class="button-secondary" id="send-test-mail-smtp"><?php _e("Send a test mail",WYSIJA)?></a>
                    </th>
                    <td colspan="2">
                        <?php

                        ?>
                    </td>
                </tr>

                <tr class="hidechoice choice-sending-method-smtp choice-sending-method-site choice-sending-method-gmail">
                    <th scope="row">
                        <?php
                            $field=__('Send...',WYSIJA);

                            echo $field.'<p class="description">'.str_replace(array('[link]','[/link]'),array('<a href="http://support.wysija.com/knowledgebase/wp-cron-batch-emails-sending-frequency/" target="_blank">','</a>'),__('Your web host\'s has limits. We suggest 70 emails per 15 minutes to be safe. [link]Find out more[/link] on support.wysija.com',WYSIJA)).'</p>';
                        ?>
                    </th>
                    <td colspan="2">

                        <?php
                            $name='sending_emails_number';
                            $id=str_replace('_','-',$name);
                            $value=$this->model->getValue($name);
                            $params=array("id"=>$id,'name'=>'wysija[config]['.$name.']','size'=>'6');
                            //if($this->model->getValue("smtp_host")=="smtp.gmail.com") $params["readonly"]="readonly";
                            $field=$formsHelp->input($params,$value);
                            $field.= '&nbsp;'.__('emails every',WYSIJA).'&nbsp;';


                            $name='sending_emails_each';
                            $id=str_replace('_','-',$name);
                            $value=$this->model->getValue($name);
                            $field.=$formsHelp->dropdown(array('name'=>'wysija[config]['.$name.']','id'=>$id),$formsHelp->eachValues,$value);
                            $field.='<span class="choice-under15"><b>'.__('This is fast!',WYSIJA).'</b> '.str_replace(array('[link]','[/link]'),array('<a href="http://support.wysija.com/knowledgebase/wp-cron-batch-emails-sending-frequency/?utm_source=wpadmin&utm_campaign=cron" target="_blank">','</a>'),__('We suggest you setup a cron job. [link]Read more[/link] on support.wysija.com',WYSIJA)).'</span>';
                            echo $field;


                        ?>
                    </td>
                </tr>

            </tbody>
        </table>
        <?php
    }

    function bounce() {
        $intro = '<div class="intro">';
        $intro.= '<p><h3>'.__('How does it work?',WYSIJA).'</h3></p>';
        $intro.= '<ol>';
        $intro.= '  <li>'.__('Create an email account dedicated solely to bounce handling, like on Gmail or your own domain.',WYSIJA).'</li>';
        $intro.= '  <li>'.__('Fill out the form below so we can connect to it.',WYSIJA).'</li>';
        $intro.= '  <li>'.__('Take it easy, the plugin does the rest.',WYSIJA).'</li>';
        $intro.= '</ol>';
        $intro.= '<p class="description">'.__('Need help?',WYSIJA).' '.str_replace(array('[link]', '[/link]'), array('<a href="http://support.wysija.com/knowledgebase/automated-bounce-handling-install-guide/">', '</a>'), __('Check out [link]our guide[/link] on how to fill out the form.', WYSIJA)).'</p>';
        $intro.= '</div>';

        echo $intro;
?>
        <div id="innertabs">
            <?php $this->innertabs(); ?>

            <div id="connection" class="wysija-innerpanel">
                <?php $this->connection(); ?>
            </div>
            <div id="actions" class="wysija-innerpanel">
                <p class="description"><?php echo __('There are plenty of reasons for bounces. Configure what to do in each scenario.',WYSIJA)?></p>
                <div id="bounce-msg-error"></div>

                <?php $this->rules(); ?>
            </div>
        </div>
<?php
    }

    function connection(){
        $step=array();
        $step['bounce_email']=array(
            'type'=>'input',
            'label'=>__('Bounce Email',WYSIJA));

        $step['bounce_host']=array(
            'type'=>'input',
            'label'=>__('Hostname',WYSIJA));

        $step['bounce_login']=array(
            'type'=>'input',
            'label'=>__('Login',WYSIJA));
        $step['bounce_password']=array(
            'type'=>'password',
            'label'=>__('Password',WYSIJA));
        $step['bounce_port']=array(
            'type'=>'input',
            'label'=>__('Port',WYSIJA),
            'size'=>"4",
            'style'=>"width:10px;");
        $step['bounce_connection_method']=array(
            'type'=>'dropdown',
            'values'=>array("pop3"=>"POP3","imap"=>"IMAP","pear"=>__("POP3 without imap extension",WYSIJA),"nntp"=>"NNTP"),
            'label'=>__('Connection method',WYSIJA));
        $step['bounce_connection_secure']=array(
            'type'=>'radio',
            'values'=>array(""=>__("No",WYSIJA),"ssl"=>__("Yes",WYSIJA)),
            'label'=>__('Secure connection(SSL)',WYSIJA));
        $step['bounce_selfsigned']=array(
            'type'=>'selfsigned',
            'label'=>__('Self-signed certificates',WYSIJA));


        $step2=array();
        $valuesDDP=array("unsub"=>__("Unsubscribe the user",WYSIJA),"del"=>__("Delete the subscriber",WYSIJA), "not"=>__("Do nothing",WYSIJA));
        $step2['bounce_email_notexists']=array(
            'type'=>'dropdown',
            'values'=>$valuesDDP,
            'label'=>__('When email does not exist... ',WYSIJA));

        $step2['bounce_inbox_full']=array(
            'type'=>'dropdown',
            'values'=>$valuesDDP,
            'label'=>__('When mailbox full...',WYSIJA));

        ?>
        <table class="form-table">
            <tbody>
                <?php

                echo $this->buildMyForm($step,"","config");

                $name='bouncing_emails_each';
                $id=str_replace('_','-',$name);
                $value=$this->model->getValue($name);
                $formsHelp=&WYSIJA::get("forms","helper");
                $field=$formsHelp->dropdown(array("name"=>'wysija[config]['.$name.']',"id"=>$id),
                        array("fifteen_min"=> __("15 minutes",WYSIJA),
                            "thirty_min"=> __("30 minutes",WYSIJA),
                            "hourly"=> __("1 hour",WYSIJA),
                            "two_hours"=> __("2 hours",WYSIJA),
                            "twicedaily"=> __("Twice daily",WYSIJA),
                            "daily"=> __("Day",WYSIJA)),
                        $value);
                $checked="";
                if($this->model->getValue("bounce_process_auto")) $checked='checked="checked"';
                echo '<tr><td><label for="bounce-process-auto"><input type="checkbox" '.$checked.' id="bounce-process-auto" value="1" name="wysija[config][bounce_process_auto]" />
                    '.__("Process bounce automatically",WYSIJA).'</label></td><td id="bounce-frequency"><label for="'.$id.'">'.__("each",WYSIJA)."</label> ".$field.'</td></tr>';
                /*try to connect button*/
                echo '<tr><td><a class="button-secondary" id="bounce-connector">'.__("Does it work? Try to connect.",WYSIJA).'</a></td><td></td></tr>';


                ?>
            </tbody>
        </table>
        <?php
    }

    function log(){
        dbg(get_option('wysija_log'),0);
    }

     function rules(){

         $helpRules=&WYSIJA::get("rules","helper");
         $rules=$helpRules->getRules(false,true);

        $modelList=&WYSIJA::get("list","model");
        /* get lists which have users  and are enabled */
        $query="SELECT * FROM [wysija]list WHERE is_enabled>0";
        $arrayList=$modelList->query("get_res",$query);
        $step2=array();
        $valuesDDP=array(""=>__("Do nothing",WYSIJA),"delete"=>__("Delete the user",WYSIJA),"unsub"=>__("Unsubscribe the user",WYSIJA));
        foreach($arrayList as $list){
            $valuesDDP["unsub_".$list['list_id']]=sprintf(__('Unsubscribe the user and add him to the list "%1$s" ',WYSIJA),$list['name']);
        }

        foreach($rules as $rule){
                if(isset($rule['behave'])) continue;
                $label=$rule['title'];
                if(isset($rule['action_user_min']) && $rule['action_user_min']>0){
                    $label.=' '.sprintf(_n('after %1$s try', 'after %1$s tries', $rule['action_user_min'],WYSIJA),$rule['action_user_min']);
                }
                $step2['bounce_rule_'.$rule['key']]=array(
                'type'=>'dropdown',
                'values'=>$valuesDDP,
                'label'=>$label);
                if(isset($rule['action_user'])){
                    $step2['bounce_rule_'.$rule['key']]['default']=$rule['action_user'];
                }
                if(isset($rule['forward'])){
                    $step2['bounce_rule_'.$rule['key']]['forward']=$rule['forward'];
                }

        }


        $formFields="<ol>";$i=0;
        $formHelp=&WYSIJA::get("forms","helper");
        foreach($step2 as $row =>$colparams){

            $formFields.='<li>';
            $value=$this->model->getValue($row);
            if(!$value && isset($colparams['default'])) $value=$colparams['default'];

            if(isset($colparams['label'])) $label=$colparams['label'];
            else  $label=ucfirst($row);
            $desc='';
            if(isset($colparams['desc'])) $desc='<p class="description">'.$colparams['desc'].'</p>';
            $formFields.='<label for="'.$row.'">'.$label.$desc.' </label>';



            if(isset($colparams['forward'])){
                $valueforward=$this->model->getValue($row."_forwardto");
                if($valueforward===false) {
                    $modelU=&WYSIJA::get("user","model");
                    $modelU->getFormat=OBJECT;

                    $datauser=$modelU->getOne(false,array('wpuser_id'=>WYSIJA::wp_get_userdata('ID')));

                    $valueforward=$datauser->email;
                }

                $formFields.='<input  id="'.$row.'" size="30" type="text" class="bounce-forward-email" name="wysija[config]['.$row."_forwardto".']" value="'.$valueforward.'" />';
            }else{

                $formFields.=$formHelp->dropdown(array('id'=>$row, 'name'=>'wysija[config]['.$row.']'), $colparams['values'], $value, '');
            }

            $i++;
            $formFields.='</li>';
        }
        $formFields.="</ol>";
        echo $formFields;

    }

    function advanced(){

        $step=array();

        $step['role_campaign']=array(
            'type'=>'capabilities',
            '1col'=>1);

        $step['replyto_name']=array(
            'type'=>'fromname',
            'class'=>'validate[required]',
            'label'=>__('Reply-to name & email',WYSIJA),
            'desc'=>__('You can change the default reply-to name and email for your newsletters. This option is also used for the activation emails and Admin notifications (in Basics).',WYSIJA));


        $config=&WYSIJA::get("config","model");


        if(!$config->getValue("premium_key")){
            $step['bounce_email']=array(
            'type'=>'input',
            'label'=>__('Bounce Email',WYSIJA),
            "desc"=>__('To which address should all the bounced emails go? Get the [link]Premium version[/link] to automatically handle these.',WYSIJA),
            'link'=>'<a class="premium-tab" href="javascript:;" title="'.__("Purchase the premium version.",WYSIJA).'">');
        }



        $modelU=&WYSIJA::get('user','model');
        $objUser=$modelU->getCurrentSubscriber();

        $step['viewinbrowser']=array(
            'type'=>'viewinbrowser',
            'label'=>__('Link to browser version',WYSIJA),
            'desc'=>__('Displays at the top of your newsletters. Don\'t forget to include the link tag, ie: [link]The link[/link]',WYSIJA),
            );

        $step['unsubscribe_linkname']=array(
            'type'=>'input',
            'label'=>__('Text of "Unsubscribe" link',WYSIJA),
            'desc'=>__('This changes the label for the unsubscribe link in the footer of your newsletters.',WYSIJA));

        $step['unsubscribed_title']=array(
            'type'=>'input',
            'label'=>__('Unsubscribe page title',WYSIJA),
            'desc'=>__('This is the [link]unsubscription confirmation[/link] page a user is directed to after clicking on the unsubscribe link at the bottom of a newsletter.',WYSIJA),
            'link'=>'<a href="'.$modelU->getConfirmLink($objUser,"unsubscribe",false,true).'&demo=1" target="_blank" title="'.__("Preview page",WYSIJA).'">');


        $step['unsubscribed_subtitle']=array(
            'type'=>'input',
            'label'=>__('Unsubscribe page content',WYSIJA));


        $step['manage_subscriptions']=array(
        'type'=>'viewinbrowser',
        'label'=>__('Subscribers can edit their profile',WYSIJA),
        'desc'=>__('Add a link in the footer of all your newsletters so subscribers can edit their profile and lists. [link]See your own subscriber profile page.[/link]',WYSIJA),
        'link'=>'<a href="'.$modelU->getConfirmLink($objUser,"subscriptions",false,true).'" target="_blank" title="'.__("Preview page",WYSIJA).'">',);


        $step['advanced_charset']=array(
            'type'=>'dropdown_keyval',
            'values'=>array('UTF-8','UTF-7',
                'BIG5',
                "ISO-8859-1","ISO-8859-2","ISO-8859-3","ISO-8859-4","ISO-8859-5","ISO-8859-6","ISO-8859-7","ISO-8859-8","ISO-8859-9","ISO-8859-10","ISO-8859-13","ISO-8859-14","ISO-8859-15",
                'Windows-1251','Windows-1252'),
            'label'=>__('Charset',WYSIJA),
            'desc'=>__('Squares or weird characters are displayed in your emails? Select the encoding for your language.',WYSIJA));
        if($config->getValue("premium_key")){
            $step['dkim']=array(
            'type'=>'dkim',
            'label'=>__('DKIM signature',WYSIJA),
            'desc'=>__('Spam filters like this. Wysija can sign all your emails with DKIM. Spam filters then check if your signature matches the one on your domain.',WYSIJA));
        }

        $step['debug_new']=array(
            'type'=>'debugnew',
            'label'=>__('Debug mode',WYSIJA),
            'desc'=>__("Enable this to show Wysija's errors. Our support might ask you to enable this if you seek their help.",WYSIJA));
        ?>
        <table class="form-table">
            <tbody>
                <?php
                echo $this->buildMyForm($step,"","config");

                    ?>
                    <tr><th scope="row">
                        <div class="label"><?php _e("Reinstall from scratch",WYSIJA)?>
                        <p class="description"><?php _e("Want to start all over again? This will wipe out Wysija and reinstall anew.",WYSIJA)?></p>
                        </div>
                    </th><td><p><a class="button" href="admin.php?page=wysija_config&action=reinstall"><?php _e('Reinstall now...',WYSIJA); ?></a></p></td></tr>


            </tbody>
        </table>
        <?php
    }

    function premium(){
       $helperLicence=&WYSIJA::get("licence","helper");
       $urlpremium="http://www.wysija.com/?wysijap=checkout&wysijashop-page=1&controller=orders&action=checkout&wysijadomain=".$helperLicence->getDomainInfo()."&nc=1&utm_source=wpadmin&utm_campaign=purchasebutton";

       $arrayPremiumBullets=array(
           'more2000'=>array(
               'title'=>__('Send to more than 2000 subscribers.',WYSIJA),
               'desc'=>__('You have no more limits. Send to 100 000 if you want.',WYSIJA)
               ),
           'linksstats'=>array(
               'title'=>__('Find out which links are clicked.',WYSIJA),
               'desc'=>__('This is the most important engagement metric. You\'ll get hooked.',WYSIJA)
               ),
           'advlinkstats'=>array(
               'title'=>__('Track clicked links for each subscriber.',WYSIJA),
               'desc'=>__('Find out who is really addicted to your newsletters.',WYSIJA)
               ),
           'trackga'=>array(
               'title'=>__('Track with Google Analytics.',WYSIJA),
               'desc'=>__('Find out what your subscribers do once on your site.',WYSIJA)
               ),
           'cron'=>array(
               'title'=>__('We activate a cron job for you.',WYSIJA),
               'desc'=>__('We make sure you\'re sending every 15 minutes to avoid unregular delivery.',WYSIJA)
               ),
           'bounces'=>array(
               'title'=>__('Let us handle your bounces.',WYSIJA),
               'desc'=>__('It\'s bad to send to invalid addresses. Wysija removes them for you. Your reputation stays clean.',WYSIJA)
               ),
           'themes'=>array(
               'title'=>__('Download more beautiful themes.',WYSIJA),
               'desc'=>__('We work with top notch designers. The latest and prettiest are exclusive.',WYSIJA)
               ),
           'support'=>array(
               'title'=>__('Fast and efficient support.',WYSIJA),
               'desc'=>__('It\'s like a valet service from the engineers themselves: Ben, Jo and Kim.',WYSIJA)
               ),
           'dkim'=>array(
               'title'=>__('Increase your deliverability with DKIM.',WYSIJA),
               'desc'=>__('Add this signature to your emails with Wysija. Spam filters can then authenticate your emails and your domain.',WYSIJA)
               ),
           'install'=>array(
               'title'=>__('Upgrade in a few clicks.',WYSIJA),
               'desc'=>__('You don\'t need to reinstall. We\'ll simply activate your site.',WYSIJA)
               ),
           'happy'=>array(
               'title'=>__('Join our happy users.',WYSIJA),
               'desc'=>__('Wysija is getting better everyday thanks to users like you. <br />Read [link]what they are saying[/link].',WYSIJA),
               'link'=>'http://www.wysija.com/youre-the-best-newsletter-plugin-for-wordpress-ever/?utm_source=wpadmin&utm_campaign=premiumtab'
               ),
           'trynow'=>array(
               'title'=>__('Try it now. Not happy? Get your money back.',WYSIJA),
               'desc'=>__('30-Day money back guarantee. Good reason to try us out.',WYSIJA)
               ),
       );

        ?>
            <div id="premium-content">
                    <h2><?php echo __('11 Cool Reasons to Upgrade to Premium',WYSIJA)?></h2>
                    <div class="bulletium">
                        <?php
                            foreach($arrayPremiumBullets as $key => $bullet){

                                ?>
                                <div id="<?php echo $key ?>" class="bullet-hold clearfix">
                                    <div class="feat-thumb"></div>
                                    <div class="description">
                                        <h3><?php echo $bullet['title'] ?></h3>
                                        <p><?php
                                        if(isset($bullet['link'])){
                                            echo str_replace(array('[link]','[/link]'),array('<a href="'.$bullet['link'].'" target="_blank">','</a>'),$bullet['desc']);
                                        }else   echo $bullet['desc'] ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            <p class="wysija-premium-wrapper"><a class="wysija-premium-btns wysija-premium" href="<?php echo $urlpremium; ?>" target="_blank"><?php echo __('Upgrade for $99 a year for 1 site.',WYSIJA).'<img src="'.WYSIJA_URL.'img/wpspin_light.gif" alt="loader"/>'; ?></a></p>
            <p><?php echo __('Already paid?', WYSIJA) ?> <a id="premium-activate" type="submit" class="wysija" href="javascript:;" /><?php echo esc_attr(__('Activate your Premium licence.',WYSIJA)); ?></a></p>

            <p><?php echo str_replace(array('[link]','[/link]'),array('<a href="http://www.wysija.com/contact/?utm_source=wpadmin&utm_campaign=premiumtab" target="_blank">','</a>'),__('Got a sales question? [link]Get in touch[/link] with Kim, Jo, Adrien and Ben.',WYSIJA));?></p>
            <p><?php echo str_replace(array('[link]','[/link]'),array('<a href="http://support.wysija.com/terms-conditions/?utm_source=wpadmin&utm_campaign=premiumtab" target="_blank">','</a>'),__('Read our simple and easy [link]terms and conditions.[/link]',WYSIJA));?></p>

        <?php

    }



    function premium_activated(){
       $helperLicence=&WYSIJA::get("licence","helper");
       $urlpremium="http://www.wysija.com/?wysijap=checkout&wysijashop-page=1&controller=orders&action=checkout&wysijadomain=".$helperLicence->getDomainInfo()."&nc=1&utm_source=wpadmin&utm_campaign=purchasebutton";

       $arrayPremiumBullets=array(
           'act-createbounce'=>array(
               'title'=>__('To do: create a bounce address.',WYSIJA),
               'desc'=>__('Click on the Bounce Handling tab and read our guide if you need help.',WYSIJA)."\n".__('Note: Mailjet handles its own bounce.',WYSIJA)
               ),
            'act-dkim'=>array(
               'title'=>__('Set up your DKIM signature.',WYSIJA),
               'desc'=>__('Find the option in the Advanced tab!',WYSIJA)
               ),
           'act-trackga'=>array(
               'title'=>__('Track with Google Analytics.',WYSIJA),
               'desc'=>'<a href="http://support.wysija.com/knowledgebase/track-your-newsletters-visitors-in-google-analytics/?utm_source=wpadmin&utm_campaign=premiumactivated" target="_blank">'.__('See how it works.',WYSIJA).'</a>'
               ),
           'act-findclick'=>array(
               'title'=>__('Find out which links are clicked.',WYSIJA),
               'desc'=>__('This is the most important engagement metric. You\'ll get hooked.',WYSIJA)
               ),
           'act-advclick'=>array(
               'title'=>__('See which links are clicked.',WYSIJA),
               'desc'=>__('Find out if a subscriber is really addicted to your newsletters.',WYSIJA)
               ),
           'act-cron'=>array(
               'title'=>__('We check every 15 min. that your newsletter is being sent.',WYSIJA),
               'desc'=>__('We trigger your sending mecanism every 15 minutes to avoid unregular delivery. Avoid setting up a "cron job".',WYSIJA)
               ),
           'act-sendmor2000'=>array(
               'title'=>__('Send to more than 2000 subscribers.',WYSIJA),
               'desc'=>__('You have no more limits. Send to 100 000 if you want.',WYSIJA)
               ),
           'act-themes'=>array(
               'title'=>__('Download beautiful themes by known designers.',WYSIJA),
               'desc'=>__('Get beautiful themes and their Photoshop files directly in the visual editor.',WYSIJA)
               ),
           'act-finaltip'=>array(
               'title'=>__('Final tip: send with a professional email provider.',WYSIJA),
               'desc'=>__('Wysija highly recommends you send with a professional Email Provider. This will increase your deliverability. [link]Read more.[/link]',WYSIJA),
               'link'=>'http://support.wysija.com/knowledgebase/send-with-smtp-when-using-a-professional-sending-provider/?utm_source=wpadmin&utm_campaign=premiumactivated'
               ),

       );

        ?>
            <div id="premium-content">
                    <h2><?php echo __('The Little Guide to Premium',WYSIJA)?></h2>
                    <div class="bulletium">
                        <?php
                            foreach($arrayPremiumBullets as $key => $bullet){

                                ?>
                                <div id="<?php echo $key ?>" class="bullet-hold clearfix">
                                    <div class="feat-thumb"></div>
                                    <div class="description">
                                        <h3><?php echo $bullet['title'] ?></h3>
                                        <p><?php
                                        if(isset($bullet['link'])){
                                            echo str_replace(array('[link]','[/link]'),array('<a href="'.$bullet['link'].'" target="_blank">','</a>'),$bullet['desc']);
                                        }else   echo $bullet['desc'] ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            <p>
                <a class="wysija-premium-btns wysija-support" href="http://support.wysija.com/?utm_source=wpadmin&utm_campaign=premiumactivated" target="_blank"><?php echo __('Get support',WYSIJA); ?></a>
            </p>

            <p><?php echo str_replace(array('[link]','[/link]'),array('<a href="http://www.wysija.com/contact/?utm_source=wpadmin&utm_campaign=premiumtab" target="_blank">','</a>'),__('Got a sales question? [link]Get in touch[/link] with Kim, Jo, Adrien and Ben.',WYSIJA));?></p>

        <?php

    }

}
