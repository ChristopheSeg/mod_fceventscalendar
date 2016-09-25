/*------------------------------------------------------------------------
# mod_fceventscalendar - News Calendar
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# Copyright (C) 2010 www.joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

function updateEventsCalendar(curmonth,curyear,mid,Itemid) 
{	
	var currentURL = window.location;
	var live_site = currentURL.protocol+'//'+currentURL.host+sfolder;
	
	var loading = document.getElementById('monthyear_'+mid);
	
	loading.innerHTML='<img src="'+live_site+'/modules/mod_fceventscalendar/assets/loading.gif" border="0" align="absmiddle" />';
	
	var ajax = new XMLHttpRequest;
   	ajax.onreadystatechange=function()
  	{
		if (ajax.readyState==4 && ajax.status==200)
		{
			document.getElementById("eventscalendar"+mid).innerHTML = ajax.responseText;
		}
  	}	
   	ajax.open("GET",live_site+"/index.php?option=com_ajax&module=fceventscalendar&Itemid="+Itemid+"&month="+curmonth+"&year="+curyear+"&mid="+mid+"&format=raw",true);
	ajax.send();
}