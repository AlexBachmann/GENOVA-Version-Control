<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th colspan="4" width="20%">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_COMPARE'); ?>
	</th>
	<th width="20%">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_DATE');?>
	</th>
	<th width="10%">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_USER');?>
	</th>
	<th width="20%">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_COMMENT');?>
	</th>
	<?php 
	if($this->actions->get('core.edit', false)){
		?>
		<th width="20%">
			<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_ACTIONS');?>
		</th>
		<?php 
	}
	?>
</tr>