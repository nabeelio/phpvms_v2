<?php
/**
 * Codon PHP Framework
 *	www.nsslive.net/codon
 * Software License Agreement (BSD License)
 *
 * Copyright (c) 2008 Nabeel Shahzad, nsslive.net
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2.  Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Nabeel Shahzad 
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.nsslive.net/codon
 * @license BSD License
 * @package codon_core
 */
 
class TemplateSet 
{
	public $template_path = '';
	public $enable_caching = false;
	public $cache_timeout;
	
	public $tpl_ext = '.tpl';
	protected $vars = array();
	
	/*public function __construct($path='')
	{
		if($path!='')
			$this->Set($path);
	}*/
	
	/**
	 * Set the default path to look for the templates
	 * 
	 * @param string $path Path to the templates folder
	 */
	public function setTemplatePath($path)
	{
		# Remove trailing directory separator
		$len = strlen($path);
		if($path[$len-1] == DS)
			$path=substr($path, 0, $len-1);
			
		$this->template_path = $path;
	}
	
	public function enableCaching($bool=true)
	{
		$this->enable_caching = $bool;
	}
	
	/**
	 * Clear all variables
	 */
	public function clearVars()
	{
		$this->vars = array();
	}
	
	/**
	 * Set a variable to the template, call in the template
	 * as $name
	 * 
	 * @param mixed $name Variable name
	 * @param mixed $value Variable value
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}
	
	/**
	 * Alias to self::ShowTemplate();
	 * 
	 * @param string $tpl_name Template name including extention
	 * @param bool $checkskin Check the skin folder or not
	 */
	public function show($tpl_name, $checkskin=true, $force_base=false)
	{
		return $this->ShowTemplate($tpl_name, $checkskin, $force_base);
	}
	
	public function showVars()
	{
		extract($this->vars, EXTR_OVERWRITE);

		echo '<pre>';
		print_r(get_defined_vars());
		echo '</pre>';
	}
	
	
	/**
	 * Show a template on screen, checks to see if the
	 *	template is cached or not as well. To return a template,
	 *  use self::GetTemplate(); this ends up calling GetTemplate()
	 *	if the cache is empty or disabled
	 *
	 * @param string $tpl_name Template name including extention
	 * @param bool $checkskin Check the skin folder or not
	 * @return mixed This is the return value description
	 *
	 */
	public function showTemplate($tpl_name, $checkskin=true, $force_base=false)
	{		
		if($this->enable_caching == true)
		{
			$cached_file = CACHE_PATH . DS . $tpl_name;
			
			// The cache has expired
			if((time() - filemtime($cached_file)) > ($this->cache_timeout*3600))
			{
				unlink($cached_file);
				
				$tpl_output = $this->GetTemplate($tpl_name, true, $checkskin, $force_base);
				
				echo $tpl_output;
				
				//cache it into the storage file
				if($this->enable_caching == true)
				{
					$fp = fopen($cached_file, 'w');
					fwrite($fp, $tpl_output, strlen($tpl_output));
					fclose($fp);			
				}
			}
			else // Cache not expired, so just include that cache
			{
				@include $cached_file;
			}
		}
		else
		{
			return $this->getTemplate($tpl_name, false, $checkskin, $force_base);
		}
	}
	
	
	/**
	 * Alias to $this->GetTemplate()
	 *
	 * @param string $tpl_name Template to return (with extension)
	 * @param bool $ret Return the template or output it on the screen
	 * @param bool $checkskin Check the active skin folder for the template first
	 * @return mixed Returns template text is $ret is true
	 *
	 */
	public function get($tpl_name, $ret=false, $checkskin=true, $force_base=false)
	{
		return $this->getTemplate($tpl_name, $ret, $checkskin, $force_base);
	}
	
