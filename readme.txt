=== PlugInstaller ===
Contributors: hschaefer, angrycamel
Tags: plugin, admin, install, manage, upload
Requires at least: 2.0.2
Tested up to: 2.2
Stable tag: 0.1.6

Install and uninstall plugins from your admin interface without manually uploading the plugins via FTP

== Description ==

PlugInstaller is a WordPress plugin-management plugin that eliminates the need to download a plugin archive, upload it to your FTP server and unpack it manually. With PlugInstaller, you can easily enter a plugin URL (e.g. directly from a plugin repository website like the WordPress.org plugin repository) within the admin interface which is in turn automatically downloaded and installed on your server or you can upload a file from your local file system which is also automatically installed. If you do no longer like one or more plugins that you previously installed, you can uninstall those plugins with a single click.

With PlugInstaller you will no longer have to use any other software than your WordPress administration interface to install or uninstall plugins. In addition to that, plugin installation will hardly last more than 5 to 10 seconds with PlugInstaller, as there is no user interference required. You can also display the readme file provided along with any installed plugins with a single click from your plugin management page.

== Installation ==

IMPORTANT: 	This plugin DOES NOT work on a windows server, as there are no commandline tools
to unpack archives.

PREREQUISITES: 	Make sure your server has the following tools installed and readily available within the
search path: "tar", "gzip" and "unzip".
Also, set your wp-content/plugins directory to world-writeable (chmod -R 777 plugins) or at least writeable to your httpd user.

Carefully follow the following steps as it is probably the last time you will ever do this:

1. Upload the plugin package to your wp-content/plugins directory
1. Unzip the package (unzip pluginst.zip)
1. Go to your "plugins" administration page and activate the PlugInstaller plugin

== Usage ==

To install a new plugin package go to "Plugins/Install" in your administration interface. Scroll down to the
"Instructions" section to get more detailed usage instructions.

== More information ==

For more information visit the author's website at http://henning.imaginemore.de or write to
henning.schaefer@gmail.com for comments and support.

== License ==

Copyright 2007  Henning Schaefer  (email : henning.schaefer@gmail.com)

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

