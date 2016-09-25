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

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$cal = modFCEventsCalendarHelper::getCal($params);

require JModuleHelper::getLayoutPath('mod_fceventscalendar');