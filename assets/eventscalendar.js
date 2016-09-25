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