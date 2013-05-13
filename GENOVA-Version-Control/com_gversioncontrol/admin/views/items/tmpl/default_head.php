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
$this->columns = 3;
if(!empty($this->items)){
	$item = $this->items[0];
}
?>
<tr>
	<th width="1%">
		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
	</th>
	<?php 
	if(isset($item->state)){
		$this->columns++;
		?>
		<th width="5%">
			<?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
		</th>
		<?php 
	}
	if(isset($item->access_level)){
		$this->columns++;
		?>
		<th width="10%">
			<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
		</th>
		<?php 
	}
	if(isset($item->category_title)){
		$this->columns++;
		?>
		<th width="10%">
			<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
		</th>
		<?php 
	}
	if(isset($item->user_name)){
		$this->columns++;
		?>
		<th width="10%">
			<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'user_name', $listDirn, $listOrder); ?>
		</th>
		<?php 
	}
	if(isset($item->language)){
		$this->columns++;
		?>
		<th width="5%">
			<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
		</th>
		<?php 
	}
	?>
	<th width="1%" class="nowrap">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
	</th>
</tr>