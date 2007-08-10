<?php
$this_dir = ABSPATH . PLUGINDIR . '/pluginstaller';

require_once($this_dir . '/ui.php');
require_once($this_dir . '/readme.php');
require_once($this_dir . '/uninstall.php');
require_once($this_dir . '/install.php');
require_once($this_dir . '/update.php');
require_once($this_dir . '/utils.php');

if ($_GET['activate'] == 'all') {
	    $all = get_plugins();
		foreach ($all as $plugin => $data) {
		  $current = get_option('active_plugins');
		  if (!in_array($plugin, $current)) {
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			include(ABSPATH . PLUGINDIR . '/' . $plugin);
			do_action('activate_' . $plugin);
		  }
		}
		$activateall = true;
		unset($_GET['activate']);
	} 

if ( isset($_GET['action']) ) {
	if ('activate' == $_GET['action']) {
		check_admin_referer('activate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		$plugin = trim($_GET['plugin']);
		if ( validate_file($plugin) )
			wp_die(__('Invalid plugin.'));
		if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
			wp_die(__('Plugin file does not exist.'));
		if (!in_array($plugin, $current)) {
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			include(ABSPATH . PLUGINDIR . '/' . $plugin);
			do_action('activate_' . $plugin);
		}
		wp_redirect('plugins.php?activate=true');
		exit;
	} else if ('deactivate' == $_GET['action']) {
		check_admin_referer('deactivate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		do_action('deactivate_' . trim( $_GET['plugin'] ));
		wp_redirect('plugins.php?deactivate=true');
		exit;
	} elseif ($_GET['action'] == 'deactivate-all') {
		check_admin_referer('deactivate-all');
		$current = get_option('active_plugins');
		
		foreach ($current as $plugin) {
			array_splice($current, array_search($plugin, $current), 1);
			do_action('deactivate_' . $plugin);
		}
		
		update_option('active_plugins', array());
		wp_redirect('plugins.php?deactivate-all=true');
		exit;
	} 
}

// Clean up options
// If any plugins don't exist, axe 'em


pi_install();

$check_plugins = get_option('active_plugins');

// Sanity check.  If the active plugin list is not an array, make it an
// empty array.
if ( !is_array($check_plugins) ) {
	$check_plugins = array();
	update_option('active_plugins', $check_plugins);
}

// If a plugin file does not exist, remove it from the list of active
// plugins.
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . PLUGINDIR . '/' . $check_plugin)) {
			$current = get_option('active_plugins');
			$key = array_search($check_plugin, $current);
			if ( false !== $key && NULL !== $key ) {
				unset($current[$key]);
				update_option('active_plugins', $current);
			}
	}
}

?>


<script type='text/javascript' src='<?php echo get_settings('siteurl') ?>/wp-includes/js/dbx.js?ver=2.05'></script>
<script type='text/javascript' src='<?php echo get_settings('siteurl') . '/' . PLUGINDIR ?>/pluginstaller/dbx-plugin-key.js?ver=20070417&amp;pagenow=plugins.php'></script>
<script type='text/javascript'>
/* <![CDATA[ */
	dbxL10n = {
		manager: "pluginstaller",
		open: "open",
		close: "close",
		moveMouse: "click-down and drag to move this box",
		toggleMouse: "click to %toggle% this box",
		moveKey: "use the arrow keys to move this box",
		toggleKey: ", or press the enter key to %toggle% it"
	}
/* ]]> */
</script>

<?php

// Check for updates:
if ($_GET['update'] == 'check') {
?>

<div id="message" class="updated fade"><p><?php echo pi_check_for_updated_plugins(); ?></p>
</div>

<?php
}

// Perform update:
if (($_GET['update'] != 'check') && ($_GET['update'] != '')) {
?>

<div id="message" class="updated fade"><p><?php echo pi_perform_update($_GET['update'], $_GET['tag']); ?></p>
</div>

<?php
}

// Forcibly delete a plugin:
if ($_GET['force-delete'] != '') {
?>

<div id="message" class="updated fade"><p><?php echo pi_force_delete($_GET['force-delete']); ?></p>
</div>

<?php
}

?>

