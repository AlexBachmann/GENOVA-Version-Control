<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
$count = count($this->items); 
foreach ($this->items as $i => $item){
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center" >
			<?php 
			if($item->id != $this->last_revision->id){
			?>
				<a href="index.php?option=com_gversioncontrol&task=revisions.compare&diff_id=<?php echo $this->last_revision->id;?>&old_id=<?php echo $item->id;?><?php echo $this->getUrlExt();?>" class="hasTip" title="<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_LATEST_TIP_TITLE');?>::<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_LATEST_TIP_DESC');?>">
					<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_LATEST');?>
				</a>
			<?php 
			}
			?>
		</td>
		<td>
			<?php 
			if($item->parent_id != 0){
			?>
				<a href="index.php?option=com_gversioncontrol&task=revisions.compare&diff_id=<?php echo $item->id;?>&old_id=<?php echo $item->parent_id;?><?php echo $this->getUrlExt();?>" class="hasTip" title="<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_PREVIOUS_TIP_TITLE');?>::<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_PREVIOUS_TIP_DESC');?>">
					<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_PREVIOUS');?>
				</a>
			<?php 
			}
			?>
		</td>
		<td class="center radio">
			<?php if($count>1){
			?>
			<input type="radio" name="diff_id" class="radio-a radio-a-<?php echo $i;?>" value="<?php echo $item->id;?>" />
			<?php 	
			}?>
			
		</td>
		<td class="center radio">
			<?php if($count>1){
			?>
			<input type="radio" name="old_id" class="radio-b radio-b-<?php echo $i;?>" value="<?php echo $item->id;?>" />
			<?php 	
			}?>
		</td>
		<td class="center">
			<?php echo JHtml::_('date', $item->time, JText::_('DATE_FORMAT_LC2')); ?>
		</td>
		<td class="center">
			<?php echo $item->user_name;?>
		</td>
		<?php 
		if($this->actions->get('core.edit')){
			?>
			<td class="center">
				<?php 
				if(($this->last_revision->id != $item->id) && $this->actions->get('core.edit')){
				?>
				<a href="index.php?option=com_gversioncontrol&task=revisions.restore&id=<?php echo $item->id;?><?php echo $this->getUrlExt();?>">
					<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_RESTORE');?>
				</a>
				<?php 
				}
				?>
			</td>
			<?php 
		}
		?>
		
	</tr>
<?php 
}
	
	