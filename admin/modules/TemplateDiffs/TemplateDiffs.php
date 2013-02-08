<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */
 
class TemplateDiffs extends CodonModule
{
	
	public function index()
	{
		echo '<h3>Template Diff Viewer</h3>
			<p>This lists any templates which are found to have changes (based on the current selected skin)</p>
			<ul>';
		$dir_iterator = new RecursiveDirectoryIterator(Config::Get('BASE_TEMPLATE_PATH').DS.$file);
		$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

		$custom_found = false;
		foreach ($iterator as $file) 
		{		
			if($file->getType() != 'file')
			{
				continue;
			}
			
			$filename = $file->getBaseName();
			
			$custom_path =  SKINS_PATH.DS.$filename;
			
			if(file_exists($custom_path))
			{
				$custom_found = true;
				echo '<li><strong>'.$filename.':</strong> <a href="'.adminurl('/templatediffs/showdiff/'.$filename).'">View changes</a></li>';
			}
			else
			{
				//echo 'No changes<br>';
			}
		}
		
		if($custom_found === false)
		{
			echo '<li>No customized templates found in this skin\'s folder</li>';
		}
		
		echo '</ul>';
	}
	
	
	public function showdiff($file)
	{
		$this->render('diff_showdiff.php');
				
		# Paths
		$base = Config::Get('BASE_TEMPLATE_PATH').DS.$file;
		$custom = SKINS_PATH.DS.$file;
		
		if(!file_exists($custom))
		{
			echo '<strong>Error: </strong>The file "'.$file.'" has not been modified';
			return;
		}
		
		# Load the diffs
		include CORE_LIB_PATH.DS.'diff'.DS.'diff.php';
		$diff = new filediff();
		$text = $diff->inline($base, $custom, 2);
		echo count($diff->changes).' changes';
		echo $text;
	}
}