<h3>Agregar Comentario al PIREP</h3>
<form action="<?php echo url('/pireps/viewpireps');?>" method="post">
<strong>Comentario: </strong><br />
<textarea name="comment" style="width:90%; height: 150px"></textarea><br />

<input type="hidden" name="action" value="addcomment" />
<input type="hidden" name="pirepid" value="<?php echo $pirep->pirepid?>" />
<input type="submit" name="submit" value="Agregar Comentario" />
</form>