<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>
<h2><?php echo $this->itemTitle;?></h2>
<ul class="gversioncontrol-navigation">
	<li>
		<a class="button-default btn" href="index.php?option=com_gversioncontrol&view=revisions">
			<?php echo JText::_('COM_GVERSIONCONTROL_LINK_REVISIONS');?>
		</a>
	</li>
	<li>
		<a class="button-default btn" href="<?php echo $this->itemLink;?>">
			<?php echo JText::_('COM_GVERSIONCONTROL_LINK_CURRENT_VERSION');?>
		</a>
	</li>
</ul>
<div style="clear: both;"></div>
<table class="adminlist compare">
	<thead>
		<?php echo $this->loadTemplate('head');?>
	</thead>
	<tbody>
		<?php 
		foreach($this->payloadFields as $field){
			$this->currentField = $field;
			echo $this->loadTemplate('fieldcompare');
		}
		?>
	</tbody>
</table>