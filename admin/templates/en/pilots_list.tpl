<h3>Pilots List</h3>

<table id="grid"></table>
<div id="pager"></div>
<br />

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo fileurl('/lib/js/jqgrid/css/ui.jqgrid.css');?>" />
<script src="<?php echo fileurl('/lib/js/jqgrid/js/i18n/grid.locale-en.js');?>" type="text/javascript"></script>
<script src="<?php echo fileurl('/lib/js/jqgrid/js/jquery.jqGrid.min.js');?>" type="text/javascript"></script>

<script type="text/javascript">
$("#grid").jqGrid({
   url: '<?php echo adminaction('/pilotadmin/getpilotsjson');?>',
   datatype: 'json',
   mtype: 'GET',
   colNames: ['','First', 'Last', 'Email', 'Location', 'Status', 'Rank', 'Flights', 'Hours', 'IP', 'Edit'],
   colModel : [
		{index: 'id', name: 'id', hidden: true, search: false },
		{index: 'firstname', name : 'firstname',sortable : true, align: 'left', search: 'true', searchoptions:{sopt:['in']}},
		{index: 'lastname', name : 'lastname',  sortable : true, align: 'left', searchoptions:{sopt:['in']}},
		{index: 'email', name : 'email', sortable : true, align: 'left',searchoptions:{sopt:['li']}},
		{index: 'location', name : 'location',  sortable : true, align: 'center',searchoptions:{sopt:['eq','ne']}},
		{index: 'status', name : 'status', sortable : true, align: 'center',searchoptions:{sopt:['in']}},
		{index: 'rank', name : 'rank', sortable : true, align: 'center', searchoptions:{sopt:['eq','ne']}},
		{index: 'totalflights', name : 'totalflights', sortable : true, align: 'center',searchoptions:{sopt:['lt','gt']}},
		{index: 'totalhours', name : 'totalhours', sortable : true, align: 'center',searchoptions:{sopt:['lt','gt']}},
		{index: 'lastip', name : 'lastip', sortable : true, align: 'center', searchoptions:{sopt:['in']}},
		{index: '', name : '', sortable : true, align: 'center', search: false}
	],
    pager: '#pager', rowNum: 25,
    sortname: 'lastname', sortorder: 'asc',
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
</script>