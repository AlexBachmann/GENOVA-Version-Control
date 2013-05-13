<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$allowMerge = ($this->diffRevision->id == $this->lastRevision->id)? true:false;
foreach($this->payloadFields as $field){
?>
<tr>
	<td>
		<?php echo $field['text'];?>
	</td>
	<td>
		<!-- Has something been added or removed? +/- -->
	</td>
	<td>
		<?php echo $this->oldRevision->payload->$field['field'];?>
	</td>
	<?php if($allowMerge){
	?>
	<td>
		<input type="radio" name="merge[<?php echo $field['field'];?>]" value="<?php echo $this->oldRevision->id;?>" />
	</td>
	<td>
		<input type="radio" name="merge[<?php echo $field['field'];?>]" value="<?php echo $this->diffRevision->id;?>" />
	</td>
	<?php 
	}
	?>
	<td>
		<!-- Has something been added or removed? +/- -->
	</td>
	<td>
		<?php echo $this->diffRevision->payload->$field['field'];?>
	</td>
</tr>	
<?php 
}