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

require_once JPATH_SITE.'/components/com_flexicontent/helpers/route.php';

jimport('joomla.application.component.model');

require_once(JPATH_SITE.'/components/com_flexicontent/classes/flexicontent.fields.php');
require_once(JPATH_SITE.'/components/com_flexicontent/classes/flexicontent.helper.php');

class modFCEventsCalendarHelper{
	public static function getAjax()
	{
		// getAjax is the default method called by com_ajax
		$input = JFactory::getApplication()->input;
		$month  = $input->get('month', '', 'string');
		$year  = $input->get('year', '', 'string');
		$mid  = $input->get('mid', '', 'string');
		$Itemid =  $input->get('Itemid', '', 'string');

		// get module parameters
		$module = JModuleHelper::getModule('mod_fceventscalendar');
		$registry = new JRegistry;
		$registry->loadString($module->params);
		$registry->set('mid', $mid);
		$registry->set('Itemid', $Itemid);
		$registry->set('curmonth',$month);
		$registry->set('curyear',$year);
		$registry->set('ajaxed',1);
		$module->params = $registry;
		$cal = modFCEventsCalendarHelper::getCal($module->params);
		modFCEventsCalendarHelper::renderCal($cal,$module->params);
		return $cal->output;

	}

	public static function renderCal(&$cal,$params) {

		$output = '';
		$curyearm1=$cal->curyear-1;
		$curyearp1=$cal->curyear+1;
		$curmonthm1=$cal->curmonth!=1?$cal->curmonth-1:12;
		$curyear1=$cal->curmonth!=1?$cal->curyear:$cal->curyear-1;
		$curyear2=$cal->curmonth!=12?$cal->curyear:$cal->curyear+1;
		$curmonthp1=$cal->curmonth!=12?$cal->curmonth+1:1;
		$yearname=$params->get('subyearname')?substr($cal->curyear,2):$cal->curyear;
		$mid = $params->get('mid');
		$Itemid = $params->get('Itemid');
		$itembottomcolor = $params->get('itembottomcolor', '#ff0000');
		$daterangecolor = $params->get('daterangecolor', '#ff0000');

		$output .= "
		<table class=\"nc-header\" cellspacing=\"0\">
		<thead>
		<tr>
		<td width=\"20\"><a href=\"javascript:void(0)\" onClick=\"updateEventsCalendar({$cal->curmonth},{$curyearm1}, {$mid},{$Itemid})\" class=\"back-yr\"></a></td>
		<td width=\"20\"><a href=\"javascript:void(0)\" onClick=\"updateEventsCalendar({$curmonthm1},{$curyear1},{$mid},{$Itemid})\" class=\"back-mth\"></a></td>
		<td style=\"text-align:center\"><div id=\"monthyear_{$mid}\"><span class=\"monthname\">{$cal->monthname}</span> <span class=\"yearname\">{$yearname}</span></div></td>
						<td width=\"20\" align=\"right\"><a href=\"javascript:void(0)\" onClick=\"updateEventsCalendar({$curmonthp1},{$curyear2},{$mid},{$Itemid})\" class=\"fwd-mth\"></a></td>
						<td width=\"20\" align=\"right\"><a href=\"javascript:void(0)\" onClick=\"updateEventsCalendar({$cal->curmonth},{$curyearp1},{$mid},{$Itemid})\" class=\"fwd-yr\"></a></td>
					</tr>
				</thead>
			</table>
			<table class=\"nc-body\" cellspacing=\"0\">
				<thead>
					<tr>";

		for($i = $params->get('firstday'); $i <= $params->get('firstday')+6; $i++) {
			$d=($i>6)?($i-6):($i+1);
			$day=modFCEventsCalendarHelper::encode('MOD_FC_EVT_CAL_DAYNAME_' . $params->get('subdayname', 2) . '_' . $d, $params->get('encode'),$cal->ajaxed);
			$output .= " <td class=\"dayname dayweek{$d}\" >{$day}</td>";
}

		$output .="
		</tr>
				</thead>
				<tbody>";
					$fday=$params->get('firstday');
					for($i = 0; $i < count($cal->weeks); $i++)
					{
					?><tr class="week<?php echo $i+1; ?>">
						<?php
						for($j=0; $j < 7; $j++)
						{
							if(!empty($cal->weeks[$i][$j]))
							{
								$day = explode(' ',$cal->weeks[$i][$j]);
								$class = 'nc-day';
								$style ="";
								$bottomstyle="";
								$hasrangeitemclass="norangeitem";
								$hassingledateitemclass = " hasnoitemsbottomline";

								$ul = '';
								if ( $day[1] ) {
									$class .= " outofmonth";
								} else {
									$class .= " weekday".((($j+$fday)>6)?(($j+$fday)-6):($j+$fday+1)) . " relday" . ($j+1);
									if ($day[0]==date('j') && $cal->curmonth==date('m')) {
										$class .= " today";
									}
								}


								$ul_output='';
								if (isset($cal->items[$day[0]]) && !$day[1]) {
									$ul_output ="<ul class=\"nc-items\">";

									foreach ($cal->items[$day[0]] as $item) {

										if (isset($item->has_date_range_items)) {
											$hasrangeitemclass="hasrangeitem";
											$bottomstyle =" style=\" border-bottom-color: $daterangecolor;\"";
											$listyle=" style =\"color:$daterangecolor;\"";
										}
										if (isset($item->has_single_date_items)) {
											$hassingledateitemclass = " hasitemsbottomline";
											$style =" style=\" border-bottom-color: $itembottomcolor;\"";
											$listyle=" style =\"color:$itembottomcolor;\"";
										}
										$ul_output .="<li $listyle><a href=\"$item->link\">{$item->daterange} {$item->title}</a></li>";
									}
									$ul_output .="</ul>";
								}
								$output .="<td><span $style class=\"$class $hassingledateitemclass\">";
								$output .=$day[0]. $ul_output;
							   	$output .="</span> <span $bottomstyle class=\" $hasrangeitemclass \"></span></td>";
							} else {
							$output .="<td></td>";
							}
						}
						$output .="</tr>";
				 }
		$output .="
				</tbody>
			</table>";
		$cal->output = $output;
return;
	}

