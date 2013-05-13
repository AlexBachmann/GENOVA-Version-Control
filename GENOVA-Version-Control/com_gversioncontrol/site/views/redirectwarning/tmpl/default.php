<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$redirect = $this->state->get('redirect');
?>
<h2 class="modal-title"><?php echo JText::_('COM_VERSIONCONTROL_REDIRECTWARNING_TITLE');?></h2>
<p>
	<?php echo JText::_('COM_VERSIONCONTROL_REDIRECTWARNING_DESC');?>
</p>
<p>
	<div class="button2-left">
		<div class="next">
			<a target="_parent" href="<?php echo $redirect;?>" class="button-primary"><?php echo JText::_('COM_VERSIONCONTROL_REDIRECTWARNING_GO');?></a>
		</div>
	</div>
	<div class="button2-right">
		<div class="prev">
			<a href="#" onClick="window.parent.SqueezeBox.close()" class="button-default"><?php echo JText::_('COM_VERSIONCONTROL_REDIRECTWARNING_CLOSE');?></a>
		</div>
	</div>
</p>