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
	<?php 
	if(in_array('search', $this->filters)){
	?>
	<div class="filter-search fltlft">
		<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

		<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</div>
	<?php 
	}
	?>
	<div class="filter-select fltrt">
		<select name="filter_context" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_GVERSIONCONTROL_SELECT_CONTEXT');?></option>
			<?php echo JHtml::_('select.options', $this->get('Contexts'), 'value', 'text', $this->state->get('filter.context'), true);?>
		</select>
		<?php 
		if(in_array('published', $this->filters)){
		?>
		<select name="filter_published" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
		</select>
		<?php 	
		}
		if(in_array('category_id', $this->filters)){
			$extension = $this->get('Extension');
			if($extension){
			?>
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', $extension), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
			<?php 	
			}
		}
		if(in_array('access', $this->filters)){
		?>
		<select name="filter_access" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
		</select>
		<?php 	
		}
		if(in_array('author_id', $this->filters)){
		?>
		<select name="filter_user_id" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_VERSIONCONTROL_ITEMS_SELECT_USER');?></option>
			<?php echo JHtml::_('select.options', $this->users, 'value', 'text', $this->state->get('filter.user_id'));?>
		</select>
		<?php 	
		}
		if(in_array('language', $this->filters)){
		?>
		<select name="filter_language" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
		</select>
		<?php 	
		}
		?>
	</div>
</fieldset>
<div class="clr"> </div>