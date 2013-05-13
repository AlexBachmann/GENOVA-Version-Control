<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class GVersionControlViewRedirectWarning extends JViewLegacy {
	public function display($tmpl = null){
		$this->state = $this->get('State');
		parent::display($tmpl);
	}
}