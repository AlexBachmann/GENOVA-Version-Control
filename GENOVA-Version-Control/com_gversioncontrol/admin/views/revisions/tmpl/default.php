<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
?>
<h2><?php echo $this->itemTitle;?></h2>
<ul class="gversioncontrol-navigation">
	<li>
		<a class="button-default btn" href="<?php echo $this->itemLink;?>">
			<?php echo JText::_('COM_GVERSIONCONTROL_LINK_CURRENT_VERSION');?>
		</a>
	</li>
</ul>
<div style="clear: both;"></div>

<form action="<?php echo JRoute::_('index.php?option=com_gversioncontrol&view=revisions');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filters');?>
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
		
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder;?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn;?>" />
	<?php echo JHtml::_('form.token');?>
</form>