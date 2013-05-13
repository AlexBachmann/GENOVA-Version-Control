<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.application.component.model');
JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/models', 'GVersionControlModel');

class plgGVersionControlContent extends JPlugin{
	/*
	 * The context this plugin works with
	 * 
	 * IMPORTANT: You can set the context freely here, but you need 
	 * to be consistent with the context throughout the plugin. That's
	 * why you should specifiy it here and refer to it using $this->context later on
	 */
	protected $context = 'com_content.article';
	
	//The component name of your component
	protected $option = 'com_content';
	
	/**
	 * Returns context of this plugin for context list
	 * 
	 * In the backend component the user can switch between contexts. Here you need to add
	 * the entry of your component's context
	 * @return array 
	 */
	public function onGVersionControlGetContext(){
		$this->loadLanguage();
		return array('value' => $this->context, 'text' => JText::_('PLG_GVERSIONCONTROL_CONTENT_CONTEXT'));
	}
	/**
	 * Returns the link to the current item
	 * 
	 * Sometimes GENOVA Version Control wants to redirect the user to 
	 * the item view of the current revision. This method provides the link
	 * 
	 * @param string $context
	 * @param object $state
	 * @param integer $item_id
	 */
	public function onGVersionControlGetItemLink($context, $state, $item_id){
		if($context == $this->context){
			$app = JFactory::getApplication();
			if($app->isAdmin()){
				return 'index.php?option=com_content&task=article.edit&id='.intval($item_id);
			}elseif($app->isSite()){
				return 'index.php?option=com_content&view=article&id='.intval($item_id);
			}else{
				return 'index.php';
			}
		}
	}
	/**
	 * Returns the title of the currently published version of an item
	 * 
	 * The titles of an item may change over time. This method returns the 
	 * the title of the item of its currentyl published version
	 * 
	 * @param string $context
	 * @param object $state
	 * @param integer $item_id
	 */
	public function onGVErsionControlgetItemTitle($context, $state, $item_id){
		if($context == $this->context){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('title');
			$query->from('#__content');
			$query->where('id = '.$db->quote(intval($item_id)));
			$db->setQuery($query);
			$title = $db->loadResult();
			if($title){
				return $title;
			}
		}
	}
	/**
	 * Returns the view access level of a given item
	 * 
	 * Decides whether or not a user is allowed to see and compare revisions of an item
	 * or not. If you haven't implemented the Joomla View Levels in your component
	 * then you can also return true to allow access to all items to all users or 
	 * false for not allowing any access to all items to all uses.
	 * 
	 * @param string $context
	 * @param object $state
	 * @param integer $item_id
	 * @return int or bolean Access Level of the item or true/false for general access
	 */
	public function onGVersionControlGetAccessLevel($context, $state, $item_id){
		if($context == $this->context){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('access');
			$query->from('#__content');
			$query->where('id = '.$db->quote((int)$item_id));
			$db->setQuery($query);
			$access = $db->loadResult();
			if($access){
				return $access;
			}else{
				return false;
			}
		}
	}
	/**
	 * Creates the List Query for items of this context
	 * 
	 * Gives back a JDatabaseQuery object.
	 * The following columns are needed/optional for the items list. If the 
	 * fields are stored unter different names in you extensions table, please use aliases
	 *   id
	 *   title
	 *   state (optional)
	 *   category_title (optional)
	 *   author_name (optional)
	 *   language (the lang code or * | optional)
	 *   language_title (optional)
	 *   access_level (optional)
	 *   
	 * @param string $context
	 * @param object $state
	 * @return JDatabaseQuery $query
	 */
	public function onGVersionControlGetListQuery($context, $state){
		if($context == $this->context){
			// Create a new query object.
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$user	= JFactory::getUser();
			
			// Select the required fields from the table.
			$query->select('a.*');
			$query->from('#__content AS a');
			
			
			// Join over the language
			$query->select('lang.title AS language_title');
			$query->join('LEFT', $db->quoteName('#__languages').' AS lang ON lang.lang_code = a.language');
			
			// Join over the asset groups.
			$query->select('ag.title AS access_level');
			$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
			
			// Join over the categories.
			$query->select('c.title AS category_title');
			$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
			
			// Join over the users for the author.
			$query->select('u.name AS user_name');
			$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
			
			// Filter by access level.
			if ($access = $state->get('filter.access')) {
				$query->where('a.access = ' . (int) $access);
			}
			
			// Implement View Level Access
			if (!$user->authorise('core.admin'))
			{
				$groups	= implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN ('.$groups.')');
			}
			
			// Filter by published state
			$published = $state->get('filter.published');
			if (is_numeric($published)) {
				$query->where('a.state = ' . (int) $published);
			}
			elseif ($published === '') {
				$query->where('(a.state = 0 OR a.state = 1)');
			}
			
			// Filter by a single or group of categories.
			$baselevel = 1;
			$categoryId = $state->get('filter.category_id');
			if (is_numeric($categoryId)) {
				$cat_tbl = JTable::getInstance('Category', 'JTable');
				$cat_tbl->load($categoryId);
				$rgt = $cat_tbl->rgt;
				$lft = $cat_tbl->lft;
				$baselevel = (int) $cat_tbl->level;
				$query->where('c.lft >= '.(int) $lft);
				$query->where('c.rgt <= '.(int) $rgt);
			}
			elseif (is_array($categoryId)) {
				JArrayHelper::toInteger($categoryId);
				$categoryId = implode(',', $categoryId);
				$query->where('a.catid IN ('.$categoryId.')');
			}
			
			// Filter by author
			$authorId = $state->get('filter.author_id');
			if (is_numeric($authorId)) {
				$type = $state->get('filter.author_id.include', true) ? '= ' : '<>';
				$query->where('a.created_by '.$type.(int) $authorId);
			}
			
			// Filter by search in title.
			$search = $state->get('filter.search');
			if (!empty($search)) {
				if (stripos($search, 'id:') === 0) {
					$query->where('a.id = '.(int) substr($search, 3));
				}
				elseif (stripos($search, 'author:') === 0) {
					$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
					$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
				}
				else {
					$search = $db->Quote('%'.$db->escape($search, true).'%');
					$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
				}
			}
			
			// Filter on the language.
			if ($language = $state->get('filter.language')) {
				$query->where('a.language = '.$db->quote($language));
			}
			
			// Add the list ordering clause.
			$orderCol	= $state->get('list.ordering', 'a.title');
			$orderDirn	= $state->get('list.direction', 'asc');
			if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
				$orderCol = 'c.title '.$orderDirn.', a.ordering';
			}
			//sqlsrv change
			if($orderCol == 'language')
				$orderCol = 'lang.title';
			if($orderCol == 'access_level')
				$orderCol = 'ag.title';
			if($orderCol == 'category_title')
				$orderCol = 'c.title';
			if($orderCol == 'user_name')
				$orderCol = 'u.name';
			$query->order($db->escape($orderCol.' '.$orderDirn));
			
			//group the results
			$query->group('a.id');
			
			// echo nl2br(str_replace('#__','jos_',$query));
			return $query;
		}
	}
	/**
	 * Get a list of filters you want to enable in the list-view
	 * 
	 * This function is triggered before the filters for the item list are created.
	 * Please specify, which filters you would like to enable.
	 * Available filters are array('search', 'author_id', 'published', 'language', 'access')
	 * @param string $context
	 * @param object $state
	 */
	public function onGVersionControlGetFilters($context, $state){
		if($context == $this->context){
			return array('search', 'author_id', 'published', 'language', 'access', 'category_id');
		}
	}
	/**
	 * Returns all users that contributed content to this component
	 * 
	 * If we want to filter items by the user, who created them, we need
	 * a list of all users who actually have contributed content to this component.
	 * @param string $context
	 * @param object $state
	 */
	public function onGVersionControlGetUsers($context, $state){
		if($context == $this->context){
			// Create a new query object.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
		
			// Construct the query
			$query->select('u.id AS value, u.name AS text');
			$query->from('#__users AS u');
			$query->join('INNER', '#__content AS c ON c.created_by = u.id');
			$query->group('u.id, u.name');
			$query->order('u.name');
		
			// Setup the query
			$db->setQuery($query->__toString());
		
			// Return the result
			return $db->loadObjectList();
		}
	}
	/**
	 * Returns the component name 
	 * 
	 * @param string $context
	 * @param object $state
	 * @return string
	 */
	public function onGVersionControlGetExtension($context, $state){
		if($context == $this->context){
			return 'com_content';
		}
	}
	/**
	 * Handles any necessary events before the item is saved
	 * 
	 * Sometimes actions are required before the item is saved. Imagine the item existed
	 * before GENOVA Version Control has been installed. Then no previous revisions of this item
	 * would exist, and the previous revision could not be restored after the save.
	 * If this is the case, we want to add the old version to the revsion stack first.
	 * @param string $context
	 * @param object $item
	 * @param bolean $isNew
	 */
	public function onGVersionControlItemBeforeSave($context, $item, $isNew){
		if($context == $this->context && !$isNew){
			$config = array('context' => $this->context, 'item_id' => $item->id);
			$revisions = JModel::getInstance('Revisions', 'GVersionControlModel', $config);
			if(!$revisions->getLastRevision()){
				//This seems to be the first time version control is used,
				//so we store old version of this item too
				$old_table = JTable::getInstance('Content');
				$old_table->load($item->id);
				$payload = $this->createPayload($old_table);
				$this->loadLanguage();
				$revisions->addRevision($payload, JText::_('PLG_GVERSIONCONTROL_CONTENT_ADD_ITEM_TO_VERSIONCONTROL'));
			}
		}
	}
	/**
	 * Adds a new revision to the revision stack
	 * 
	 * After the items has been stored, we want to add a new revision to the stack.
	 * You DON'T need to check, if anything has changed since the last save. The
	 * Revisions Model does that already for you.
	 * 
	 * @param string $context
	 * @param object $item
	 * @param bolean $isNew
	 */
	public function onGVersionControlItemAfterSave($context, $item, $isNew){
		if($context == $this->context){
			$config = array('context' => $this->context, 'item_id' => $item->id);
			$revisions = JModel::getInstance('Revisions', 'GVersionControlModel', $config);
			$payload = $this->createPayload($item);
			$revisions->addRevision($payload);
		}
	}
	/**
	 * Restores an older version of an item using the corresponding payload data
	 * 
	 * @param string $context
	 * @param object $state
	 * @param object $revision Table Object of the revision that is to be restored
	 * @return boolean
	 */
	public function onGVersionControlRestoreRevision($context, $state, $revision){
		if($context == $this->context){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			// This is component specific. Here we want to restore an article,
			// so our database table is #__content
			$query->update($db->quoteName('#__content'));
			/*
			 * Now we loop through the payload properties and update the corresponding fields
			 * THIS IS WHY it is important that the payload properties have the same name
			 * as the table columns. See createPayload()
			 */ 
			foreach(get_object_vars($revision->payload) as $field => $value){
				$query->set($db->quoteName($field). ' = '.$db->quote($value));
			}
			$query->where('id = ' . $db->quote($revision->item_id));
			$sql = (string)$query;
			$db->setQuery($query);
			$res = $db->query();
			if(!$res){
				return false;
			}
			return true;
		}
	}
	/**
	 * This protected method created the payload object
	 * 
	 * In this method me map the table values to the payload properties
	 * @param unknown $item
	 * @return stdClass
	 */
	protected function createPayload($item){
		//The payload object should be of stdClass
		$payload = new stdClass();
		
		/*
		 * You can add as many properties to the payload as you want
		 * You need to add all data to the payload, that you want to be able
		 * to restore. Up until now it must be textual data.
		 * 
		 * ATTENTION: It is no concidence, that the payload properties
		 * have the same names as the corresponding table properties.
		 * Look at onGVersionControlRestoreRevision() to see why.
		 */
		$payload->title = $item->title;
		$payload->alias = $item->alias;
		$payload->introtext = $item->introtext;
		$payload->fulltext = $item->fulltext;
		
		/*
		 * The payload of a forum entry could look like this:
		 * $payload->subject = $item->subject;
		 * $payload->message = $item->message;
		 */
		
		return $payload;
	}
	/**
	 * Returns a list of fields that are stored in the payload, togehter with its translation
	 * 
	 * @param string $context
	 * @param object $state
	 * @return array An array containing the field names and the lingual translation of each fild in the payload
	 */
	public function onGVersionControlGetPayloadFields($context, $state){
		if($context == $this->context){
			$this->loadLanguage();
			$fields = array(
						array('field' => 'title', 'text' => JText::_('PLG_GVERSIONCONTROL_CONTENT_FIELD_TITLE')),
						array('field' => 'alias', 'text' => JText::_('PLG_GVERSIONCONTROL_CONTENT_FIELD_ALIAS')),
						array('field' => 'introtext', 'text' => JText::_('PLG_GVERSIONCONTROL_CONTENT_FIELD_INTROTEXT')),
						array('field' => 'fulltext', 'text' => JText::_('PLG_GVERSIONCONTROL_CONTENT_FIELD_FULLTEXT'))
					);
			return $fields;
		}
	}
	/**
	 * Returns the Editor-Button Link containing the right context and item_id
	 * 
	 * Since the editor button is executed within the component, we need this plugin
	 * to create a link to the revisions of this component's item. 
	 * @param string $option
	 * @return void|string
	 */
	public function onGVersionControlGetEditorButtonLink($option){
		if($option == $this->option){
			/*
			 * In com_content the item ID (in this case the article ID) is stored in the
			 * 'id' Request variable. In other extensions this might be different
			 */
			$item_id = JRequest::getInt('id', 0);
			if(!$item_id){
				return;
			}
			/*
			 * Now created the link. Communicate the right context for this extension
			 * (in this case 'com_content.article) and the right item_id (in this case the article id)
			 */
			return 'index.php?option=com_gversioncontrol&amp;view=revisions&amp;context='.$this->context.'&amp;item_id='.$item_id;
		}
	}
	/**
	 * Returns the actions the user is allowed to take, within the given context
	 * 
	 * This method returns an actions object, telling GENOVA Version Control whether or not
	 * the user is allowed to make change (edit) the items, for which GENOVA Version Control has
	 * stored revisions. This is important, because without the neccessary edit rights, the user
	 * will not be allowed to restore an old revision for example.
	 * 
	 * @param string $context
	 * @param object $state The state object of the current request
	 * @return JObject
	 */
	public function onGVersionControlGetActions($context, $state){
		if($context == $this->context){
			$user	= JFactory::getUser();
			$result	= new JObject;
				
			//com_content needs the article ID to know, if the user is allowed to edit or not
			$articleId = $state->get('filter.item_id');
			
			//The asset name is component specific. This is the assetname for com_content articles:
			$assetName = 'com_content.article.'.(int) $articleId;	
			/*
			 * GENOVA Version Control only needs the core.edit action
			 * to determine if the user can restore items or not. If
			 * your extension uses other action names, make sure you
			 * still name the property still 'core.edit' in the result (See example below)
			 */
			$action = 'core.edit';
			$result->set($action,	$user->authorise($action, $assetName));
			
			/*
			 * Let's assume your extension doesn't name the edit action
			 * 'core.edit', but it names it 'my_ext.edit' instead.
			 * Then the command would look simmilar like this:
			 * $result->set('core.edit', $user->authorise('my_ext.edit', 'com_myExtension'));
			 */
			
			return $result;
		}
	}
}
