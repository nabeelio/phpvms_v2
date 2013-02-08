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
 * @package module_admin_dashboard
 */

/**
 * This file handles any misc tasks that need to be done.
 * Loaded at the very end
 */

class Dashboard extends CodonModule {
    
    public function HTMLHead() {
        $this->set('sidebar', 'sidebar_dashboard.php');
    }

    public function index() {
        /* Dashboard.tpl calls the functions below
        */
        $this->CheckForUpdates();
        CentralData::send_vastats();

        $this->set('unexported_count', count(PIREPData::getReportsByExportStatus(false)));
        $this->render('dashboard.php');
    }

    public function pirepcounts() {
        # Create the chart
        //$reportcounts = '';
        $data = PIREPData::getIntervalDataByDays(array(), 30);

        if (!$data) {
            $data = array(); // so it doesn't error out
        }

        $bar_values = array();
        $bar_titles = array();
        foreach ($data as $val) {
            $bar_titles[] = $val->ym;
            $bar_values[] = floatval($val->total);
        }

        OFCharts::add_data_set($bar_titles, $bar_values);
        echo OFCharts::create_line_graph('Past 30 days PIREPS');
    }

    public function about() {
        $this->render('core_about.php');
    }

    public function CheckInstallFolder() {
        if (file_exists(SITE_ROOT . '/install')) {
            $this->set('message', 'The install folder still exists!! This poses a security risk. Please delete it immediately');
            $this->render('core_error.php');
        }
    }

    /**
     * Show the notification that an update is available
     */
    public function CheckForUpdates() {
        
        if (Config::Get('CHECK_RELEASE_VERSION') == true) {
            
            $key = 'PHPVMS_LATEST_VERSION';
            $feed  = CodonCache::read($key);
            
            if ($feed === false) {
                
                $url = Config::Get('PHPVMS_API_SERVER') . '/version/get/json/';

                # Default to fopen(), if that fails it'll use CURL
                $file = new CodonWebService();
                $contents = @$file->get($url);
                                
                # Something should have been returned
                if ($contents == '') {
                    $msg = '<br /><b>Error:</b> The phpVMS update server could not be contacted. 
    						Check to make sure allow_url_fopen is set to ON in your php.ini, or 
    						that the cURL module is installed (contact your host).';
    
                    $this->set('latestnews', $msg);
                    return;
                }
                
                #$xml = @simplexml_load_string($contents);
                $message = json_decode($contents);                
                
                if (!$message) {
                    $msg = '<br /><b>Error:</b> There was an error retrieving news. It may be temporary.
    						Check to make sure allow_url_fopen is set to ON in your php.ini, or 
    						that the cURL module is installed (contact your host).';
    
                    $this->set('latestnews', $msg);
                    return;
                }
                
                CodonCache::write($key, $message, 'medium_well');
            }
            
            if (Config::Get('CHECK_BETA_VERSION') == true) {
                $latest_version = $message->betaversion;
            } else {
                $latest_version = $message->version;
            }

            # GET THE VERSION THAT'S THE LATEST AVAILABLE
            preg_match('/^[v]?(.*)-([0-9]*)-(.*)/', $latest_version, $matches);
            list($FULL_VERSION_STRING, $full_version, $revision_count, $hash) = $matches;
            
            preg_match('/([0-9]*)\.([0-9]*)\.([0-9]*)/', $full_version, $matches);
            list($full, $major, $minor, $revision) = $matches;
            $latest_version = $major.$minor.($revision + $revision_count);
            
            # GET THE CURRENT VERSION INFO INSTALLED
            $installed_version = PHPVMS_VERSION;
            preg_match('/^[v]?(.*)-([0-9]*)-(.*)/', $installed_version, $matches);
            list($FULL_VERSION_STRING, $full_version, $revision_count, $hash) = $matches;
            
            preg_match('/([0-9]*)\.([0-9]*)\.([0-9]*)/', $full_version, $matches);
            list($full, $major, $minor, $revision) = $matches;
            $installed_version = $major.$minor.($revision + $revision_count);
            
            #echo "CURRVERSION : $installed_version<br>AVAILVERSION: $latest_version<br>";
            
            if ($installed_version < $latest_version) {
                if (Config::Get('CHECK_BETA_VERSION') == true) {
                    $this->set('message', 'Beta version ' . $message->betaversion . ' is available for download!');
                } else {
                    $this->set('message', 'Version ' . $message->version . ' is available for download! Please update ASAP');
                }

                $this->set('updateinfo', Template::GetTemplate('core_error.php', true)); 
            }

            /* Retrieve latest news from Feedburner RSS, in case the phpVMS site is down
            */
            $key = 'PHPVMS_NEWS_FEED';
            $feed_contents  = CodonCache::read($key);
            if ($feed_contents === false) {
                $feed_contents = $file->get(Config::Get('PHPVMS_NEWS_FEED'));
                CodonCache::write($key, $feed_contents, 'medium_well');
            }
            
            $i = 1;
            $count = 5; 
            $contents = '';
            $feed = simplexml_load_string($feed_contents);
            foreach ($feed->channel->item as $news) {
                                
                $news_content = (string) $news->description;
                $guid = (string) $news->guid;
                $title = (string) $news->title;
                $date_posted = str_replace('-0400', '', (string) $news->pubDate);

                $contents .= "<div class=\"newsitem\">";
                $contents .= '<a href="'.$guid.'"><b>'.$title.'</b></a><br />';
                $contents .= $news_content;
                $contents .= '<br /><br />Posted: '.$date_posted;
                $contents .= '</div>';

                if ($i++ == $count)
                    break;
            }

            $this->set('phpvms_news', $contents);

            if (Config::Get('VACENTRAL_ENABLED') == true) {
                /* Get the latest vaCentral News */
                $contents = $file->get(Config::Get('VACENTRAL_NEWS_FEED'));
                $feed = simplexml_load_string($contents);
                $contents = '';

                $i = 1;
                $count = 5; // Show the last 5
                foreach ($feed->channel->item as $news) {
                    $news_content = (string )$news->description;
                    $date_posted = str_replace('-0400', '', (string )$news->pubDate);

                    $contents .= "<div class=\"newsitem\">
									<b>{$news->title}</b> {$news_content}
									<br /><br />
									Posted: {$date_posted}
								</div>";

                    if ($i++ == $count)
                        break;
                }

                $this->set('vacentral_news', $contents);
            }
        }
    }
}
