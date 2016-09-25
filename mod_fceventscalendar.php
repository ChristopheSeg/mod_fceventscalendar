<?php
/*------------------------------------------------------------------------
# mod_fceventscalendar - News Calendar
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# Copyright (C) 2010 www.joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$cal = modFCEventsCalendarHelper::getCal($params);

require JModuleHelper::getLayoutPath('mod_fceventscalendar');