<h3><?php echo $title?></h3>

<form action="<?php echo adminurl('/sitecms/viewnews');?>" method="post">
<p><strong>Subject: </strong><input type="text" name="subject" value="<?php if(isset($newsitem)) { echo $newsitem->subject; }?>" /></p>
<p>
<p><strong>News Text: </strong></p>
<p>
<textarea id="editor" name="body" width="100%" 
style="width: 100%; height: 250px;"><?php if(isset($newsitem->body)) { echo $newsitem->body;}?></textarea>
</p>
<input type="hidden" name="action" value="<?php echo $action?>" />
<input type="hidden" name="id" value="<?php echo $newsitem->id; ?>" />
<input type="submit" name="submit" value="<?php echo $title?>" />
</form>