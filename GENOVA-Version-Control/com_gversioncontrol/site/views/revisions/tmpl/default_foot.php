<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$colspan = ($this->actions->get('core.edit', false))? '7':'6';
?>
<tr>
	<td colspan="<?php echo $colspan;?>">
		<?php echo $this->pagination->getListFooter(); ?>
	</td>
</tr>