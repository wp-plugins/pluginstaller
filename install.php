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
    eregi('(.*)/plugins/([^/]*)/(.*)',$output[$output_pos],$parts);
    $final_pos = $dest_dir . $parts[2] . '/';
  }

  
  if ($final_pos != $dest_dir . '/') {
    $final_pos = pi_sanitize($final_pos);
  }
  
  $readme_dir = $final_pos;
  
  // Save update information:
  $fp = fopen($readme_dir . '.pi-update', 'w');
  fwrite($fp, $upd_info);
  fclose($fp);
  
  // Delete downloaded package (clean up):
  unlink($package);
  
  return "";
  
}  // perform_install()

// Recurse a directory with PHP files
function pi_recurse( $dir, &$output )
{
        if (is_dir($dir)) {
           if ($dh = opendir($dir)) {
               while (($file = readdir($dh)) !== false ) {
                        if( $file != "." && $file != ".." )
                        {
                                if( is_dir( $dir . $file ) )
                                {
                                        pi_recurse( $dir . $file . "/" , $output);
                                }
                                else
                                {
                                 	if (strpos($file, '.php')) {
                                        array_push($output, $dir . $file);
                                    }
                                }
                        }
               }
               closedir($dh);
           }
        }
}

// Move a directory stucture
function dirmv($source, $dest, $overwrite = false, $funcloc = NULL){

  if(is_null($funcloc)){
    $dest .= '/' . strrev(substr(strrev($source), 0, strpos(strrev($source), '/')));
    $funcloc = '/';
  }

  if(!is_dir($dest . $funcloc))
    mkdir($dest . $funcloc); // make subdirectory before subdirectory is copied

  if($handle = opendir($source . $funcloc)){ // if the folder exploration is sucsessful, continue
    while(false !== ($file = readdir($handle))){ // as long as storing the next file to $file is successful, continue
      if($file != '.' && $file != '..'){
        $path  = $source . $funcloc . $file;
        $path2 = $dest . $funcloc . $file;

        if(is_file($path)){
          if(!is_file($path2)){
            if(!@rename($path, $path2)){
              echo '<font color="red">File ('.$path.') could not be moved, likely a permissions problem.</font>';
            }
          } elseif($overwrite){
            if(!@unlink($path2)){
              echo 'Unable to overwrite file ("'.$path2.'"), likely to be a permissions problem.';
            } else
              if(!@rename($path, $path2)){
                echo '<font color="red">File ('.$path.') could not be moved while overwritting, likely a permissions problem.</font>';
              }
          }
        } elseif(is_dir($path)){
          dirmv($source, $dest, $overwrite, $funcloc . $file . '/'); //recurse!
          rmdir($path);
        }
      }
    }
    closedir($handle);
  }
} // end of dirmv()


// Sanitize plugin 
function pi_sanitize($position) {
  
  // Parse for PHP files
  $pfiles = array();
  pi_recurse($position, $pfiles);
  
  // Look for the main plugin file:
  foreach ($pfiles as $filename) {
    $plugin_cfile = file_get_contents($filename);
    if (eregi('lugin Name',$plugin_cfile)) {
      $main_file = $filename;
      break;
    }
    
  }
  // Did the position change?
  $mfparts = explode('/',$main_file);
  $mfdir = $mfparts[count($mfparts) - 2];
  $pparts = explode('/',$position);
  $pdir = $pparts[count($pparts) - 2];
  if ($mfdir != $pdir) {
    // move main files to new dir:
    $o_mfdir = substr($main_file, 0, strrpos($main_file, '/'));
    dirmv($o_mfdir, ABSPATH . PLUGINDIR);
    // move files from original folder to new dir:
    dirmv($position, ABSPATH . PLUGINDIR . '/' . $mfdir);
    // delete original folder
    rmdir($position);
    return ABSPATH . PLUGINDIR . '/' . $mfdir;
  }else{
    // Nothing to be done:
    return $position;
  }
  // C:\Users\Henning Schaefer\Desktop\sphere-related-content.zip
}

?>