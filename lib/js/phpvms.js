/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */
 
// Icons for Google Maps
var url = window.location.href.split("/")[0];
var depicon = url + '/lib/images/towerdeparture.png';
var arricon = url + '/lib/images/towerarrival.png';

// Everything else
$(document).ready(function() 
{
	$("#form, .ajaxform").ajaxForm({
		target: '#scheduleresults',
		beforeSubmit: function (x,y,z) {
		    $("#scheduleresults").html('<div align="center"><img src="'+url+'/lib/images/loading.gif" /><br />Searching...</div>');
		},
		success: function() {
			$('#bodytext').fadeIn('slow');
		}
	});
		
	$("#code").live("change", function()
	{
		$("#depairport").load(url+"action.php/pireps/getdeptapts/"+$(this).val());
	});
		
	$("#tabcontainer").tabs();
	
	$('.deleteitem').live('click',function(){return false;});
	$('.deleteitem').live('dblclick', function(){
		$.post($(this).attr("href"), {id: $(this).attr("id")});
		rmvid= "#bid"+$(this).attr("id"); $(rmvid).slideUp();
		return false;
	});
	
	$('.addbid').live('click', function(){	
		var id = "#"+$(this).attr("id");
		
		$.get(url+"action.php/schedules/addbid?id="+$(this).attr("id"),  
			function (data) {
				$(id).html(data);
				return false;
		});
			
		return false;
	});
	
	$("div .metar").each(function(){
		icao=$(this).attr("id");
		$.getJSON(geourl+"/weatherIcaoJSON?ICAO="+icao+"&callback=?", 
		function(data){
		 	if(data.length == 0) {
		 		html = "Could not load METAR information";
		 	}
		 	else {
				data.weatherObservation.observation = data.weatherObservation.observation.replace("$", "");
				html = "<strong>METAR: </strong>"+data.weatherObservation.observation+"<br />";
			}
			
			$("#"+data.weatherObservation.ICAO).html(html);
		});
	});
});



// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){
  var cache = {};
 
  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :
     
      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +
       
        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +
       
        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");
   
    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();
