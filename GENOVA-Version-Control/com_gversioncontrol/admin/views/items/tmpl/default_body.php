<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
foreach ($this->items as $i => $item){
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_gversioncontrol&task=revisions.display&item_id='.$item->id);?>">
				<?php echo $this->escape($item->title);?>
			</a>
		</td>
		<?php 
		if($item->state){
		?>
		<td class="center">
			<?php echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', false); ?>
		</td>
		<?php 
		}
		if($item->access_level){
		?>
		<td class="center">
			<?php echo $this->escape($item->access_level);	?>
		</td>
		<?php 
		}	
		if($item->category_title){
		?>
		<td class="center">
			<?php echo $this->escape($item->category_title); ?>
		</td>
		<?php 
		}	
		if($item->user_name){
		?>
		<td class="center">
			<?php 	echo $this->escape($item->user_name);	?>
		</td>
		<?php 
		}
		
		if($item->language){
		?>
		<td class="center">
			<?php 
			if ($item->language=='*'){
				echo JText::alt('JALL', 'language');
			}else{
				echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
			}
			?>
		</td>
		<?php 
		}
		?>
		<td class="center">
			<?php echo (int) $item->id; ?>
		</td>
	</tr>
<?php 
}
	
	