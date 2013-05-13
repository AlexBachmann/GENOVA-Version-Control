<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted Access');

// Include dependencies
jimport('joomla.application.component.controller');
$lang = JFactory::getLanguage();
$lang->load('com_gversioncontrol', JPATH_ADMINISTRATOR.'/components/com_gversioncontrol');

$controller = JController::getInstance('GVersionControl');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
