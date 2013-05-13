<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$allowMerge = ($this->diffRevision->id == $this->lastRevision->id)? true:false;
//Initialize variables
$field = $this->currentField;
$compareModel = JModel::getInstance('Compare', 'GVersionControlModel');
$compareModel->setOldRevisionText($this->oldRevision->payload->$field['field']);
$compareModel->setDiffRevisionText($this->diffRevision->payload->$field['field']);
$blocks = $compareModel->getDiffBlocks();
$colspan = ($allowMerge)?4:2;
if(!empty($blocks['blocks'])){
?>
<tr>
	<td class="field-title" colspan="<?php echo $colspan;?>">
		<?php echo $field['text'];?>
	</td>
</tr>
<?php
}
foreach($blocks['blocks'] as $i => $block){
	?>
	<tr>
		<td class="oldcolumn <?php echo $block['type'];?>">
		<?php 
		foreach($block['from'] as $line){
			echo $line['text'];
		}
		?>
		</td>
		<?php 
		if($allowMerge){
		?>
		<td>
		
		</td>
		<td>
		
		</td>
		<?php 
		}
		?>
		<td class="diffcolumn <?php echo $block['type'];?>">
		<?php 
		foreach($block['to'] as $line){
			echo $line['text'];
		}
		?>
		</td>
	</tr>
	<?php 
}