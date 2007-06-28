<?php

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

?>