	private static function get_range_date_string(
			$start_date ='', $end_date='',
			$date_format='DMY', $show_single_date='0',
			$Y='Y', $M='M', $D='d', $YMD_sep='/', $range_sep='-') {

		if ($date_format=='none') {
			return '';
		}
			$app = JFactory::getApplication();
		$start_date=strtotime($start_date);
		$date_range = '';
		// If only one date
		if ( empty($end_date) ) {
			if ($show_single_date) {
				if ($date_format=='DMY'){
					$date_range = date( "$D$YMD_sep$M$YMD_sep$Y", $start_date );
				} else {
					$date_range = date( "$Y$YMD_sep$M$YMD_sep$D", $start_date );
				}
			}
			return $date_range;
		}
		$end_date=strtotime($end_date);
		if ( date('FjY',$start_date) == date('FjY',$end_date) ) {
			if ($show_single_date) {
				if ($date_format=='DMY'){
					$date_range = date( "$D$YMD_sep$M$YMD_sep$Y", $start_date );
				} else {
					$date_range = date( "$Y$YMD_sep$M$YMD_sep$D", $start_date );
				}
			}

			return $date_range;
		}

		if ($date_format=='DMY'){
			// Setup basic dates: sample 2016-09-19 / 2016-09-27
			$start_date_pretty = date( "$Y$YMD_sep$M$YMD_sep$D", $start_date );
			$end_date_pretty = date( "$D", $end_date );
			//
			if ( date('F',$start_date) != date('F',$end_date) ) {
				$end_date_pretty = date( "$M$YMD_sep", $end_date ). $end_date_pretty;

				// If only months differ add suffix and year to end_date
				if ( date('Y',$start_date) != date('Y',$end_date) ) {
					$end_date_pretty = date( "$Y$YMD_sep", $end_date) . $end_date_pretty;
				}
			}

			// build date_range return string
			$date_range .= $start_date_pretty .'-'. $end_date_pretty . ': ';
		}
		 else {
			// Setup basic dates: sample 2016-09-19 / 2016-09-27
			$start_date_pretty = date( "$D", $start_date );
			$end_date_pretty = date( "$D$YMD_sep$M$YMD_sep$Y", $end_date );
			//
			if ( date('F',$start_date) != date('F',$end_date) ) {
				$start_date_pretty = $start_date_pretty . date( "$M$YMD_sep", $end_date );
				;
				// If only months differ add suffix and year to end_date
				if ( date('Y',$start_date) != date('Y',$end_date) ) {
					$start_date_pretty = $start_date_pretty . date( "$Y$YMD_sep", $end_date);
				}
			}


			// build date_range return string
			$date_range .= $start_date_pretty . $range_sep . $end_date_pretty . ': ';
		}

		return $date_range;
	}


