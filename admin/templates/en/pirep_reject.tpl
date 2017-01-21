<h3>Reject PIREP</h3>
<p>Please enter a comment for why you are rejecting this report. It'll be entered in the comments for the report. You do have the option later on to accept this report.</p>
<form id="form" action="<?php echo SITE_URL?>/admin/action.php/pirepadmin/viewpending" method="post">
<textarea name="comment" style="width: 90%;"></textarea>

<input type="hidden" name="pirepid" value="<?php echo $pirepid;?>" />
<input type="hidden" name="action" value="rejectpirep" />
<input type="submit" name="submit" value="Reject this Report" />
<input type="button" name="close" value="Cancel" class="jqmClose" />

</form>