<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.controller');

/**
 * Version Controller
 */
 
class GVersionControlController extends JControllerLegacy 
{
	protected $default_view = 'revisions';
	
	public function __construct(){
		parent::__construct();
		$this->addModelPath(JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/models', 'GVersionControlModel');
	}
	public function display($cachable = false, $urlparams = false){
		//if there is a controller for this view, then we want to use its display method instead
		$view = JRequest::getVar('view', null);
		if($path = JPath::find(JPATH_COMPONENT.'/controllers/', strtolower($view).'.php')){
			require_once $path;
			$class = 'GVersionControlController'.ucfirst($view);
			$controller = new $class();
			$controller->display($cachable, $urlparams);
			$controller->redirect();
			return;
		}
		parent::display($cachable, $urlparams);
	}	
}