	public static function getCal(&$params) {
		$cal = new JObject();

		$curmonth=(int) JRequest::getVar('month',($params->get("defmonth")?$params->get("defmonth"):date('n')));
		$curyear=(int) JRequest::getVar('year',($params->get("defyear")?$params->get("defyear"):date('Y')));

		$dayofmonths=array(31,(!($curyear%400)?29:(!($curyear%100)?28:(!($curyear%4)?29:28)) ), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$dayofmonth = $dayofmonths[$curmonth-1];
		$day_count = 1;
		$num = 0;

		$weeks = array();
		for($i = 0; $i < 7; $i++) {
			$a=floor((14-$curmonth)/12);
			$y=$curyear-$a;
			$m=$curmonth+12*$a-2;
			$dayofweek=($day_count+$y+floor($y/4)-floor($y/100)+floor($y/400)+floor((31*$m)/12)) % 7;
			$dayofweek = $dayofweek - 1 - $params->get("firstday");
			if($dayofweek <= -1) $dayofweek =$dayofweek + 7;


			if($dayofweek == $i)
			{
				$weeks[$num][$i] = $day_count.' 0';
				$day_count++;
			}
			else
			{
				$weeks[$num][$i] = ($dayofmonths[$curmonth!=1?($curmonth-2):(11)]-($dayofweek-1-$i)).' 1';
			}
		}

		while(true) {
			$num++;
			for($i = 0; $i < 7; $i++)
			{
				if ($day_count > $dayofmonth) {
					$weeks[$num][$i] = ($day_count-$dayofmonths[$curmonth-1]).' 1';
				} elseif ($day_count <= $dayofmonth) {
					$weeks[$num][$i] = $day_count.' 0';
				}
				$day_count++;

				if($day_count > $dayofmonth && $i==6) break;
			}
			if($day_count > $dayofmonth && $i==6) break;
		}

		if (!$params->get('ajaxed')) {
			$ajaxed = 0;
		} else {
			$ajaxed = 1;
		}

		$monthname = 'MOD_FC_EVT_CAL_MONTHNAME_' . $params->get( "submonthname", 0 ) . '_' . $curmonth;
		$monthname = modFCEventsCalendarHelper::encode($monthname,$params->get('encode'),$ajaxed);

		$cal->items = modFCEventsCalendarHelper::getList($params, $curmonth, $curyear );
		$cal->weeks = $weeks;
		$cal->curmonth = $curmonth;
		$cal->curyear = $curyear;
		$cal->monthname = $monthname;
		$cal->dayofmonths = $dayofmonths;
		$cal->ajaxed = $ajaxed;

		return $cal;
	}

	public static function getList(&$params, $curmonth, $curyear)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$access = !JComponentHelper::getParams('com_flexicontent')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

		$catids  = $params->get('catid', array());
		$catids_list = implode(',', $catids);

		$user_selection  = $params->get('user_selection', '0');


		// (article->state takes the values 1=published, 0=unpublished, -1=archived, -2=deleted)
		$state = $params->get('state', 'published');
		$date_format  = $params->get('date_format', 'DMY');
		$show_single_date = $params->get('show_single_date', '0');
		$year_format = $params->get('year_format', 'Y');
		$month_format  = $params->get('month_format', 'm');
		$day_format = $params->get('day_format', 'd');
		$YMD_separator = $params->get('YMD_separator', '/');
		$range_separator = $params->get('range_separator', '-');

		$startdate = $params->get('startdate', '');
		$query ="SELECT name,iscore FROM #__flexicontent_fields WHERE id=$startdate";
		$db->setQuery($query);
		$field = $db->loadObject();
		$startdateiscorefield = $field->iscore;
		$startdatefieldname = $field->name;


		$enddate = $params->get('enddate', '');
		$query ="SELECT name,iscore FROM #__flexicontent_fields WHERE id=$enddate";
		$db->setQuery($query);
		$field = $db->loadObject();
		$enddateiscorefield = $field->iscore;
		$enddatefieldname = $field->name;

		$startDateRange = $curyear . '-' . sprintf("%02d", $curmonth) . '-01 00:00:00';

		$endDateRange   = $curyear . '-' . sprintf("%02d", $curmonth+1) . '-01 00:00:00';
		if ( $curmonth == 12 ) {
			$endDateRange = ($curyear + 1) . '-01-01 00:00:00';
		}

		if ($enddate){
			$usedaterange=true;
		} else  {
			$usedaterange=false;
		}

		$state   = $params->get('state', 1);

		$select = "SELECT i.id, i.title, i.alias, i.catid, c.alias AS category_alias";
		$from = " FROM #__content AS i";
		$join = " JOIN #__flexicontent_items_ext AS ie ON ie.item_id = i.id
				JOIN #__flexicontent_types AS ty ON ie.type_id = ty.id
				JOIN #__flexicontent_cats_item_relations AS rel ON rel.itemid = i.id
				JOIN #__categories AS c ON c.id = i.catid
				LEFT JOIN #__users AS u ON u.id = i.created_by";
		$where = " WHERE 1";

		//TODO Language ?? Ty.access c.access i.access
		$where .= " AND rel.catid IN ($catids_list)
				AND ( i.state =$state )
				AND ( ( i.publish_up = '0000-00-00 00:00:00' OR i.publish_up <= UTC_TIMESTAMP() )  ) AND ( ( i.publish_down = '0000-00-00 00:00:00' OR i.publish_down >= UTC_TIMESTAMP() ) )
				AND ( ie.language LIKE 'fr%' OR ie.language=\"*\" )
				AND ty.access IN (0,1,1,5) AND c.access IN (0,1,1,5) AND i.access IN (0,1,1,5)";
		$order = " ORDER BY i.title";
		$w8 ='';
		switch ($user_selection)
		{
			case 'by_me':
				$user = JFactory::getUser();
				$userId = $user->get('id');
				$where.= " (AND i.created_by=$userId OR i.modified_by=$userId)";
				break;
			case 'not_me':
				$user = JFactory::getUser();
				$userId = $user->get('id');
				$where.= " AND i.created_by!=$userId AND i.modified_by!=$userId";
				break;
		}

		// construct the tricky filter

		if (true)
		{
			// Filter for start date (Event has always a start date)
			if ($startdateiscorefield)
			{
				$select .=" ,i.$startdatefieldname as day";
				if ($usedaterange) {
					$w1 = " (i.$startdatefieldname<='$startDateRange')";
					$w4 = " (i.$startdatefieldname<='$endDateRange')";
					$w5 = " (i.$startdatefieldname>='$startDateRange')";
				}
				$w8 = " AND ('$startDateRange'<=i.$startdatefieldname) AND (i.$startdatefieldname<='$endDateRange')";
			}
			else
			{
				$join .= " JOIN #__flexicontent_fields_item_relations AS irel ON irel.item_id = i.id";
				$select .=" ,irel.value as day";
				$where .= " AND irel.field_id=$startdate AND irel.valueorder=1 AND irel.suborder=1";
				if ($usedaterange) {
					$w1 = " (irel.value<='$startDateRange')";
					$w4 = " (irel.value<='$endDateRange')";
					$w5 = " (irel.value>='$startDateRange')";
	 			}
				$w8 .= " AND ('$startDateRange'<=irel.value) AND (irel.value<='$endDateRange')";

			}
		}
		if ($usedaterange)
		{
			// Filter for end date (Range date event)
			if ($enddateiscorefield)
			{
				$select .=" ,i.$enddatefieldname as day2";
				$w0 = '';
				$w2 = " (i.$enddatefieldname>='$startDateRange')";
				$w3 = " (i.$enddatefieldname>='$endDateRange')";
				$w6 = " (i.$enddatefieldname<='$endDateRange')";
				if ($startdateiscorefield) {
					$w7 = " (i.$enddatefieldname='0000-00-00 00:00:00' AND i.$startdatefieldname>='$endDateRange' AND i.$startdatefieldname<='$endDateRange')";
				} else {
					$w7 = " (i.$enddatefieldname='0000-00-00 00:00:00' AND irel.value >='$startDateRange' AND irel.value<='$endDateRange')";
				}
			}
			else
			{
				$join2 = " JOIN #__flexicontent_fields_item_relations AS irel2 ON irel2.item_id = i.id";
				$select2 =" ,irel2.value as day2";
				$w0 = " AND irel2.field_id=$enddate AND irel2.valueorder=1 AND irel2.suborder=1";
				$w2 = " (irel2.value>='$startDateRange')";
				$w3 = " (irel2.value>='$endDateRange')";
				$w6 = " (irel2.value<='$endDateRange')";
				if ($startdateiscorefield) {
					$w7 = " (irel2.value='0000-00-00 00:00:00' AND i.$startdatefieldname>='$endDateRange' AND i.$startdatefieldname<='$endDateRange')";
				} else {
					$w7 = " (irel2.value='0000-00-00 00:00:00' AND irel.value >='$startDateRange' AND irel.value<='$endDateRange')";
				}
			}
		}

		$where_ids='';
		// First retrieve date range items
		if ($usedaterange)
			{
			// (day <= startDR and day2>=startDR) OR (day2>= endDR and day< endDR) OR (day >= startDR and day2<=endtDR)
			// OR (day2='0000-00-00 00:00:00' and day >= startDR AND day< endDR)
			$where2 =$where . " $w0 AND (( $w1 AND $w2 ) OR ( $w3 AND $w4 ) OR ( $w5 AND $w6)  OR ($w7) )";
			$query = $select.$select2.$from.$join.$join2.$where2.$order;
			$db->setQuery($query);
			$items_date_range = $db->loadObjectList();
			$items_id = array();
			foreach ($items_date_range as &$item) {
				$items_id[] = $item->id;
				}
			if (!empty($items_id)) {
				$where_ids=' AND i.id NOT IN ('.implode(",", $items_id).')';
			}
		}

		// Than search for : single date items, or,
		// or date range items without second date (considered as single date items
		$where1 =$where . " $w8 ";
		$query = $select.$from.$join.$where1.$where_ids.$order;
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$first_day_of_month = $curyear.'-'.$curmonth.'-01';
		$d = new DateTime($first_day_of_month);
		$d->modify('first day of this month');
		$first_day_of_month = $d->format('Y-m-d');
		$d->modify('last day of this month');
		$last_day_of_month = $d->format('Y-m-d');
		$first_day_of_month_time = strtotime($first_day_of_month);
		$last_day_of_month_time = strtotime($last_day_of_month);

		$calitems = array();

		foreach ($items_date_range as &$item) {
			$app->enqueueMessage("item= $item->id: day: $item->day / $item->day2 titre:$item->title");
		}
		foreach ($items as &$item) {
			$app->enqueueMessage("item= $item->id: day: $item->day ------- titre:$item->title");
		}
		$app->enqueueMessage("date_format=$date_format");
		// calculate date range item calendar
		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->categoryslug = $item->catid.':'.$item->category_alias;


			//TODO remove ACCESS ???
			if ($access || in_array($item->access, $authorised)) {
				$link = FlexicontentHelperRoute::getItemRoute($item->slug, $item->categoryslug);
				if ($params->get('remmonth',0)) {
					$link .= '&month='.$curmonth.'&year='.$curyear;
				}
			} else {
				$link = 'index.php?option=com_users&view=login';
			}
			$item->daterange = modFCEventsCalendarHelper::get_range_date_string(
						$item->day, '',
						$date_format, $show_single_date,
						$year_format, $month_format, $day_format,
						$YMD_separator, $range_separator);

			$item->link = JRoute::_($link);
			$item->day1 =$item->day;
			$item->day2 ='';
			$item->day = JHtml::_('date',$item->day, 'j');
			$item->has_single_date_items=true;
			$calitems[$item->day][] = $item;

		}

		// calculate date range item calendar
		foreach ($items_date_range as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->categoryslug = $item->catid.':'.$item->category_alias;

			$item->daterange = modFCEventsCalendarHelper::get_range_date_string(
						$item->day, $item->day2,
						$date_format, $show_single_date,
						$year_format, $month_format, $day_format,
						$YMD_separator, $range_separator);

			//TODO remove ACCESS ???
			if ($access || in_array($item->access, $authorised)) {
				$link = FlexicontentHelperRoute::getItemRoute($item->slug, $item->categoryslug);
				if ($params->get('remmonth',0)) {
					$link .= '&month='.$curmonth.'&year='.$curyear;
				}
			} else {
				$link = 'index.php?option=com_users&view=login';
			}

			$item->link = JRoute::_($link);
			$item->day1 =$item->day;

			$sdate = explode("-", $item->day);
			$edate = explode("-", $item->day2);
			$startdatetime = @mktime(0,0,0, $sdate[1],$sdate[2],$sdate[0]);
			$enddatetime = @mktime(0,0,0, $edate[1],$edate[2],$edate[0]);
			if($startdatetime < $first_day_of_month_time)
				$startdatetime = $first_day_of_month_time;
				if($enddatetime > $last_day_of_month_time)
					$enddatetime = $last_day_of_month_time;
					for($i=$startdatetime;$i<=$enddatetime;$i=$i+86400) {
						$itemx = clone($item);
						$itemx->day = JHtml::_('date', date('Y-m-d', $i), 'j');
						$itemx->has_date_range_items=true;
						$calitems[$itemx->day][] = $itemx;
					}

		}


		return $calitems;
	}


	public static function encode($text,$encode,$ajaxed) {
		if ($encode!='UTF-8' && $ajaxed) {
			$text=iconv("UTF-8", $encode, JText::_($text));
		}
		else {
			$text=JText::_($text);
		}
		return $text;
	}
}
