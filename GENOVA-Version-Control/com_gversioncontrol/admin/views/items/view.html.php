<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class GVersionControlViewItems extends JViewLegacy {
	public function display($tmpl = NULL){
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->users		= $this->get('Users');
		$this->filters 		= $this->get('Filters');
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}
		
		parent::display($tmpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar(){
		$user		= JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_VERSIONCONTROL_ITEMS_TITLE'), 'article.png');
		if ($user->authorize('core.admin', 'com_gversioncontrol')) {
			JToolBarHelper::preferences('com_gversioncontrol');
			JToolBarHelper::divider();
		}
	}
	
}