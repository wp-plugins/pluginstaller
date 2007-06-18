<?php
/*
Plugin Name: PlugInstaller
Plugin URI: http://henning.imaginemore.de/pluginstaller/
Description: Easy (un)installation of new plugins directly from the admin interface
Version: 0.1.6
Author: Henning Schaefer
Author URI: http://henning.imaginemore.de
*/

/*  Copyright 2007  Henning Schaefer  (email : henning.schaefer@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// cURL fallback routine for fopen()
function uopen ( $filename , $mode ) {
		 $fp = @fopen ( $filename , $mode );
		 if ( !$fp ) {
		 	$fp = @popen ( "curl $filename", "r");
			if ( !$fp ) {
		       return false;
		 	}
		 }
		 return $fp;
}

// cURL fallback routine for fclose()
function uclose ( $fp ) {
		 if ( !@fclose( $fp ) ) {
		 	return @pclose( $fp );
		 }
		 return true;
}

// Check for updates:
function pi_check_for_update() {
  $current_version = '0.1.6';
  $fp = uopen('http://henning.imaginemore.de/pi-version.txt','r');
  $available_version = fgets($fp);
  uclose($fp);
  if ($current_version != $available_version) {
  ?>
  <div id="message" class="updated fade"><p>A newer version of plugInstaller is available! <a href='http://henning.imaginemore.de/pluginstaller/' target='_blank'>Update...</a></p></div>
  <?php
  }
}

// Useful links section on admin page:
function pi_useful_links() {
?>
<h2>Useful links</h2>
Browse the <a href='http://wordpress.org/extend/plugins/' target='_blank'>WordPress.org plugin repository</a>
for plugins or look around at the <a href='http://dev.wp-plugins.org/' target='_blank'>wp-plugins.org repository wiki</a>.<br><br>
To directly install a plugin from the WordPress.org plugin repository, change to the desired plugin's detail page, right-click the "download" button
and select "Copy link location". Then, right-click the plugin installer's URL input box, select "paste" and hit the install button.<br><br>
Visit the <a href='http://henning.imaginemore.de' target='_blank'>author's blog</a> for comments, news and updates.
<?php
}

//Manual section on admin page:
function pi_manual() {
?>
<h2>Instructions</h2>

<ul>
<li>To install a plugin you can either upload a file from your local computer or automatically download a package file from the web and have it installed.</li>
<li><b>Note:</b> The PlugInstaller <b>does not work with Windows</b>, as there are no commandline tools to unpack archives.</li>
<li>PlugInstaller currently supports .zip and .tar.gz archives.</li>
<li>To <b>upload a plugin archive from your computer</b>, browse to the desired file by clicking on "browse" next to the "Select file" input box and hit "install".</li>
<li>To <b>install a plugin from a web location (url)</b>, enter the url of the desired plugin into the "Enter URL" input box and hit "install".</li>
<li>The file will then be uploaded/downloaded and automatically be installed.</li>
<li>After successful installation, visit the "Plugins" page to activate the newly installed plugin.</li>
<li>Please be patient: Uploading/Downloading may take a while depending on file size.</li>
</ul>

<h3>Possible problems</h3>
<ul>
<li>You may run into problems when unpacking the up-/downloaded package. Please make sure that "tar", "gzip" and "unzip" are installed on your system and available within the search path.</li>
<li>If you experience problems during upload or download of a package, make sure you have set the plugins directory to world-writable.</li>
</ul>

<?php
}

// Installer section:
function pi_install_section() {
?>
          <h2>Install plugin</h2>
          
          You can install a plugin by uploading a file from your computer or download and install the plugin directly from a URL (e.g. from a plugin repository).<br><br>
          
          <form name="fromurl" method="post" enctype="multipart/form-data" action="<?=$location ?>">
                Enter URL: <input name="fileurl" type="text" />
                or select a file: <input name="filename" type="file" />
                <input type="submit" name='installfile' value="Install" />
                <input type="hidden" name="doinstall" value="1" />
          </form>
          <br><br>
          <?php
}

function pi_uninstall_section() {
?>
  		 <h2>Uninstall plugins</h2>
<?php

if ( get_option('active_plugins') )
	$current_plugins = get_option('active_plugins');  		 
  		 $plugins = get_plugins();

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn&#8217;t open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
?>
<table class="widefat plugins">
	<thead>
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th style="text-align: center"><?php _e('Version'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th style="text-align: center"<?php if ( current_user_can('edit_plugins') ) echo ' colspan="2"'; ?>><?php _e('Action'); ?></th>
	</tr>
	</thead>
<?php
	$style = '';

	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$plugin_data['show'] = false;
		} else {
			$toggle = "<a href='/wp-admin/plugins.php?page=".$_GET['page']."&uninstall=".$plugin_file."'>".__('Uninstall')."</a>";
			$plugin_data['show'] = true;
		}

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

		// Sanitize all displayed data
		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

		if ( $style != '' )
			$style = 'class="' . $style . '"';
		if ($plugin_data['show']) {
		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'><p>{$plugin_data['Description']} <cite>".sprintf(__('By %s'), $plugin_data['Author']).".</cite></p></td>
		<td class='togl'>$toggle</td>
	</tr>";
	  }
	}
?>

</table>  		 

<br><br>

<b>Hint:</b> Only deactivated plugins may be uninstalled. Active plugins will not appear in the list. You may deactivate unused plugins <a href='/wp-admin/plugins.php'>here</a>.<br><br>
  		 
<?php

}

}

// Parse Wordpress Readme files:

function pi_parse_readme($readme) {
  // H1
  $readme = eregi_replace('===([^=]*)===','<h2>\\1</h2>',$readme);
  // H2
  $readme = eregi_replace('==([^=]*)==','<h3>\\1</h3>',$readme);
  // H3
  $readme = eregi_replace('=([^=]*)=','<h4>\\1</h4>',$readme);
  // Links
  $readme = eregi_replace('\[([^]]*)\]\(([^)]*)\)',"<a target='_blank' href='\\2'>\\1</a>",$readme);
  // Auto-URL:
  $readme = preg_replace("/([^'])(http:\/\/|ftp:\/\/)([^\s,]*)/i","$1<a target='_blank' href='$2$3'>$2$3</a>",$readme);
  return $readme;
}

// Display readme function:

function pi_display_readme($filename) {
  echo "<h3>Readme file</h3>";
  echo "Carefully read the following information that explains how to use your new plugin:<br><br>";
  echo "<div style='overflow: auto; height: 300px; border: 1px solid #000000;'>";
    if (stristr($filename, 'html') !== false) {
      echo file_get_contents($filename);
    }else{
      echo nl2br(pi_parse_readme(file_get_contents($filename)));
    }
  echo "</div>";
}

// Plugin install admin page:

function pi_install_page() {
  global $readme_dir;

$doinstall = $_POST['doinstall'];
$installurl = $_POST['installurl'];
$installfile = $_POST['installfile'];
$fileurl = $_POST['fileurl'];
$filename = $_POST['filename'];
$uninstall = $_GET['uninstall'];

pi_check_for_update();

?>
  		<div class="wrap">
          
          <?php
          
          if (isset($uninstall)) {
            echo uninstall_plugin($uninstall);
            return true;
          }
          
          if (isset($doinstall)) {
          
            if ($fileurl != "") {
              $result = install_from_url($fileurl);
            }elseif ($_FILES['filename']['name'] != ""){
              $result = install_from_file();
            }else{
              $result = "Please specify a filename or a download URL!";
            }
            
            if ($result == "") {
              ?>
              
              <h3>Installation successfully completed</h3>
              Your plugin has been successfully installed. Visit the <a href='/wp-admin/plugins.php'>plugins page</a> to activate your plugin.<br><br>
              
              <?php
                // Readme file available?
                if ($readme_dir != '') {
                  // Check for readme files
                  if (file_exists($readme_dir . 'readme.html')) {
                    pi_display_readme($readme_dir . 'readme.html');
                  }elseif (file_exists($readme_dir . 'README.html')) {
                    pi_display_readme($readme_dir . 'README.html');
                  }elseif (file_exists($readme_dir . 'README.HTML')) {
                    pi_display_readme($readme_dir . 'README.HTML');
                  }elseif (file_exists($readme_dir . 'Readme.html')) {
                    pi_display_readme($readme_dir . 'Readme.html');
                  }elseif (file_exists($readme_dir . 'readme.txt')) {
                    pi_display_readme($readme_dir . 'readme.txt');
                  }elseif (file_exists($readme_dir . 'README.txt')) {
                    pi_display_readme($readme_dir . 'README.txt');
                  }elseif (file_exists($readme_dir . 'README.TXT')) {
                    pi_display_readme($readme_dir . 'README.txt');
                  }elseif (file_exists($readme_dir . 'Readme.txt')) {
                    pi_display_readme($readme_dir . 'Readme.txt');
                  }elseif (file_exists($readme_dir . 'README')) {
                    pi_display_readme($readme_dir . 'README');
                  }
                }
                
              ?>
              
              <?php
            }else{
              ?>
              
              <h3>An error has occured</h3>
              <?=$result?><br><br>
              <a href='<?=$location?>'>Go back and try again</a>
              
              <?php
            }
          
          }else{
            pi_install_section();
          	pi_uninstall_section();
            pi_useful_links();
            echo "<br><br>";
            pi_manual();
          
            }
            
          ?>
          
        </div>
<?php
} // pi_admin_page()

function uninstall_plugin($filename) {

  // Get Subdirectory information for plugin:
  $parts = Array();
  eregi('(.*)/(.*)',$filename,$parts);
  $subdir = $parts[1] . '/';
  $file = $parts[2];
  // Check for "subdirred" plugin:
  if ($subdir == '/') {
    $message = "<h3>A problem has occured</h3>";
    $message .= "PlugInstaller cannot uninstall this plugin, as it has not been installed into a subdirectory!<br><br>";
    $message .= "<a href='?page=".$_GET["page"]."'>Back to PlugInstaller</a>";
    return $message;
  }
  
  // Check for other plugins within that directory:
  $dest_dir = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME']) - 20) . "wp-content/plugins/";
  $dp = opendir($dest_dir . $subdir);
  // Parse directory:
  $phpfiles = array();
  while ($dirfile = readdir($dp)) {
    if (filetype($dest_dir . $subdir . $dirfile) == "file") {
      if (strpos($dirfile, '.php') !== false) {
        if ($dirfile != $file) {
          array_push($phpfiles, $dirfile);
        }
      }
    }
  }
  closedir($dp);
  // Determine if those files are separate plugins:
  $delete_all = true;
  if (count($phpfiles) != 0) {
    $plugin_names = array();
    foreach($phpfiles as $phpfile) {
      $fp = uopen($dest_dir . $subdir . $phpfile,"r");
      while ($line = fgets($fp)) {
        if (eregi("Plugin Name", $line) !== false) {
          array_push($plugin_names, $line);
          $delete_all = false;
        }
      }
      uclose($fp);
    }
  }
  
  // Output skipped plugins
  if (!$delete_all) {
    $skipped_plugins = '';
    foreach ($plugin_names as $name) {
      $skipped_plugins = substr(stristr($name, ':'),1);
    }
  }
  
  
  if ($delete_all) {
    $output = array();
    exec("rm -r ".$dest_dir . $subdir,$output,$result);
    if ($result > 0) {
      $message = "<h3>A problem has occured</h3>";
      $message .= "The plugin directory could not be deleted. Check for the appropriate rights in your <i>/wp-content/plugins</i> directory!<br><br>";
      $message .= "<a href='?page=".$_GET["page"]."'>Go back and try again</a>";
    }else{
      $message = "<h3>Uninstallation successfully completed</h3>";
      $message .= "The plugin has been successfully uninstalled.<br><br>";
      $message .= "<a href='?page=".$_GET["page"]."'>Back to PlugInstaller</a>";
    }
  }else{
    if (!unlink($dest_dir . $subdir . $file)) {
      $message = "<h3>A problem has occured</h3>";
      $message .= "The plugin file could not be deleted. Check for the appropriate rights in your <i>/wp-content/plugins</i> directory!<br><br>";
      $message .= "<a href='?page=".$_GET["page"]."'>Go back and try again</a>";
    }else{
      $message = "<h3>Uninstallation successfully completed</h3>";
      $message .= "The plugin has been successfully uninstalled.<br><br>The following plugins that were installed in the same directory have been retained: <i>".$skipped_plugins."</i><br><br>";
      $message .= "<a href='?page=".$_GET["page"]."'>Back to PlugInstaller</a>";
    }
  }
  // END DEBUG

  return $message;
}

function install_from_file() {
  // Check file upload:
  if ($_FILES['filename']['name'] == "") {
    return "Please select a file to be uploaded!";
  }
  
  // Determine destination directory
  $dest_dir = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME']) - 20) . "wp-content/plugins/";
  $dest_file = $dest_dir . $_FILES['filename']['name'];
  
  // Move file to destination directory
  if (!move_uploaded_file($_FILES['filename']['tmp_name'], $dest_file)) {
    return "A problem has occured while processing your upladed file!";
  }
  
  return perform_install($dest_file);
} // install_from_file()

function install_from_url($url) {
  // Check input:
  if ($url == "") {
    return "Please select a URL to be downloaded!";
  }
  
  // Determine file type (zip/tar.gz)
  $ext = "";
  if (strpos($url,'.zip')) {
    $ext = ".zip";
  }
  if (strpos($url,'.tar.gz')) {
    $ext = ".tar.gz";
  }
  if ($ext == "") {
    $ext = ".pitmp";
  }

  // Determine destination directory
  $dest_dir = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME']) - 20) . "wp-content/plugins/";
  $dest_file = $dest_dir . "plugin".$ext;
  
  // Download file
  $file = file_get_contents($url);
  $df = uopen($dest_file, 'w');
  if (!fwrite($df, $file)) {
    return "A problem has occured while trying to download your file!";
  }
  
  return perform_install($dest_file);
} // install_from_url()

function perform_install($package) {
  global $readme_dir;
  
  $output = Array();
  $dest_dir = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME']) - 20) . "wp-content/plugins/";
  
  // Determine file type (zip/tar.gz)
  $cmd = "";
  // Guess file type?
  if (strpos($package,'.pitmp')) {
    $fp = uopen($package, "r");
    if (fread($fp, 2) == 'PK') {
      // Guessing a ZIP file
      uclose($fp);
      rename($package, $package . '.zip');
      $package .= '.zip';
    }else{
      // Guessing a TGZ file
      uclose($fp);
      rename($package, $package . '.tar.gz');
      $package .= '.tar.gz';
    }
  }
  
  if (strpos($package,'.zip')) {
    $cmd = "unzip -d $dest_dir ";
    $output_pos = 2;
  }
  if (strpos($package,'.tar.gz')) {
    $cmd = "tar --directory=$dest_dir -xvzf ";
    $output_pos = 0;
  }
  
  // Unpack file:
  exec($cmd.$package,$output,$result);
  
  if ($result > 0) {
    // Delete downloaded package (clean up):
    unlink($package);
    return "A problem has occured while unpacking your plugin package!";
  }
  
  // get Readme file location:
  if ($output_pos == 0) {
    $readme_dir = $dest_dir . $output[$output_pos];
  }else{
    $parts = array();
    eregi('(.*)/plugins/(.*)/(.*)',$output[$output_pos],$parts);
    $readme_dir = $dest_dir . $parts[2] . '/';
  }
  
  // Delete downloaded package (clean up):
  unlink($package);
  
  return "";
  
}  // perform_install()

// Create plugin admin menu
function pi_add_menu() {
        add_submenu_page('plugins.php','Plugin installer', 'Install/Uninstall', 'activate_plugins', __FILE__, 'pi_install_page'); 
} // pi_add_menu()

// Replace old plugins page
function pi_replace_plugins_page() {
   if (($_SERVER['SCRIPT_NAME'] == '/wp-admin/plugins.php') && ($_GET['page'] == '')) {
     include(substr(__FILE__,0,strlen(__FILE__) - 12).'plugins.php');
     die();
   }
}

// Register plugin admin page:
add_action('admin_menu', 'pi_add_menu');
add_action('admin_notices', 'pi_replace_plugins_page');
?>