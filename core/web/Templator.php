<?php

defined('DIRACCESS') or die('Cannot access this directly');
require_once(UTILITIES . '/smarty/libs/Smarty.class.php');

/**
 * @todo error catching
 * @todo replace component arrays with objects
 */
class Templator
{
	private $smartyTemplate;
	private $templateFile;
	private $components;
	private $templateDirectory;
	//private $oei_templateDirectory;
	private $component_out; //placeholder for combined component output
	private $firephp;
	/**
	 * 
	 * @return 
	 * @param string $template template name - without location
	 * @param string $dir location of this template as defined in config.xml
	 */
	public function __construct($template = '',$dir = 'TemplateDirectory'){
		global $g_mobile;
		$this->firephp = FirePHP::getInstance(true);
		
		if($dir == 'oei_Components' && ($g_mobile))
			$dir = 'MobileComponents';
			
		if($dir == 'TemplateDirectory' && ($g_mobile))
			$dir = 'MobileDirectory';
				
		//$this->firephp->info($dir);	
			
			
		
		$this->component_out = '';
		
		global $session;
		
		//$this->firephp->info("Creating a new template called " . $session->get('templateID') . '_' . $template);
		
		$this->templateFile = $template;

		$config = new Configuration(CONFIG . '/' . Constants::config,'config');
		
		$this->templateDirectory = ($g_mobile) ? $config->__get('MobileDirectory') : $config->__get('TemplateDirectory');		
		
		$this->Directory = $config->__get('Directory'); //get sampleserver directory
		$this->smartyTemplate = new Smarty; //initialize smarty
		$this->smartyTemplate->compile_id = $session->get('templateID');
		$this->smartyTemplate->template_dir = ROOT . $config->__get($dir);
		$this->smartyTemplate->compile_dir = UTILITIES . '/smarty/templates_c';
		$this->smartyTemplate->config_dir = UTILITIES . '/smarty/configs';
		
		//set some global variables
		$this->smartyTemplate->assign('domain', DOMAIN);		
		$this->smartyTemplate->assign('webroot', WEBROOT . '/');
		$this->smartyTemplate->assign('html_path', WEBROOT . $this->templateDirectory);
		$this->smartyTemplate->assign('oei_root', WEBROOT . $this->Directory);	
		$this->smartyTemplate->assign('oei_path', WEBROOT . $this->templateDirectory);//$this->oei_templateDirectory);			
		$this->smartyTemplate->assign('loggedin', $session->get('loggedin'));
		
		
		//if an activeruser has been created, register
		if(Session::get('activeuser')){
			//FIX - this needs to assign the entire user object (register_object) not just these single values
			
			//FIX - this is called 3 times even with the null check.  BUG
				if($this->smartyTemplate->get_template_vars('fname') == null ) {
					//$this->firephp->info("assiging fname and lname");
					$activeuser = Session::get('activeuser');
					//$this->firephp->info('setting session_user');
					//$this->firephp->info($activeuser->getAttributes());
					$this->smartyTemplate->assign('session_user',$activeuser->getAttributes());
					$this->smartyTemplate->assign('userid',tokenize($activeuser->getId()));
				}
		}
		
	}
	
	/**
	 * add sub components to parent templates, if any.  assigns to local components
	 * @return 
	 * @param object $components
	 */
	public function addComponents($components){

		//$this->firephp->info($components,__CLASS__ . ":" . __FUNCTION__ . " called with");

		//loads template file info from templates.xml
		$componentInfo = xmlToArray::getArray(CONFIG.'/'.Constants::templateInfo,'template-config','components','component');		
		
		if(array_key_exists('componentID',$components)) { //temporary - component object needed
			foreach($componentInfo as $info){
				if(array_key_exists('componentID',$info) && $info['componentID'] == $components['componentID']){
					$info['htmltag'] = $components['htmltag'];
					if(array_key_exists('tags',$components)) { //looks for component html tags
							$info['tags'] = $components['tags'];	
						}
					$this->components[] = $info;
					
				}
			}
			//$this->firephp->info($this->components,__CLASS__ . ":" . __FUNCTION__ . " Updated (single)");
		} else {
			foreach($components as $component){  //adds additional component properties
			//printArray($component);
				foreach($componentInfo as $info){
			//$this->firephp->info($info,__CLASS__ . ":" . __FUNCTION__ . " info");
				//$this->firephp->info($component,__CLASS__ . ":" . __FUNCTION__ . " component");
					if(array_key_exists('componentID',$info) && $info['componentID'] == $component['componentID']){
						$info['htmltag'] = $component['htmltag'];
						
						if(array_key_exists('tags',$component)) { //looks for component html tags
							$info['tags'] = $component['tags'];
						}
					
						$this->components[] = $info;
					}
					
				}
			}
			//$this->firephp->info($this->components,__CLASS__ . ":" . __FUNCTION__ . " Updated (additional)");
		}
		
		
		//print 'finished local components as ' .
		//printArray($this->components);
		
	}
	
