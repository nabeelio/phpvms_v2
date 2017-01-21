<h3>Downloads</h3>
<?php
if(!$allcategories)
{
	echo 'No categories or downloads have been added!';
	$allcategories = array();
}

foreach($allcategories as $category)
{
?>
<div id="<?php echo $category->id;?>"
	<h3><?php echo $category->name?> 
		<span style="font-size: 8pt">
		<button id="dialog" class="jqModal button {button:{icons:{primary:'ui-icon-wrench'}}}" 
			href="<?php echo adminaction('/downloads/editcategory?id='.$category->id);?>">Edit</button>  
			
		<button class="ajaxcallconfirm button {button:{icons:{primary:'ui-icon-trash'}}}" action="deletecategory" id="<?php echo $category->id?>" 
			href="<?php echo adminaction('/downloads')?>">Delete</button>   
			
		<button id="dialog" class="jqModal button {button:{icons:{primary:'ui-icon-circle-plus'}}}" 
			href="<?php echo adminaction('/downloads/adddownload?cat='.$category->id);?>">Add Download</button>
		</span>
	</h3>
<?php
	$alldownloads = DownloadData::getDownloads($category->id);
	
	if(!$alldownloads)
	{
		echo 'There are no downloads under this category.';
	}
	else
	{
?>
	<table id="tabledlist" class="tablesorter">
		<thead>
		<tr>
			<th>Download Name</th>
			<th>Description</th>
			<th align="center">Download Count</th>
			<th>Options</th>
		</tr>
		</thead>
		<tbody>
<?php	
foreach($alldownloads as $download) 
{ 
?>
<tr id="row<?php echo $download->id?>">
	<td><?php echo '<a href="'.$download->link.'">'.$download->name.'</a>' ?></td>
	<td><?php echo $download->description==''?'-':$download->description; ?></td>
	<td align="center"><?php echo ($download->hits=='')? '0' : $download->hits?></td>
	
	<td width="1%" nowrap>
		<button id="dialog" class="jqModal {button:{icons:{primary:'ui-icon-wrench'}}}" 
			href="<?php echo adminaction('/downloads/editdownload?id='.$download->id);?>">Edit</button>
			
		<button class="deleteitem {button:{icons:{primary:'ui-icon-trash'}}}" action="deletedownload" id="<?php echo $download->id?>"
			href="<?php echo adminaction('/downloads');?>">Delete</button>	
	</td>
</tr>
<?php	
}
?>
		</tbody>
		</table>
<?php
	 }
	 ?>
	 </div>
<?php
}
?>