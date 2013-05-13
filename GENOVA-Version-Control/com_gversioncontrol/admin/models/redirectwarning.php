<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 


class GVersionControlModelRedirectWarning extends JModelLegacy {
	protected function populateState(){
		$redirect = urldecode(JRequest::getVar('redirect'));
		$this->setState('redirect', $redirect);
		parent::populateState();
	}
}