<h3>Add Comment</h3>
<p>This comment will be emailed to the submitter of the PIREP. You can ask for specifics on the report, and get an answer, prior to accepting or rejecting the report.</p>
<form id="flashForm" action="<?php echo SITE_URL?>/admin/action.php/pirepadmin/addcomment" method="post">
<textarea name="comment" style="width: 90%;"></textarea>

<input type="hidden" name="pirepid" value="<?php echo $pirepid;?>" />
<input type="hidden" name="action" value="addcomment" />
<input type="submit" name="submit" value="Add Comment" />

</form>