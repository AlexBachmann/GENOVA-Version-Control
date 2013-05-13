<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport('joomla.application.component.view');

class GVersionControlViewRevisions extends JViewLegacy  {
	public $modal = null;
	public function display($tmpl = NULL){
		//First let's check if the user has the neccessary access rights
		$model = $this->getModel();
		if(!$model->checkAccess()){
			throw new RuntimeException(JText::_('COM_GVERSIONCONTROL_NO_ACCESS'));
		}
		JHtml::_('behavior.framework', true);
		JHtml::script('administrator/components/com_gversioncontrol/assets/js/versioncontrol.js');
		JHtml::stylesheet('administrator/components/com_gversioncontrol/assets/css/styles.css');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->users = $this->get('Users');
		$this->actions = $this->get('Actions');
		$this->last_revision = $this->get('LastRevision');
		$this->itemTitle = $this->get('ItemTitle');
		$this->itemLink = $this->get('ItemLink');
		
		$this->addToolbar();
		
		parent::display($tmpl);
	}
	/* Add the page title and toolbar.
	*
	* @since	1.6
	*/
	protected function addToolbar(){
		JToolBarHelper::title(JText::_('COM_VERSIONCONTROL_REVISIONS'), 'article.png');
		if(count($this->items)>1){
			JToolBarHelper::custom('revisions.compare', 'search', 'search-over', 'COM_VERSIONCONTROL_REVISIONS_TB_COMPARE', false);
		}
	}
}