<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>
<fieldset id="filter-bar">
	<div class="filter-select fltrt">
		<?php 
			if(count($this->items)>1){
			?>
			<button onClick="Joomla.submitform('revisions.compare')" class="button-primary"><?php echo JText::_('COM_VERSIONCONTROL_REVISIONS_TB_COMPARE');?></button>
			<?php 
			}
		?>
		<select name="filter_user_id" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_VERSIONCONTROL_ITEMS_SELECT_USER');?></option>
			<?php echo JHtml::_('select.options', $this->users, 'value', 'text', $this->state->get('filter.user_id'));?>
		</select>
	</div>
</fieldset>
<div class="clr"> </div>