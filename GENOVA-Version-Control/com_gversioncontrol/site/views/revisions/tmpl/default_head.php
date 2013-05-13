<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
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
	<?php 
		if($this->actions->get('core.edit')){
			?>
			<th width="20%">
				<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_ACTIONS');?>
			</th>
			<?php 
		}
	?>
	
</tr>