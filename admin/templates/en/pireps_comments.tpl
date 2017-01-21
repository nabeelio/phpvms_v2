<?php
if(!$comments)
{
	echo '<p align="center"><strong>There are no comments for this PIREP</strong></p>';
	return;
}
?>
<div id="dialogresult"></div>
<?php
foreach($comments as $comment)
{
?>
	<p id="row<?php echo $comment->id;?>">
	<strong><?php echo $comment->firstname. ' '. $comment->lastname ?> - <?php echo date(DATE_FORMAT, $comment->postdate);?></strong><br />
	<?php echo $comment->comment; ?>
	<br />
	
	<span style="float: right"><a href="<?php echo SITE_URL.'/admin/action.php/pirepadmin/deletecomment'?>" 
		class="deletecomment" action="deletecomment" id="<?php echo $comment->id;?>">Delete Comment</a></span><br />
		
	<hr>
	</p>
<?php
}
?>