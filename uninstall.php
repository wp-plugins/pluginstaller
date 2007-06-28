<?php

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
  $dest_dir = ABSPATH . PLUGINDIR . '/';
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
    }else{
      $message = "The plugin has been successfully uninstalled.";
    }
  }else{
    if (!unlink($dest_dir . $subdir . $file)) {
      $message = "<h3>A problem has occured</h3>";
      $message .= "The plugin file could not be deleted. Check for the appropriate rights in your <i>/wp-content/plugins</i> directory!<br><br>";
    }else{
      $message = "The plugin has been successfully uninstalled.<br><br>The following plugins that were installed in the same directory have been retained: <i>".$skipped_plugins."</i>";
    }
  }
  // END DEBUG

  return $message;
}

?>