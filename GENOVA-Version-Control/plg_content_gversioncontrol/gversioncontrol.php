<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgContentGVersionControl extends JPlugin{
	
	public function onContentBeforeSave($context, $article, $isNew)
	{
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
	
		// Trigger the onFinderAfterSave event.
		$results = $dispatcher->trigger('onGVersionControlItemBeforeSave', array($context, $article, $isNew));
	}
	public function onContentAfterSave($context, $article, $isNew)
	{
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		if($context == 'com_content.form'){
			$context = 'com_content.article';
		}
	
		// Trigger the onFinderAfterSave event.
		$results = $dispatcher->trigger('onGVersionControlItemAfterSave', array($context, $article, $isNew));
	}
	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0){
		//Check access level
		$accesslevel = $this->params->get('accesslevel');
		$user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		if(!in_array($accesslevel, $levels)){
			return;
		}
		
		$this->loadLanguage();
		// Figure out the name of the text field
		if ( isset($row->text) ) {
			$text_field_name = 'text';
		}
		elseif ( isset($row->fulltext) ) {
			$text_field_name = 'fulltext';
		}
		elseif ( isset($row->introtext) ) {
			$text_field_name = 'introtext';
		}
		else {
			// Unrecognized
			return false;
		}
		
		// In some cases, we know what the text_field_name should be
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		if (isset($row->introtext) AND $option == 'com_content' AND $view == 'category' AND $layout == 'blog')
		{
			$text_field_name = 'introtext';
		}
		if (isset($row->introtext) AND $option == 'com_content' AND $view == 'featured')
		{
			$text_field_name = 'introtext';
		}
		
		$html = '<a class="gversioncontrol revisions-link" href="index.php?option=com_gversioncontrol&view=revisions&context=com_content.article&item_id='.$row->id.'">'.JText::_('PLG_CONTENT_GVERSIONCONTROL_REVISIONS').'</a>';
		
		$row->$text_field_name .= $html;
		return;
	}
}
