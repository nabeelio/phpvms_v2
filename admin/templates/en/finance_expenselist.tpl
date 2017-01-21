<h3>Current Expenses</h3>
<?php
if(!$allexpenses)
{
	echo '<p>No expenses have been added</p>';
	return;
}

$expense_list = Config::Get('EXPENSE_TYPES');
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Name</th>
	<th>Price</th>
	<th>Type</th>
	<th>Options</th>
</tr>
</thead>
<tbody>
<?php
foreach($allexpenses as $expense)
{
?>
<tr id="row<?php echo $expense->id;?>">
	<td align="center"><?php echo $expense->name; ?></td>
	<td align="center"><?php 
	
	if($expense->type == 'P' || $expense->type == 'G')
		echo $expense->cost.'%'; 
	else
		echo Config::Get('MONEY_UNIT').$expense->cost; 
	
	?></td>
	<td align="center"><?php echo $expense_list[$expense->type]; ?></td>
	<td align="center" width="1%" nowrap>
		<button id="dialog" class="jqModal button" 
			href="<?php echo adminaction('/finance/editexpense/'.$expense->id);?>">
		Edit</button>
		
		<button href="<?php echo adminaction('/finance/viewexpenses');?>" action="deleteexpense"
			id="<?php echo $expense->id;?>" class="deleteitem button">Delete</button>
	</td>
</tr>
	<?php
}
?>
</tbody>
</table>