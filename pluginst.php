<?php
/*
Plugin Name: PlugInstaller
Plugin URI: http://henning.imaginemore.de/pluginstaller/
Description: Easy (un)installation of new plugins directly from the admin interface
Version: 0.1.95
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

// Replace old plugins page
function pi_replace_plugins_page() {
   if ((strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php')) && ($_GET['page'] == '')) {
     include(ABSPATH. PLUGINDIR .'/pluginstaller/plugins.php');
     die();
   }
}

add_action('admin_notices', 'pi_replace_plugins_page');
?>