<h3>All Pages</h3>
<?php
if(!$allpages)
{
	echo '<p>No pages have been added!</p>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Page Name</th>
	<th>Updated By</th>
	<th>Update Date</th>
	<th>File/Link</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($allpages as $page)
{
?>
<tr id="row<?php echo $page->pageid;?>">
	<td align="center"><?php echo $page->pagename; ?></td>
	<td align="center"><?php echo $page->postedby; ?></td>
	<td align="center"><?php echo $page->postdate; ?></td>
	<td align="center"><a href="<?php echo url('/pages/'.$page->filename); ?>"><?php echo $page->filename; ?></a></td>
	<td align="center" width="1%" nowrap>
		<button class="{button:{icons:{primary:'ui-icon-wrench'}}}" 
		onclick="window.location='<?php echo adminurl('/sitecms/editpage?pageid='.$page->pageid);?>';">Edit</button>

		<button class="deleteitem {button:{icons:{primary:'ui-icon-trash'}}}" 
			id="<?php echo $page->pageid;?>"
			href="<?php echo adminaction('/sitecms/viewpages?action=deletepage&pageid='.$page->pageid);?>">Delete</button>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>