<?php
if(!$allpages)
	return;

foreach($allpages as $page)
{
	echo '<li><a href="'.url('/pages/'.$page->filename).'">'.$page->pagename.'</a></li>';
}

?>