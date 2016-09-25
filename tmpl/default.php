<?php
/*------------------------------------------------------------------------
# mod_fceventscalendar - Event Calendar
# ------------------------------------------------------------------------
# author    Christophe Seguinot
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# website https://github.com/ChristopheSeg/mod_fceventscalendar
# mod_fceventscalendar is a fork of mod_fcnewscalendar
# written by Jesús Vargas Garita from www.joomlahill.com
#
-------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die;

$doc = JFactory::getDocument();

if (file_exists(JPATH_BASE.DIRECTORY_SEPARATOR.'modules' . DIRECTORY_SEPARATOR . 'mod_fceventscalendar' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $params->get('stylesheet'))) {
	$stylesheet = $params->get('arrows_stylesheet', 'blue-arrows.css');
} else {
	$stylesheet = 'blue-arrows.css';
}

$doc->addStyleSheet('modules/mod_fceventscalendar/css/fc_evt_cal_style.css');
$doc->addStyleSheet('modules/mod_fceventscalendar/css/' . $stylesheet);
$script = "var sfolder = '" . JURI::base(true) . "';";
$doc->addScriptDeclaration($script);
$doc->addScript('modules/mod_fceventscalendar/assets/eventscalendar.js');

$params->set('mid',$module->id);
$Itemid = $app->getMenu()->getActive()->id;
$params->set('Itemid',$Itemid);

?>
<div class="eventscalendar" id="eventscalendar<?php echo $module->id; ?>">
<?php
modFCEventsCalendarHelper::renderCal($cal,$params);
echo $cal->output;

?>
	<div class="bgimgcall"><img src="modules/mod_fceventscalendar/assets/opacity.png" /></div>
</div>
