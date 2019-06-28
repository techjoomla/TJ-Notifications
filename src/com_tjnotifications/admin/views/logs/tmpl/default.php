<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.modal', 'a.modal');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$doc = Factory::getDocument();
$doc->addStyleSheet(JUri::root() . 'administrator/components/com_tjnotifications/assets/css/tjnotifcations.css');
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    var width = jQuery(window).width();
    var height = jQuery(window).height();

    //ID of container
    jQuery('.modal notification').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');
});
</script>

<form action="index.php?option=com_tjnotifications&view=logs" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
			<?php
			// Search tools bar
			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => 1,'filtersHidden' => 0)));
			?>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible">
					<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
					<tr>
						<th width="2%" class="">
							<input type="checkbox" name="checkall-toggle" value=""
								title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
								onclick="Joomla.checkAll(this)"/>
						</th>
						<th class="minwidth-150px">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_SUBJECT"), 'subject', $listDirn, $listOrder);?>
						</th>
						<th class="minwidth-150px">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BODY"), 'title', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TO"), 'to', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_CC"), 'cc', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BCC"), 'bcc', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_DATE"), 'date', $listDirn, $listOrder);?>
						</th>

						<th width="" class="minwidth-150px">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_STATE"), 'state', $listDirn, $listOrder);?>
						</th>

						<th width="" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PROVIDER"), 'provider', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_FROM"), 'from', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PARAMS"), 'params', $listDirn, $listOrder);?>
						</th>
						<th width="2%" class="">
							<?php echo JHtml::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_ID"), 'id', $listDirn, $listOrder);?>
						</th>
					</tr>
					</thead>
					<tbody>
						<?php if (!empty($this->items)) : ?>
							<?php foreach ($this->items as $i => $row) :
							?>
								<tr>
									<td class="">
										<?php echo JHtml::_('grid.id', $i, $row->id); ?>
									</td>
									<td class="">
										<?php echo htmlspecialchars($row->subject, ENT_COMPAT, 'UTF-8'); ?>
									</td>
									<td class="">
									<a class="modal notification" href="<?php echo Route::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=body&id='. $row->id); ?>"><?php echo Text::_("COM_TJNOTIFICATIONS_TITLE_VIEW_CONTENTS");?></a>
									</a>
									<td class="">
											<?php echo str_replace(',', '<br />', $row->to); ?>
									</td>
									<td class="">
											<?php echo str_replace(',', '<br />', $row->cc); ?>
									</td>
									<td class="">
											<?php echo str_replace(',', '<br />', $row->bcc); ?>
									</td>
									<td class="">
											<?php echo htmlspecialchars($row->date, ENT_COMPAT, 'UTF-8');?>
									</td>
									<td class="">
											<?php
											if($row->state ==1)
											{
												echo Text::_('COM_TJNOTIFICATIONS_STATE_SENT');
											}
											elseif($row->state ==0)
											{
												echo Text::_('COM_TJNOTIFICATIONS_STATE_FAILED');
											}
											?>
									</td>
									<td class="">
											<?php echo htmlspecialchars($row->key, ENT_COMPAT, 'UTF-8');?>
									</td>

									<td class="">
											<?php echo htmlspecialchars($row->provider, ENT_COMPAT, 'UTF-8');?>
									</td>

									<td class="">
											<?php echo htmlspecialchars($row->from, ENT_COMPAT, 'UTF-8'); ?>
									</td>
									<td class="">
									<?php if(!empty($row->params)){ ?>
									<a class="modal notification" href="<?php echo Route::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=param&id='. $row->id); ?>"><?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP");?></a>
									</a>
									<?php }
									else
									{
										echo Text::_('COM_TJNOTIFICATIONS_EMPTY_PARAMS');
									}?>
									</td>
									<td class="">
											<?php echo htmlspecialchars($row->id, ENT_COMPAT, 'UTF-8');?>
									</td>
									</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
