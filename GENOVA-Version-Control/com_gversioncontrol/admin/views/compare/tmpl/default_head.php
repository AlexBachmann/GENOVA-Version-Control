<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$allowMerge = ($this->diffRevision->id == $this->lastRevision->id)? true:false;
?>
<th class="old-revision-title">
	<?php echo JText::sprintf('COM_VERSIONCONTROL_COMPARE_VERSION_OF_S', JHtml::_('date', $this->oldRevision->time, JText::_('DATE_FORMAT_LC2')))?><br />
	<?php echo JText::sprintf('COM_VERSIONCONTROL_COMPARE_VERSION_BY_S', $this->oldRevision->user->name);?>
	<?php 
	if($this->actions->get('core.edit', false)){
	?>
	<br />
	<br />
	<a href="index.php?option=com_gversioncontrol&task=revisions.restore&id=<?php echo $this->oldRevision->id;?>">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_RESTORE');?>
	</a>
	<?php 
	}
	?>
</th>
<?php 
if($allowMerge){
?>
<th colspan="2"></th>
<?php 
}
?>
<th class="diff-revision-title" style="vertical-align: top;">
	<?php 
		if($allowMerge){
			echo JText::sprintf('COM_VERSIONCONTROL_COMPARE_CURRENT_VERSION_OF_S', JHtml::_('date', $this->diffRevision->time, JText::_('DATE_FORMAT_LC2')));
		}else{
			echo JText::sprintf('COM_VERSIONCONTROL_COMPARE_VERSION_OF_S', JHtml::_('date', $this->diffRevision->time, JText::_('DATE_FORMAT_LC2')));
		}
	?>
	<br />
	<?php echo JText::sprintf('COM_VERSIONCONTROL_COMPARE_VERSION_BY_S', $this->diffRevision->user->name);?>
	<?php 
	if(!$allowMerge && $this->actions->get('core.edit', false)){
	?>
	<br />
	<br />
	<a href="index.php?option=com_gversioncontrol&task=revisions.restore&id=<?php echo $this->diffRevision->id;?>">
		<?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_RESTORE');?>
	</a>
	<?php 
	}
	?>
</th>

