<?php 
class cmsanywhere extends Module {
	function __construct(){
		$this->name = 'cmsanywhere';
		$this->tab = 'front_office_features';
        $this->author = 'Maztch';
		$this->version = '1.1';
        $this->dir = '/modules/cmsanywhere/';
		parent::__construct();
		$this->displayName = $this->l('CMS anywere');
		$this->description = $this->l('Insert CMS content anywhere in your site');
        
		
		$get_hooks = HookCore::getHooks();

		list($v1,$v2,$v3) = explode( '.', _PS_VERSION_ );
		//magic code to add on the fly all the hooks
		
		if ( (int)$v1 == 1 && (int)$v2 == 4 )
		{ //version 1.4.X
			foreach( $get_hooks as $hook ) {
				//ignore the RightColumn hook
				$hook_name = 'hook'.ucwords($hook['name']);
				//if ( $hook_name == 'hookRightColumn' ) { continue; }
				if ( !empty( $hook['position'] ) ) {
				$this->hooks[] = strtolower ('hook'.$hook['name']);
				}
			}
		}
		if ( (int)$v1 == 1 && (int)$v2 == 5 )
		{ //version 1.5.X
			foreach( $get_hooks as $hook ) {
				$test_hook = ( strpos($hook['name'],'display') === 0 )? true : false;
				if ( !empty( $test_hook ) ) {
					$hook_name = str_replace( 'display','hook',$hook['name'] );
					if ( $hook_name == 'hookdisplayHome' ) { continue; }
					 $this->hooks[] = strtolower('hook'.$hook['name']);
				}
			}
		}
	}
    
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
        
	function install(){
        if (parent::install() == false 
	    OR $this->registerHook('home') == false
        OR Configuration::updateValue('cmsanywhere', '0') == false
        ){
            return false;
        }
        return true;
	}
    
	public function getContent(){
	   	$output="";
		if (Tools::isSubmit('module_settings')){            		
			Configuration::updateValue('cmsanywhere', $_POST['cmsanywhere']);                                   
        }	   
        $output.="";
        return $output.$this->displayForm();
	}
    
    public function getCMS($lang){
    	return CMS::listCms($lang);
    }
    

	public function displayForm(){
	    $options="<option>".$this->l('-- SELECT --')."</option>";
	    $idlang = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach (self::getCMS($idlang) AS $k=>$v){
            if (Configuration::get('cmsanywhere')==$v['id_cms']){
                $selected='selected="yes"';
            } else {
                $selected='';
            }
            $options.="<option value=\"".$v['id_cms']."\" $selected>".$v['meta_title']."</option>";
        }
		$form='';
		return $form.'		
		<div style="diplay:block; clear:both; margin-bottom:20px;">
		<h2><img src="'._MODULE_DIR_ .'cmsanywhere/logo.png"> CMS Anywhere</h2>
		</div>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset style="position:relative; margin-bottom:10px;">
            <legend>'.$this->l('Select CMS page').'</legend>
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <label>'.$this->l('CMS Page').':</label>
                    <div class="margin-form" style="text-align:left;" >
                    <select name="cmsanywhere">'.$options.'
                    </select>
                </div>
                                          
                <div style="margin-top:20px; clear:both; overflow:hidden; display:block; text-align:center">
	               <input type="submit" name="module_settings" class="button" value="'.$this->l('save').'">
	            </div>
            </div>
            </fieldset>
		</form>';
	}   
   
	public function __call($method, $args)
	{
		//if hook exists
		if(function_exists($method)) { 
			return call_user_func_array($method, $args);
		}
		
		//check if it is one other hook
		$test_dynamic_hooks = ( in_array( strtolower ($method) , $this->hooks ) )? true : false;
		if ( !empty( $test_dynamic_hooks ) ) {
			return $this->hookdisplayHome($args[0]);   
		}
	}
   
    //////////////////////////////////////
	//the default hook -> hookHome
	/////////////////////////////////////
	function hookdisplayHome($params){
	    if ($this->psversion()==4 || $this->psversion()==3){
            global $cookie;
            $this->context = new StdClass();
            $this->context->cookie=$cookie;
        }
        global $smarty;
        
		$smarty->assign('cms', new CMS(Configuration::get('cmsanywhere'), $this->context->cookie->id_lang));
        return ($this->display(__FILE__, '/cmsanywhere.tpl'));
	}
	
	///////////////////////////////////////////////////////////////////////////
	// needed for prestashop 1.4.9
	// in this version call is ignored by method_exists() on Module::hookExec()
	//////////////////////////////////////////////////////////////////////////
	function hookRightColumn($params){return $this->hookdisplayHome($params);}
	function hookLeftColumn($params){return $this->hookdisplayHome($params);}
	function hookCenter($params){ return $this->hookdisplayHome($params);}
	function hookFooter($params){ return $this->hookdisplayHome($params);}
	function hookHome($params){return $this->hookdisplayHome($params);}
	function hookTop($params){return $this->hookdisplayHome($params);}
	function hookHeader($params){return $this->hookdisplayHome($params);}
	function hookCustomerAccount($params){return $this->hookdisplayHome($params);}
	function hookCreateAccountForm($params){return $this->hookdisplayHome($params);}
	function hookCreateAccount($params){return $this->hookdisplayHome($params);}
	function hookCreateAccountTop($params){return $this->hookdisplayHome($params);}
	function hookAdminCustomers($params){return $this->hookdisplayHome($params);}
	function hookOrderConfirmation($params){return $this->hookdisplayHome($params);}
	function hookUpdateOrderStatus($params){return $this->hookdisplayHome($params);}
	function hookProductFooter($params){return $this->hookdisplayHome($params);}
	function hookPaymentReturn($params){return $this->hookdisplayHome($params);}
	function hookBackBeforePayment($params){return $this->hookdisplayHome($params);}
	function hookShoppingCartExtra($params){return $this->hookdisplayHome($params);}
	function hookPayment($params){return $this->hookdisplayHome($params);}
	function hookCancelProduct($params){return $this->hookdisplayHome($params);}
	function hookNewOrder($params){return $this->hookdisplayHome($params);}
	function hookShoppingCart($params){return $this->hookdisplayHome($params);}
	function hookOrderReturn($params){return $this->hookdisplayHome($params);}
	function hookMyAccountBlock($params){return $this->hookdisplayHome($params);}
	function hookExtraRight($params){return $this->hookdisplayHome($params);}
	function hookExtraLeft($params){return $this->hookdisplayHome($params);}
	function hookAuthentication($params){return $this->hookdisplayHome($params);}
	function hookProductTabContent($params){return $this->hookdisplayHome($params);}
	function hookProductTab($params){return $this->hookdisplayHome($params);}
	function hookProductOutOfStock($params){return $this->hookdisplayHome($params);}
	function hookUpdateQuantity($params){return $this->hookdisplayHome($params);}
	function hookSearch($params){return $this->hookdisplayHome($params);}
	function hookExtraCarrier($params){return $this->hookdisplayHome($params);}
	function hookProductActions($params){return $this->hookdisplayHome($params);}
	///////////////////////////////////////////////////////////////////////////
	// needed for prestashop 1.4.9
	//////////////////////////////////////////////////////////////////////////
}
?>