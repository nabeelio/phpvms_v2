<h3>Tasks</h3>
<ul class="filetree treeview-famfamfam">
	<li><span class="file">
	<a href="<?php echo SITE_URL?>/admin/index.php/pirepadmin/viewpending">View all</a></span></li>
	<li><span class="file">
	<a href="<?php echo SITE_URL?>/admin/index.php/pirepadmin/viewpending?hub=<?php echo Auth::$userinfo->hub?>">View my hub (<?php echo Auth::$userinfo->hub?>)</a>
	</span></li>
</ul>
<h3>Help</h3>
<p>Accept or reject any pending PIREPs. If you choose to reject, you will be asked to give a reason for the rejection. It will be entered as a comment on the flight report.</p>