<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport('joomla.application.component.controller');

class GVersionControlControllerRevisions extends JControllerLegacy {
	public function display($cachable = false, $urlparams = false){
		$app = JFactory::getApplication();
		$context = $app->getUserStateFromRequest('com_gversioncontrol.filter.context', 'context');
		$item_id = $app->getUserStateFromRequest('com_gversioncontrol.filter.item_id', 'item_id', 0, 'int');
		if(!$context || !$item_id){
			$this->setRedirect('index.php?option=com_gversioncontrol', JText::_('COM_VERSIONCONTROL_NOTICE_SELECT_CONTEXT'));
			return;
		}
		JRequest::setVar('view','revisions');
		parent::display($cachable, $urlparams);
	}
	public function compare(){
		$app = JFactory::getApplication();
		$context = $app->getUserStateFromRequest('com_gversioncontrol.filter.context', 'context');
		$item_id = $app->getUserStateFromRequest('com_gversioncontrol.filter.item_id', 'item_id', 0, 'int');
		if(!$context || !$item_id){
			$this->setRedirect('index.php?option=com_gversioncontrol', JText::_('COM_VERSIONCONTROL_NOTICE_SELECT_CONTEXT'));
			return;
		}
		$diff_id = JRequest::getInt('diff_id', 0);
		$old_id = JRequest::getInt('old_id', 0);
		if(!$diff_id || !$old_id){
			JError::raise('500', JText::_('COM_VERSIONCONTROL_ERROR_BAD_REQUEST'));
		}
		$config['item_id'] = $item_id;
		$config['context'] = $context;
		$config['diff_id'] = $diff_id;
		$config['old_id'] = $old_id;
		$model = $this->getModel('Revisions', 'GVersionControlModel', $config);
		$viewLayout = JRequest::getCmd('layout', 'default');
		$doc = JFactory::getDocument();
		$view = $this->getView('Compare', $doc->getType(), '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
		$view->setModel($model, true);
		$view->display();
	}
	public function restore(){
		$app = JFactory::getApplication();
		$context = $app->getUserStateFromRequest('com_gversioncontrol.filter.context', 'context');
		$item_id = $app->getUserStateFromRequest('com_gversioncontrol.filter.item_id', 'item_id', 0, 'int');
		$id = JRequest::getInt('id',0);
		if(!$context || !$item_id){
			$this->setRedirect('index.php?option=com_gversioncontrol', JText::_('COM_VERSIONCONTROL_NOTICE_SELECT_CONTEXT'));
			return;
		}
		$config['item_id'] = $item_id;
		$config['context'] = $context;
		$model = $this->getModel('Revisions', 'GVersionControlModel', $config);
		if($model->restore($id)){
			$itemLink = $model->getItemLink();
			$this->setRedirect($itemLink, JText::_('COM_VERSIONCONTROL_REVISIONS_RESTORE_SUCCESS'));
		}else{
			$this->setRedirect('index.php?option=com_gversioncontrol&task=revisions.display', JText::_('COM_VERSIONCONTROL_REVISIONS_RESTORE_FAILURE'), 'warning');
		}
	}
}