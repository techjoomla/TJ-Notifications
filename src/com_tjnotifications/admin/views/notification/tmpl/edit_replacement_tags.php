<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
?>

<?php
if (!empty($this->item->replacement_tags))
{
	?>
	<div>
		<div class="alert alert-info">
			<?php echo Text::_('COM_TJNOTIFICATIONS_TAGS_DESC'); ?>
			<br/>
		</div>

		<table class="table table-bordered">
			<thead class="thead-default">
				<tr>
					<th><?php echo Text::_('COM_TJNOTIFICATIONS_REPLACEMENT_TAGS'); ?></th>
					<th><?php echo Text::_('COM_TJNOTIFICATIONS_REPLACEMENT_TAGS_DESC'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach (json_decode($this->item->replacement_tags) as $tags)
				{
					?>
					<tr>
						<td scope="row"><?php echo '{{' . $tags->name . '}}'; ?></td>
						<td><?php echo $tags->description; ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