	/**
	 * GetTemplate
	 *  This gets the actual template data from a template, and fills
	 *	in the variables
	 *
	 * @param string $tpl_name Template to return (with extension)
	 * @param bool $ret Return the template or output it on the screen
	 * @param bool $checkskin Check the active skin folder for the template first
	 * @param bool $force_base Force it to read from the base template dir
	 * @return mixed Returns template text is $ret is true
	 *
	 */
	public function getTemplate($tpl_name, $ret=false, $checkskin=true, $force_base=false)
	{
		/* See if the file has been over-rided in the skin directory
		 */
		if(strstr($tpl_name, $this->tpl_ext) === false)
		{
			$tpl_name .= $this->tpl_ext;
		}

		$tpl_path = $this->getTemplatePathDefaultLanguage($tpl_name, $checkskin, $force_base);
		if(!$tpl_path)
		{
			trigger_error('The template file "'.$tpl_name.'" doesn\'t exist');
			return;
		}

		extract($this->vars, EXTR_OVERWRITE);
		
		ob_start();
		include $tpl_path; 
		$cont = ob_get_contents();
		ob_end_clean();
		
		# Check if we wanna return
		if($ret==true)		
			return $cont;
			
		echo $cont;
	}
	
	/**
	 * getTemplatePathDefaultLanguage
	 *  This function tries to obtain a template path with the usual language. The first choice
	 *  is the language set in the Lang class, which can be either set from SITE_LANGUAGE or,
	 *  if a pilot is logged in, from the profile.
	 *
	 * @param string $tpl_name Template to return (with extension)
	 * @param bool $checkskin Check the active skin folder for the template first
	 * @param bool $force_base Force it to read from the base template dir
	 * @return mixed Returns a template path if found, or false if not found.
	 */
	public function getTemplatePathDefaultLanguage($tpl_name, $checkskin=true, $force_base=false)
	{
		$tpl_path = $this->getTemplatePathWithLanguage($tpl_name, Lang::$language, $checkskin, $force_base);
		if(!$tpl_path) {
			$tpl_path = $this->getTemplatePathWithLanguage($tpl_name, Config::Get('SITE_BASE_LANGUAGE'), $checkskin, $force_base);
		}
		return $tpl_path;
	}
	
	/**
	 * getTemplatePathWithLanguage
	 *  This function tries to find a template for the given language.
	 *
	 * @param string $tpl_name Template to return (with extension)
	 * @param bool $checkskin Check the active skin folder for the template first
	 * @param bool $force_base Force it to read from the base template dir
	 * @return mixed Returns a template path if found, or false if not found.
	 */
	public function getTemplatePathWithLanguage($tpl_name, $lang, $checkskin=true, $force_base=false)
	{
		if($force_base === true) 
		{
			$old_tpl = $this->template_path;
			$this->template_path = Config::Get('BASE_TEMPLATE_PATH');
			
			if($checkskin === true)
			{
				if(defined('SKINS_PATH') && file_exists(SKINS_PATH . DS . $lang . DS . $tpl_name))
				{
					$tpl_path = SKINS_PATH . DS . $lang . DS . $tpl_name;
				}
				else
				{
					$tpl_path = $this->template_path . DS . $lang . DS . $tpl_name;
				}
			}
		}

		if((!defined('ADMIN_PANEL') || $force_base == true) && $checkskin == true)
		{
			if(defined('SKINS_PATH') && file_exists(SKINS_PATH . DS . $lang . DS . $tpl_name))
			{
				$tpl_path = SKINS_PATH . DS . $lang . DS . $tpl_name;
			}
			else
			{
				$tpl_path = $this->template_path . DS . $lang . DS . $tpl_name;
			}
		}
		else
		{
			$tpl_path = $this->template_path . DS . $lang . DS . $tpl_name;
		}
		
		if($force_base) 
		{
			$this->template_path = $old_tpl;
		}
		
		if(file_exists($tpl_path))
			return $tpl_path;
		else
			return false;
	}

	/**
	 * ShowModule
	 *	This is an alias to MainController::Run(); calls a function
	 *	in a module. Returns back whatever the called function returns
	 *
	 * @param string $ModuleName Module name to call
	 * @param string $MethodName Function which to call in the module
	 * @return mixed This is the return value description
	 *
	 */
	public function showModule($ModuleName, $MethodName='ShowTemplate')
	{
		return MainController::Run($ModuleName, $MethodName);
	}
}