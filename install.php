<?php

function install_from_file() {
  // Check file upload:
  if ($_FILES['filename']['name'] == "") {
    return "Please select a file to be uploaded!";
  }
  
  // Determine destination directory
  $dest_dir = ABSPATH . PLUGINDIR . '/';
  $dest_file = $dest_dir . $_FILES['filename']['name'];
  
  // Move file to destination directory
  if (!move_uploaded_file($_FILES['filename']['tmp_name'], $dest_file)) {
    return "A problem has occured while processing your upladed file!";
  }
  
  return perform_install($dest_file);
} // install_from_file()

function install_from_url($url) {
  global $upd_info;
  // Check input:
  if ($url == "") {
    return "Please select a URL to be downloaded!";
  }
  
  // Determine file type (zip/tar.gz)
  $ext = "";
  if (strpos($url,'.zip')) {
    $ext = ".zip";
    // Get SVN Name
    $upd_info = substr(trim($url), strrpos(trim($url),'/') + 1, strlen(trim($url)) - 4);
    // Remove Version info
    $upd_info = substr($upd_info, 0, strpos($upd_info,'.'));
  }
  if (strpos($url,'.tar.gz')) {
    $ext = ".tar.gz";
    $upd_info = substr(trim($url), strrpos(trim($url),'/') + 1, strlen(trim($url)) - 7);
    $upd_info = substr($upd_info, 0, strpos($upd_info,'.'));
  }
  if ($ext == "") {
    $ext = ".pitmp";
  }

  // Determine destination directory
  $dest_dir = ABSPATH . PLUGINDIR .'/';
  $dest_file = $dest_dir . "plugin".$ext;
  
  // Download file
  $file = u_get_contents($url);
  $df = uopen($dest_file, 'w');
  if (!fwrite($df, $file)) {
    return "A problem has occured while trying to download your file!";
  }
  
  return perform_install($dest_file);
} // install_from_url()

function perform_install($package) {
  global $readme_dir;
  global $upd_info;
  global $main_file;
  
  $output = Array();
  $dest_dir = ABSPATH . PLUGINDIR . '/';
  
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
    $final_pos = $dest_dir . $output[$output_pos];
  }else{
    $parts = array();
    eregi('(.*)/plugins/(.*)/(.*)',$output[$output_pos],$parts);
    $final_pos = $dest_dir . $parts[2] . '/';
  }
  
  $final_pos = pi_sanitize($final_pos);
  
  $readme_dir = $final_pos;
  
  // Save update information:
  $fp = fopen($readme_dir . '.pi-update', 'w');
  fwrite($fp, $upd_info."\n".$main_file."\n");
  fclose($fp);
  
  // Delete downloaded package (clean up):
  unlink($package);
  
  return "";
  
}  // perform_install()


// Sanitize plugin 
function pi_sanitize($position) {
  global $main_file;
  //echo $position;
  return $position;
}

?>