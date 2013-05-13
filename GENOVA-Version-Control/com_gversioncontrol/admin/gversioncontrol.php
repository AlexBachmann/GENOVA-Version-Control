<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted Access');

// Register helper class
JLoader::register('VersionControlHelper', dirname(__FILE__) . '/helpers/versioncontrol.php');

// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('GVersionControl');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
