<?php

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

// cURL fallback routine for file_get_contents()
function u_get_contents ( $filename ) {
         if ($file = @file_get_contents ( $filename )) {
         } else {
        	$curl = curl_init( $filename );
            curl_setopt($curl, CURLOPT_HEADER, 0);  // ignore any headers
            ob_start();  // use output buffering so the contents don't get sent directly to the browser
            curl_exec($curl);  // get the file
            curl_close($curl);
            $file = ob_get_contents();  // save the contents of the file into $file
            ob_end_clean();  // turn output buffering back off		 
         }
         return $file;
}

// Check for PlugInstaller updates:
function pi_check_for_update() {
  $current_version = '0.1.7';
  $fp = uopen('http://henning.imaginemore.de/pi-version.txt','r');
  $available_version = fgets($fp);
  uclose($fp);
  if ($current_version != $available_version) {
  ?>
  <div id="message" class="updated fade"><p>A newer version of plugInstaller is available! <a href='http://henning.imaginemore.de/pluginstaller/' target='_blank'>Update...</a></p></div>
  <?php
  }
}

?>