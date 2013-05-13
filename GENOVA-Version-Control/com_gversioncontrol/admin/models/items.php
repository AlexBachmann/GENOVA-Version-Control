<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport('joomla.application.component.modellist');

class GVersionControlModelItems extends JModelList {
	protected $contexts = null;
	public function __construct($config){
		if(empty($config['filter_fields'])){
			$config['filter_fields'] = array(
					'id', 
					'title', 
					'catid', 
					'state',
					'access_level',  
					'created', 
					'user_name',
					'category_title',
					'language'
			);
		}
		parent::__construct($config);
		//We need to set the context more global, sice we use the state variables set here
		//in other views as well...
		$this->context = $this->option;
	}
	protected function populateState($ordering = null, $direction = null){
	
		$context = $this->getUserStateFromRequest($this->context.'.filter.context', 'filter_context', null);
		if(is_null($context)){
			//Check if the content plugin is installed and active
			if($this->contextExists('com_content.article')){
				$context = 'com_content.article';
				$app = JFactory::getApplication();
				$app->setUserState($this->context.'.filter.context', $context);
			}
		}
		$this->setState('filter.context', $context);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	
		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
	
		$userId = $this->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id', 0, 'int');
		$this->setState('filter.user_id', $userId);
	
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);
	
		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);
	
	
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
	
		// List state information.
		parent::populateState();
	}
	public function getItems(){
		$context = $this->getState('filter.context');
		if(!$context){
			return array();
		}
		return parent::getItems();
	}
	protected function getListQuery(){
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetListQuery', array($context, $state));
		if(!empty($results)){
			return $results[0];
		}else{
			return null;
		}
	}
	public function getUsers(){
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetUsers', array($context, $state));
		if(!empty($results)){
			return $results[0];
		}else{
			return array();
		}
	}
	public function getContexts(){
		if(!$this->contexts){
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('gversioncontrol');
			
			$this->contexts = $dispatcher->trigger('onGVersionControlGetContext', array());
		}
		
		return $this->contexts;
	}
	public function getFilters(){
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetFilters', array($context, $state));
		if(!empty($results)){
			return $results[0];
		}else{
			return array();
		}
	}
	public function getExtension(){
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetExtension', array($context, $state));
		if(!empty($results)){
			return $results[0];
		}else{
			return null;
		}
	}
	public function getTotal(){
		$context = $this->getState('filter.context');
		if(!$context){
			return 0;
		}
		return parent::getTotal();
	}
	public function contextExists($needle){
		$contexts = $this->getContexts();
		foreach($contexts as $context){
			if($context['value'] == $needle){
				return true;
			}
		}
		return false;
	}
}