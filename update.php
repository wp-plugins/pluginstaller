<?php

function pi_perform_update($plugin, $tag) {
  // Unregister this plugin from the list of updateable plugins:
  $updates = get_option('updated_plugins');
  unset($updates[$plugin]);
  update_option('updated_plugins', $updates);
  
  // Get svn name:
  $plugin_svn = substr($plugin, 0, strpos($plugin,'/'));
  
  // Deactivate plugin:
  $current = get_option('active_plugins');
  array_splice($current, array_search( $plugin, $current), 1 ); // Array-fu!
  update_option('active_plugins', $current);
  do_action('deactivate_' . trim( $plugin ));
  
  // Uninstall plugin:
  uninstall_plugin($plugin);
  
  // Install-URL:
  $url = "http://downloads.wordpress.org/plugin/$plugin_svn.$tag.zip";
  
  $ret = install_from_url($url);
  
    return $ret . " During the upgrade process, your plugin has been deactivated. Be sure to re-activate it!";
  
}

function pi_check_for_updated_plugins() {
  // Set all plugins to be up to date:
  $updated = array();
  update_option('updated_plugins',$updated);

  // Get plugin list:
  $all_plugins = get_plugins();
  
  $count = 0;
  foreach ($all_plugins as $plugin_name => $plugin_data) {
    $plugin_svn = substr($plugin_name, 0, strpos($plugin_name,'/'));
    $plugin_version = $plugin_data['Version'];
    $plugin_readme = u_get_contents("http://svn.wp-plugins.org/$plugin_svn/trunk/readme.txt");
    // find stable tag:
    $findings = array();
    if (eregi('Stable Tag: ([^ ]+)',$plugin_readme,$findings)) {
      $version = trim(substr($findings[1], 0, strpos($findings[1], "\n")));
      if ($version != 'trunk') {
        if ($version != $plugin_data['Version']) {
          $updated[$plugin_name] = $version;
          $count++;
        }
      }
    }
  }

  update_option('updated_plugins',$updated);

  if ($count == 0) {
    return 'There are currently no updates for your plugins.';
  }else{
    return "There are updates available for $count plugins.";
  }
}

?>