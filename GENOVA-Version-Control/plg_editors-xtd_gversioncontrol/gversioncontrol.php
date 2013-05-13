<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die;

/**
 * Editor Article buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.article
 * @since 1.5
 */
class plgButtonGVersionControl extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name)
	{
		JHtml::_('behavior.modal');

		//Get the link from the plugin
		$option = JRequest::getVar('option', null);
		if(!$option){
			return;
		}
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('gversioncontrol');
		
		$results = $dispatcher->trigger('onGVersionControlGetEditorButtonLink', array($option));
		if(empty($results)){
			return;
		}
		$redirect = $results[0];
		$link = 'index.php?option=com_gversioncontrol&view=redirectwarning&layout=modal&tmpl=component&redirect='.urlencode($redirect);
		
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_EDITOR-XTD_BUTTON_REVISIONS'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
