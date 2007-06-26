<?php

//Manual section on admin page:
function pi_manual() {
?>

You can install a plugin by uploading a file from your computer or download and install the plugin directly from a URL (e.g. from a plugin repository).        

<h3>Instructions</h3>

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

Visit the <a href='http://henning.imaginemore.de' target='_blank'>author's blog</a> for comments, news and updates.

<?php
}

// Installer section:
function pi_install_section() {
?>       
          <form name="fromurl" method="post" enctype="multipart/form-data" action="<?=$location?>">
                Enter URL: <input name="fileurl" type="text" />
                or select a file: <input name="filename" type="file" />
                <input type="submit" class='button' name='installfile' value="Install" />
                <input type="hidden" name="doinstall" value="1" />
          </form>
          <br />
          <?php
}

// Plugin install admin page:

function pi_install() {
  global $readme_dir;

$doinstall = $_POST['doinstall'];
$installurl = $_POST['installurl'];
$installfile = $_POST['installfile'];
$fileurl = $_POST['fileurl'];
$filename = $_POST['filename'];
$uninstall = $_GET['uninstall'];

?>
          
          <?php
          
          if (isset($uninstall)) {
            echo '<div id="message" class="updated fade"><p>'.uninstall_plugin($uninstall).'</p></div>';
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
              
              <div id="message" class="updated fade"><p>Your plugin has been successfully installed. Please view the plugin's readme file before activating it!</p></div>
              
              <?php
            }else{
              ?>
              
              <h3>An error has occured</h3>
              <?=$result?><br><br>
              <a href='<?=$location?>'>Go back and try again</a>
              
              <?php
            }
          
          }
          ?>
          
<?php
} // pi_admin_page()

?>