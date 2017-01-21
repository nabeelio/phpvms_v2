<h3><?php echo $title?></h3>

<table id="grid"></table>
<div id="pager"></div>
<br />

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo fileurl('/lib/js/jqgrid/css/ui.jqgrid.css');?>" />
<script src="<?php echo fileurl('/lib/js/jqgrid/js/i18n/grid.locale-en.js');?>" type="text/javascript"></script>
<script src="<?php echo fileurl('/lib/js/jqgrid/js/jquery.jqGrid.min.js');?>" type="text/javascript"></script>

<script type="text/javascript">
$("#grid").jqGrid({
   url: '<?php echo adminaction('/operations/schedulegrid');?>',
   datatype: 'json',
   mtype: 'GET',
   colNames: ['Code', 'Flight Num', 'Departure', 'Arrival', 'Aircraft', 'Registration', 'Route', 'Days',
				'Distance', 'Flown', 'Edit', 'Delete'],
   colModel : [
		{index: 'code', name : 'code', width: 40, sortable : true, align: 'center', search: 'true', searchoptions:{sopt:['eq','ne']}},
		{index: 'flightnum', name : 'flightnum', width: 65, sortable : true, align: 'center', searchoptions:{sopt:['eq','ne']}},
		{index: 'depicao', name : 'depicao', width: 60, sortable : true, align: 'center',searchoptions:{sopt:['eq','ne']}},
		{index: 'arricao', name : 'arricao', width: 60, sortable : true, align: 'center',searchoptions:{sopt:['eq','ne']}},
		{index: 'a.name', name : 'aircraft', width: 100, sortable : true, align: 'center',searchoptions:{sopt:['in']}},
		{index: 'a.registration', name : 'registration', width: 100, sortable : true, align: 'center', searchoptions:{sopt:['eq','ne']}},
		{index: 'route', name : 'route', width: 100, sortable : true, align: 'center',searchoptions:{sopt:['in']}},
		{index: 'daysofweek', name : 'route', width: 100, sortable : true, align: 'center', search: false},
		{index: 'distance', name : 'distance', width: 100, sortable : true, align: 'center', searchoptions:{sopt:['lt','le','gt','ge']}},
		{index: 'timesflown', name : 'flown', width: 100, sortable : true, align: 'center', search: false},
		{index: '', name : '', width: 100, sortable : true, align: 'center', search: false},
		{index: '', name : '', width: 100, sortable : true, align: 'center', search: false}
	],
    pager: '#pager', rowNum: 25,
    sortname: 'flightnum', sortorder: 'asc',
    viewrecords: true, autowidth: true,
    height: '100%'
});

jQuery("#grid").jqGrid('navGrid','#pager', 
	{edit:false,add:false,del:false,search:true,refresh:true},
	{}, // edit 
	{}, // add 
	{}, //del 
	{multipleSearch:true} // search options 
); 

function deleteschedule(id)
{
	var answer = confirm("Are you sure you want to delete?")
	if (answer) {
		$.post("<?php echo adminaction('/operations/schedules');?>", { action: "deleteschedule", id: id },
			function() 
			{ 
				$("#grid").trigger("reloadGrid"); 
			}
		);
	}
}

function showroute(schedule_id)
{
	$('#jqmdialog').jqm({
		ajax:'<?php echo adminaction('/operations/viewmap');?>?type=schedule&id='+schedule_id
	}).jqmShow();
}
</script>