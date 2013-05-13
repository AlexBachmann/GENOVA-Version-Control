<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.application.component.view');

class GVersionControlViewCompare extends JViewLegacy {
	public function display($tmpl = NULL){
		//First let's check if the user has the neccessary access rights
		$model = $this->getModel();
		if(!$model->checkAccess()){
			throw new RuntimeException(JText::_('COM_GVERSIONCONTROL_NO_ACCESS'));
		}
		JHtml::stylesheet('administrator/components/com_gversioncontrol/assets/css/styles.css');
		$this->oldRevision = $this->get('OldRevision');
		$this->diffRevision = $this->get('DiffRevision');
		$this->lastRevision = $this->get('LastRevision');
		$this->payloadFields = $this->get('PayloadFields');
		$this->actions = $this->get('Actions');
		$this->itemTitle = $this->get('ItemTitle');
		$this->itemLink = $this->get('ItemLink');
		$this->state = $this->get('State');
		
		parent::display($tmpl);
	}
}