	/**
	 * populates placeholders with mapped values from templates.xml
	 * pass array of 1st level smarty tags to be parsed into the main page
	 * @return 
	 * @param object $file
	 */
	public function populate(){  
			
		//print $this->components;
			
			
		//handle sub components
		if(is_array($this->components)){ //parse and assign child components
			foreach($this->components as $component){
				$this->component_out = "";
				if(is_array($component)){ //multiple components
				//$this->firephp->info($component,__CLASS__ . ":" . __FUNCTION__ . " Multiple Components Found");
					$subtemplate = new Templator($component['fileName'],$component['location']);
					//$this->firephp->info($component['location']);
					//$this->firephp->info($subtemplate,__CLASS__ . ":" . __FUNCTION__ . " subtemplate");
					//handle component tag assignments
					$this->assignComponentTags($subtemplate,$component);
					
					if($subtemplate->populate()) { //parse and assign to parent html tag
						$this->component_out .= $subtemplate->getContents();
					}
					//$this->firephp->info(__CLASS__ . ":" . __FUNCTION__ . " Assigning to " . $component['htmltag'] . " : " . $this->component_out);
					$this->smartyTemplate->assign($component['htmltag'],$this->component_out);
				} else {  //single component territory
					//$this->firephp->info($component,__CLASS__ . ":" . __FUNCTION__ . " Single Components Found");
					$subtemplate = new Templator($this->components['fileName'],$component['location']);
					
					//handle component tag assignments
					$this->assignComponentTags($subtemplate,$this->components);
										
					$this->component_out .= $subtemplate->getContents();
					//$this->firephp->info(__CLASS__ . ":" . __FUNCTION__ . " Assigning to " . $component['htmltag'] . " : " . $this->component_out);
					$this->smartyTemplate->assign($component['htmltag'],$this->component_out);
					break;
				}
			}
			
			//$this->firephp->info($this->component_out,__CLASS__ . ":" . __FUNCTION__ . " Assiging oei_content with: ");
			$this->smartyTemplate->assign('oei_content',$this->component_out);
		} else {
			//$this->firephp->info("Lowest level reached");
		}
		
		return true;
	}
	
	private function assignComponentTags($com_Templator,$com){
		//$this->firephp->info($com, __CLASS__ . ":" . __FUNCTION__ . " Called with ");
		if(is_array($com) && array_key_exists('tags',$com)){
			foreach($com['tags'] as $tag => $value){
				$com_Templator->smartyTemplate->assign($tag,$value);
				//$this->firephp->info("Assigning to " . $tag . " = " . $value);
			}
		} else {
			//$this->firephp->info(__CLASS__ . ":" . __FUNCTION__ . " No tags found ");
		}
	}
	
	public function getContents(){
		global $session;
		//$this->firephp->info($session->get('templateID'));
		return $this->smartyTemplate->fetch($this->templateFile,$session->get('templateID'));
	}
	
	/**
	 * basic passthrough assignment for top level tags
	 * @return 
	 * @param object $tag
	 * @param object $val
	 */
	
	public function assignTags($tag,$val){
		$this->smartyTemplate->assign($tag,$val);
	}
	
	public function display_components(){
		return $this->component_out;
	}
}

?>