<?php if (isset($_GET['activate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate-all'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('All plugins <strong>deactivated</strong>.'); ?></p></div>
<?php endif; ?>
<?php if ($activateall) : ?>
	<div id="message" class="updated fade"><p><?php _e('All plugins <strong>activated</strong>.'); ?></p></div>
<?php endif; ?>

<?php pi_check_for_update(); ?>

<div class="wrap">


<div id='readme' style='padding: 10px; background-color: #FFFFFF; display: none; z-index: 200; position: absolute; width: 70%; left: 15%; height: 70%;'>
<h2><?php _e('Readme') ?> (<a href='javascript:void(null)' onClick='hideReadme()'>close</a>)</h2>

<div id='the_readme' style='overflow: auto; height: 400px;'>
<?php

/*

function parse_readme($readme) {
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

// find the readme file:
$parts = array();
eregi('([^/]*)/(.*)',$_GET["readme"], $parts);

$readme_dir  = ABSPATH . PLUGINDIR . '/' . $parts[1] . '/';

if (file_exists($readme_dir . 'readme.html')) {
  $filename = $readme_dir . 'readme.html';
}elseif (file_exists($readme_dir . 'README.html')) {
  $filename = $readme_dir . 'README.html';
}elseif (file_exists($readme_dir . 'README.HTML')) {
 $filename = $readme_dir . 'README.HTML';
}elseif (file_exists($readme_dir . 'Readme.html')) {
  $filename = $readme_dir . 'Readme.html';
}elseif (file_exists($readme_dir . 'readme.txt')) {
  $filename = $readme_dir . 'readme.txt';
}elseif (file_exists($readme_dir . 'README.txt')) {
  $filename = $readme_dir . 'README.txt';
}elseif (file_exists($readme_dir . 'README.TXT')) {
  $filename = $readme_dir . 'README.txt';
}elseif (file_exists($readme_dir . 'Readme.txt')) {
  $filename = $readme_dir . 'Readme.txt';
}elseif (file_exists($readme_dir . 'README')) {
  $filename = $readme_dir . 'README';
}


// Display:
if ($filename == "") {
  echo "<h3>Sorry, there is no readme file available for this plugin!</h3>";
}else{
    if (stristr($filename, 'html') !== false) {
      echo file_get_contents($filename);
    }else{
      echo nl2br(pi_parse_readme(file_get_contents($filename)));
    }
}

*/

?>
</div>

</div>





<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
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

<div id="installer-box" class="dbx-group" >

<div class="dbx-b-ox-wrapper">
<fieldset id="install" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle"><?php _e('Install a new Plugin') ?> (<a href='javascript:void(null)' onClick="document.getElementById('manual').style.display = 'block';">Help</a>)</h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content">

<div id='manual' style='display: none;'>
  <?php pi_manual(); ?>
</div>

<?php pi_install_section(); ?>

<?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>.'); ?>
</div>
</div>
</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
<fieldset id="postexcerpt" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle"><?php _e('Active Plugins') ?></h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content">

<table style="width: 100%;" cellspacing='0' cellpadding='0'>
<?php

    $available = '';

	$style = '';
	
	$count = 0;

    $updates = get_option('updated_plugins');

	foreach($plugins as $plugin_file => $plugin_data) {
	    $echo = false;
		//$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$echo = true;
			$uninstall = "";
		} else {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
			$uninstall = "</td><td style='width: 150px; vertical-align: top;'><a class='delete' href='javascript:void(null)' onClick=\"if (confirm('Do you really want to uninstall ".addslashes(strip_tags($plugin_data['Title']))."?')) {window.location.href='plugins.php?page=".$_GET['page']."&uninstall=".$plugin_file."'; }\">".__('Uninstall')."</a>";
		}
		$readme = "<a href='javascript:void(null)' onClick=\"showReadme('$plugin_file');\" title='".__('Display the readme file')."' class='edit'>".__('View Readme')."</a>";

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

		// Sanitize all displayed data
		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

		if ( $style != '' )
			$style = 'class="' . $style . '"';
		if ( is_writable(ABSPATH . 'wp-content/plugins/' . $plugin_file) )
			$edit = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."'>".__('Edit')."</a>";
		else
			$edit = '';
  
		$output = "<tr id='row$count' onMouseOver=\"document.getElementById('row$count').style.backgroundColor = '#b8d4ff';\" onMouseOut=\"document.getElementById('row$count').style.backgroundColor = '#ffffff';\" $style>";
		$output .= "<td class='name' >";
		$output .= "<strong id='tick$count' style='cursor: pointer;' onClick=\"if (document.getElementById('descr$count').style.display == 'block') { document.getElementById('descr$count').style.display = 'none'; this.innerHTML = '[+]';}else{ document.getElementById('descr$count').style.display = 'block'; this.innerHTML = '[-]';}\">[+]</strong>";
		$output .= " {$plugin_data['Title']}";
		
		if (@array_key_exists($plugin_file, $updates)) {
		  $output .= " <span onClick=\"if (confirm('Would you like to update ".strip_tags($plugin_data['Title'])." from version ".$plugin_data['Version']." to version ".$updates[$plugin_file]."?')) { window.location.href = 'plugins.php?update=".$plugin_file."&tag=".$updates[$plugin_file]."'; }\" style='cursor: pointer; padding: 2px; border: 1px solid #c0c000; background-color: #ffff80; color: #c00000; font-size: 7pt; font-weight: bold;'>UPDATE!</span>";
		}
		
		$output .= "<div id='descr$count' style='display: none;'><cite>".sprintf(__('By %s'), $plugin_data['Author'])."</cite>, <strong>Version:</strong> {$plugin_data['Version']}<br /><br />";
		$output .= "{$plugin_data['Description']}";
		if ( current_user_can('edit_plugins') )
		  $output .=  ' '.$edit;
		$output .= '<br />&nbsp;</div></td>';
		$output .= "<td class='togl' style='width: 150px; vertical-align: top;'>$toggle</td>
		<td class='readme' style='width: 150px; vertical-align: top;'>$readme</td>";
		$output .= $uninstall;
		$output .= "</td></tr>";
	    if (!$echo) {
          $available .= $output;
        }else{
          echo $output;
        }
        $count++;
	}
?>
<tr>
  <td><br /><input type='button' class='button' value='Check for updated plugins' onClick='window.location.href = "plugins.php?update=check";'></td>
  <td align='center'><br /><input type='button' class='button' value='Deactivate all' onClick='if (confirm("Do you really want to deactivate all plugins?")) { window.location.href = "<?php echo wp_nonce_url('plugins.php?action=deactivate-all', 'deactivate-all'); ?>"; }'></td>
  <td>&nbsp;</td>
</tr>

</table>
<br />
<?php printf(__('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), PLUGINDIR); ?>
</div>
</div>
</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
<fieldset id="postexcerpt" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle"><?php _e('Available but Inactive Plugins') ?></h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content">

<table style="width: 100%;" cellspacing='0' cellpadding='0'>
	
<?php

echo $available;

?>	

<tr>
  <td>&nbsp;</td>
  <td align='center'><br /><input type='button' class='button' value='Activate all' onClick='if (confirm("Do you really want to activate all plugins?")) { window.location.href = "plugins.php?activate=all"; }'></td>
  <td>&nbsp;</td>
</tr>
	
</table>

</div>
</div>
</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
<fieldset id="postexcerpt" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle"><?php _e('Broken Plugins') ?></h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content">
These plugins seem to be broken, that means there is a directory for the plugin, but no plugin file is found inside that directory.<br /><br />
<table style="width: 100%;" cellspacing='0' cellpadding='0'>
	
<?php

// Check for broken plugins:

$broken = "";

$plugin_check = array();
// reduce plugin files to their directories:
foreach ($plugins as $fname => $data) {
  array_push($plugin_check, substr($fname, 0, strrpos($fname, '/')));
}

// Scan plugins directory:
$dp = opendir(ABSPATH . PLUGINDIR);
while (($file = readdir($dp)) !== false) {
  if ((filetype(ABSPATH . PLUGINDIR . '/' . $file) == 'dir') && ($file != '.') && ($file != '..')) {
    if (!in_array($file, $plugin_check)) {
      $broken .= "<tr><td class='name'><strong>$file</strong></td>";
      $broken .= "<td class='togl' style='width: 150px; vertical-align: top;'>";
      $broken .= "<a href='javascript:void(null)' onClick=\"if (confirm('Do you really want to delete the plugin directory ".addslashes($file)."? This may cause side-effects and problems.')) { window.location.href = 'plugins.php?force-delete=$file'; }\"' title='".__('Delete this plugin')."' class='delete'>".__('Delete')."</a>";
	  $broken .= "</td>";
      $broken .= "</tr>";
    }
  }
}
closedir($dp);

if ($broken == "") {
  $broken = "<strong>There are currently no broken plugins.</strong>";
}

echo $broken;

?>	
	
</table>

</div>
</div>
</fieldset>
</div>

</div>

<?php
}
?>
</div>


<!-- the fade box -->
<div id='fadediv' style='display: none; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; -moz-opacity: 0.5; opacity: 0.5; filter:alpha(opacity=50); background-color: #000000; z-index: 100;'>&nbsp;</div>

<script type='text/javascript'>
function fadeOut() {
  var _docHeight = document.height || document.body.offsetHeight;
  document.getElementById('fadediv').style.height = _docHeight + 'px';
  document.getElementById('fadediv').style.display = 'block';
}

function fadeIn() {
  document.getElementById('fadediv').style.display = 'none';
}

function showReadme(plugin) {
  fadeOut();
  document.getElementById('readme').style.display = 'block';
}

function hideReadme() {
  document.getElementById('readme').style.display = 'none';
  fadeIn();
}
</script>


<?php
include('admin-footer.php');
?>
