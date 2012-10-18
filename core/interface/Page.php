<?php

/**
 * Page handler object
 * @author John Skrzypek
 * @copyright
 * @todo change the xml accessor to bring back specific parent with nodes
 * @todo change if($initializeRule){ to a wrapper object, that parses the initializeRule script,
 * using the name of the file as the object it's referencing back
 * e.g.: $ordersamples->addTagtoComponent becomes $this->addTagtoComponent
 */

defined('DIRACCESS') or die ('Cannot access this directly');

class Page extends Overloader {
    private $pageID;
	private $pageTemplate;
    private $pageContent;
    private $loginRequired;
    private $adhoc_tags = Array();
	private $template;
	private $modal;
	

    public function __construct($id = 'defaultPage') {
		parent::__construct();
		$this->firephp->info("In Page constructor with page ID: " . $id);
        $this->pageID = $id;
        $this->pageContent = 'Page Information Not found';
        $this->loadByPageID($this->pageID);
		$this->modal = false;
    }

    /**
     * Add 1st level smarty tags to parent template page
     * @return
     * @param object $tag
     * @param object $value
     */

    public function getLoginrequired() {
        if ($this->entity->attributes) {
            if ($this->entity->attributes['RequiredLogin'] == 'Y')
                return true;
        }

        return false;
	}
	
	/**
	 * Specifically for switching a page within the loader
	 */
	
	private function reload($newpage){
		$this->pageID = $newpage;
	    $pages = xmlToArray::getArray(INCLUDES.'/'.Constants::pageInfo, 'page-config', 'pages', 'page');
		$this->pageTemplate = xmlToArray::getNodeDetail($pages, 'pageID', $this->pageID, 'pageTemplate');		
	}

	public function load() {
		$this->firephp->info('load');
		$this->firephp->info(Constants::pageInfo);
	    //- must replace
	    //get page information - to be replaced by db
	    $pages = xmlToArray::getArray(INCLUDES.'/'.Constants::pageInfo, 'page-config', 'pages', 'page');
	    
		$this->pageTemplate = xmlToArray::getNodeDetail($pages, 'pageID', $this->pageID, 'pageTemplate');
		//$this->firephp->info($this->pageTemplate,"Found page template");
		
		$initializeRule = xmlToArray::getNodeDetail($pages, 'pageID', $this->pageID, 'initializeRule');
		//$this->firephp->info($initializeRule,"Found page init rule");
		
		//logic for specific pages and components
		$this->firephp->info($initializeRule);
		if ($initializeRule) {
		    require_once (RULES.'/loaders/'.$initializeRule.'.php');
		}

		$components = xmlToArray::getNodeDetail($pages, 'pageID', $this->pageID, 'componentmapper'); //get components as array		
		//$this->firephp->info($components,"Found components");
			
		//get templates.xml info
		$templates = xmlToArray::getArray(INCLUDES.'/'.Constants::templateInfo, 'template-config', 'templates', 'template');
		$templateFile = xmlToArray::getNodeDetail($templates, 'templateID', $this->pageTemplate, 'fileName'); //find template filename
		$this->template = new Templator($templateFile);

		if(is_array($components) && array_key_exists("componentID",$components['component'])) { //temporary - component object needed
						
			$component = &$components['component'];
			
			if(is_array($this->adhoc_tags) && array_key_exists($component['componentID'], $this->adhoc_tags)){
				 $component['tags'] = $this->adhoc_tags[$component['componentID']];
			}
			//$this->firephp->info($components['component'],__CLASS__ . ":" . __FUNCTION__ . " 111 About to addComponents (single)");
			$this->template->addComponents($components['component']); //components are subtemplates
						
 		} elseif(is_array($components['component'])) {
			foreach($components['component'] as &$component){
				//attach adhoc values to component tags
				
				if(is_array($this->adhoc_tags) && array_key_exists($component['componentID'], $this->adhoc_tags)){
				   $component['tags'] = $this->adhoc_tags[$component['componentID']];	
				}
			}
			//$this->firephp->info($components['component'],__CLASS__ . ":" . __FUNCTION__ . " 2 About to addComponents (additional)");
			$this->template->addComponents($components['component']); //components are subtemplates
		}
		
		//last main tags to add	
		$config = new Configuration(INCLUDES . '/' . Constants::config,'config');
		$title =  $config->__get('Product'); //get current products
		
		if($this->entity->attributes['title']){
			$title = $title . " - " . $this->entity->attributes['title'];
		}

		$this->template->assignTags('title', $title);

		if ($this->template->populate()){
			if($this->modal){
				$this->pageContent = $this->template->display_components(); //send only component html
			} else {
				$this->pageContent = $this->template->getContents(); //send entire page
			}
		}
			
			
	}

	public function display() {

		if (!$this->pageContent) {
	        echo 'No text to display';
	    	return false;
		}
		
		print $this->pageContent;
		 
	}

	public function getDisplay() {
    	if (!$this->pageContent) {
        	return false;
		}
		print '<br/>pageContent: ' . $this->pageContent; 
		return $this->pageContent;
	}

	/**
	 * @todo replace with overloader functions for different loadBy's
	 *
	 */

	public function loadByPageID($pageID) {
	    if (! isset ($this->entity->attributes['id'])) {
	    $dao = get_class($this).'dao';
		$this->firephp->info('Generating new ' .  get_class($this).'dao');
			try {
		    	$this->entity = new $dao($pageID, 'pageID');
			} catch(oeiSampleServerException $e) {
		    	$this->__construct('notfound'); //internal 404 error - send to generic page
		    	
				$alertMessage = new Message('oei_message','message');
				$alertMessage->addMessage(PAGE_NOT_FOUND,Message::BAD);
				$this->addTagtoComponent($alertMessage->getComponentName(),
										 $alertMessage->getTag(),
										 $alertMessage->getMessage());
			}
		}
	}

	/**
	 * adhoc tag assignments to components
	 * @return
	 * @param object $componentID
	 * @param object $tagname
	 * @param object $val
	 */

	public function addTagtoComponent($componentID, $tagname, $val){
	    $this->adhoc_tags[$componentID][$tagname] = $val;
	}
	
	public function forceLogin(){
		$alertMessage = new Message;
		$this->reload('loginPage');
		$alertMessage->addMessage('You must login again - force',Message::BAD);	
		$this->addTagtoComponent('login','alerts', $alertMessage->getMessage());		
	}
	
	public function setModal($state){
		$this->modal = $state;
	}
	

}

?>
