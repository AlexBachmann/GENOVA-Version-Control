<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport('joomla.application.component.modellist');

class GVersionControlModelRevisions extends JModelList {
	protected $context;
	protected $item_id;
	protected $lastRevision = null;
	protected $diffRevision = null;
	protected $oldRevision = null;
	protected $actions = null;
	protected $access = null;
	protected $itemLink = null;
	protected $itemTitle = null;
	
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
		$this->context = $this->option;
		$this->getState();
		if(isset($config['context'])){
			$this->setState('filter.context', $config['context']);
		}
		if(isset($config['item_id'])){
			$this->setState('filter.item_id', $config['item_id']);
		}
		//We need the context and the item_id for this object to work
		if(!$this->getState('filter.context') || !$this->getState('filter.item_id')){
			throw new RuntimeException(JText::_('COM_VERSIONCONTROL_EXCEPTION_NO_STATE_VARIABLES_GIVEN'));
		}
		if(isset($config['diff_id'])){
			$this->setState('diff_id', $config['diff_id']);
		}
		if(isset($config['old_id'])){
			$this->setState('old_id', $config['old_id']);
		}
		
		$this->addTablePath(JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/tables');
	}
	protected function populateState($ordering = null, $direction = null){
		$context = $this->getUserStateFromRequest($this->context.'.filter.context', 'context');
		$this->setState('filter.context', $context);
		
		$item_id = $this->getUserStateFromRequest($this->context.'.filter.item_id', 'item_id', 0, 'int');
		$this->setState('filter.item_id', $item_id);
		
		$userId = $this->getUserStateFromRequest($this->context.'.revisions.filter.user_id', 'filter_user_id', 0, 'int');
		$this->setState('filter.user_id', $userId);
	
		// List state information.
		parent::populateState();
	}
	public function getLastRevision(){
		if(!$this->lastRevision){
			$query = $this->getListQuery();
			$db = $this->getDbo();
			$db->setQuery($query, 0, 1);
			$res = $db->loadObject();
			if(!$res) return false;
			$table = $this->getTable();
			$table->load($res->id);
			$this->lastRevision = $table;
		}
		return $this->lastRevision;
	}
	public function getDiffRevision(){
		if(!$this->diffRevision){
			if(!$this->getState('diff_id', 0)){
				throw new RuntimeException(JText::_('COM_VERSIONCONTROL_ERROR_BAD_REQUEST'));
			}
			$this->diffRevision = $this->getRevision($this->getState('diff_id', 0));
		}
		return $this->diffRevision;
	}
	public function getOldRevision(){
		if(!$this->oldRevision){
			if(!$this->getState('old_id', 0)){
				throw new RuntimeException(JText::_('COM_VERSIONCONTROL_ERROR_BAD_REQUEST'));
			}
			$this->oldRevision = $this->getRevision($this->getState('old_id', 0));
		}
		return $this->oldRevision;
	}
	public function getRevision($id){
		$table = $this->getTable();
		$table->load($id);
		if($table->context != $this->getState('filter.context') || $table->item_id != $this->getState('filter.item_id')){
			throw new RuntimeException(JText::_('COM_VERSIONCONTROL_ERROR_BAD_REQUEST'));
		}
		return $table;
	}
	public function setLastRevision($table){
		$this->lastRevision = $table;
	}
	public function addRevision($payload, $comment = null){
		$lastRevision = $this->getLastRevision();
		$table = $this->getTable();
		$hash = $table->getPayloadHash($payload);
		//if the last revision is exactly the same as this revision, we skip it
		if($lastRevision && $lastRevision->payload_hash == $hash) return true;
		
		$table->context = $this->getState('filter.context');
		$table->item_id = $this->getState('filter.item_id');
		$table->payload = $payload;
		$table->comment = $comment;
		if($lastRevision){
			$table->parent_id = $lastRevision->id;
		}else{
			$table->parent_id = 0;
		}
		$table->payload_hash = $hash;
		if($table->save()){
			$this->setLastRevision($table);
			return $table;
		}
		return false;
	}
	public function getTable($type = 'Revision', $prefix = 'GVersionControlTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	protected function getListQuery(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('r.*');
		$query->from('#__gvc_revisions as r');
		$query->select('u.name as user_name');
		$query->join('LEFT', '#__users as u ON r.user = u.id');
		$query->where('r.context = '.$db->quote($this->getState('filter.context')));
		$query->where('r.item_id = ' .$db->quote($this->getState('filter.item_id')));
		$query->order('r.time DESC');
		
		return $query;
	}
	public function getUsers(){
		$db = JFactory::getDbo();
		$query = $this->getListQuery();
		$query->group('r.user');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		$list = array();
		foreach($res as $item){
			$list[] = array('value' => $item->user, 'text' => $item->user_name);
		}
		return $list;
	}
	public function getPayloadFields(){
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetPayloadFields', array($context, $state));
		if(!empty($results)){
			return $results[0];
		}else{
			return null;
		}
	}
	public function getItemLink(){
		if(is_null($this->itemLink)){
			$context = $this->getState('filter.context');
			$state = $this->getState();
			$item_id = $this->getState('filter.item_id');
			
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('gversioncontrol');
			
			$results = $dispatcher->trigger('onGVersionControlGetItemLink', array($context, $state, $item_id));
			if(!empty($results)){
				$this->itemLink = $results[0];
			}else{
				$this->itemLink = false;
			}
		}
		return $this->itemLink;
	}
	public function getItemTitle(){
		if(is_null($this->itemTitle)){
			$context = $this->getState('filter.context');
			$state = $this->getState();
			$item_id = $this->getState('filter.item_id');
				
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('gversioncontrol');
				
			$results = $dispatcher->trigger('onGVersionControlGetItemTitle', array($context, $state, $item_id));
			if(!empty($results)){
				$this->itemTitle = $results[0];
			}else{
				$this->itemTitle = false;
			}
		}
		return $this->itemTitle;
	}
	public function getActions(){
		if(is_null($this->actions)){
			$context = $this->getState('filter.context');
			$state = $this->getState();
				
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('gversioncontrol');
				
			$results = $dispatcher->trigger('onGVersionControlGetActions', array($context, $state));
			if(!empty($results)){
				$this->actions = $results[0];
			}else{
				$this->actions = new JObject();
			}
		}
		return $this->actions;			
	}
	public function checkAccess(){
		if(is_null($this->access)){
			$context = $this->getState('filter.context');
			$state = $this->getState();
			$item_id = $this->getState('filter.item_id');
			
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('gversioncontrol');
			
			$results = $dispatcher->trigger('onGVersionControlGetAccessLevel', array($context, $state, $item_id));
			//If  not plugin returns access information, we don't give access
			if(empty($results)){
				$this->access = false;
			}else{
				$result = $results[0];
				//If the plugin returns general information (true or false) we just return this
				if($result === true || $result === false){
					$this->access =  $result;
				}else{
					//Now we have to assume that an access level has been returned by the plugin
					$user = JFactory::getUser();
					$levels = $user->getAuthorisedViewLevels();
					//Check if the access level is within the authorized levels of the user
					if(in_array($result, $levels)){
						$this->access = true;
					}else{
						$this->access = false;
					}
				}
			}
			
		}
		return $this->access;
	}
	public function restore($id){
		//Check if we are authorized to edit this item
		$actions = $this->getActions();
		if(!$actions->get('core.edit', false)){
			throw new RuntimeException('The user is not authorized to restore this item.');
			return false;
		}
		
		//Get the Revision and add it to the top of the revision stack
		$revision = $this->getRevision($id);
		$this->addRevision($revision->payload);
		
		//Now let's update the extensions database entry by calling the plugin
		$context = $this->getState('filter.context');
		$state = $this->getState();
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		//$success is taken by reference by the plugin, changes it to true, if the restore was completed successfully
		$success = false;
		$results = $dispatcher->trigger('onGVersionControlRestoreRevision', array($context, $state, $revision));
		
		if(!empty($results)){
			return $results[0];
		}else{
			return null;
		}
	}
}