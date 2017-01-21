<h3>Maintenance Options</h3>

<table class="tablesorter">
<thead>
<tr>
	<td></td>
	<td></td>
</tr>
</thead>
<tbody>
	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/resethours');?>">Reset Hours</a>
		</strong>
	</td>
	<td>This will reset your VA's total hours count<br /></td>
	</tr>
	
	<tr>
		<td>
			<strong>
			<a href="<?php echo adminurl('/maintenance/resetacars');?>">Reset ACARS</a>
			</strong>
		</td>
		<td>Empties the ACARS table, if you're having problems with ACARS updates<br /></td>
	</tr>
	
	<tr>
		<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetsignatures');?>">Reset Signatures</a>
		</strong>
	</td>
	<td>Select this option to reset your member's signatures. If you change the background, regenerate them.
	
	</td>
	</tr>
	
	<tr>
	
		<td><strong>
			<a href="<?php echo adminurl('/maintenance/resetdistances');?>">Recalculate Distances</a>
			</strong>
		</td>
		<td>
			Select this to re-calcuate all the distances in your schedules and PIREPS. Useful for a import. 
			Accurate distances are required for some reports.
		</td>
	
	</tr>
	
	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetpirepcount');?>">Reset Pilot PIREP Count</a>
		</strong>
	</td>
	<td>Reset flight count totals for pilots. Use if the PIREP counts are off.</td>
	</tr>

	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetpilotpay');?>">Reset Pilot Payments</a>
		</strong>
	</td>
	<td>Scans the PIREPs and flighttimes, adjust pilot pay to the rate indicated for that pilot in that PIREP.</td>
	</tr>
	
	<tr>
	
	<!--<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetpirepfinance');?>">Reset PIREP Finances</a>
		</strong>
	</td>
	<td>This resets financial data to existing PIREPS which do not have any. NOTICE! This will reset <strong>all</strong> of your PIREPS to the current finances (expenses, fuel prices, etc)</td>
	</tr>-->
	
	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetscheduleroute');?>">Reset cached Schedule routes</a>
		</strong>
	</td>
	<td>The details of a route are cached, this resets the cache (doesn't affect the entered route), for schedules.<br /></td>
	</tr>
	
	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/resetpireproute');?>">Reset cached PIREP routes</a>
		</strong>
	</td>
	<td>The details of a route are cached, this resets the cache (doesn't affect the entered route), for PIREPs. <br /></td>
	</tr>
	
	<tr>
		<td>
			<strong>
			<a href="<?php echo adminurl('/maintenance/clearcache');?>">Clear Cache</a>
			</strong>
		</td>
		<td>This deletes all cached data</td>
	</tr>
	
	<tr>
	<td><strong>
		<a href="<?php echo adminurl('/maintenance/optimizetables');?>">Optimize Tables</a>
		</strong>
	</td>
	<td>Optimize and reindex all of your tables. Good to do often.<br /></td>
	</tr>
</tbody>
</table>

<h3>Cron</h3>
<p>If you have the ability, it's best to setup a cron-job to run the maintenance script. The command to add is:</p>
<p>
	<input type="text" name="cron" style="padding: 5px; width: 400px; " value="php -f <?php echo SITE_ROOT?>admin/maintenance.php" />
</p>
<p>It's recommended to run this sometime between 2-6am (pick an artibrary time when you would least-likely have any flights). 
Also, remember to change in local.config.php, USE_CRON to true, so phpVMS doesn't try to automatically schedule these tasks to run.</p>
<p><strong>Note: </strong> If you setup the cron to run, it's recommended to also run it by hand once to ensure data is populated, unless you want to wait for it to run automatically.</p>