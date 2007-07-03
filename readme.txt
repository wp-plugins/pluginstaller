=== PlugInstaller ===
Contributors: hschaefer, angrycamel
Donate link: http://henning.imaginemore.de/pluginstaller/donate
Tags: plugin, admin, install, manage, upload
Requires at least: 2.0.2
Tested up to: 2.2
Stable tag: 0.2.0

Install and uninstall plugins from your admin interface without uploading the plugins via FTP and automatically check for plugin updates.

== Description ==

PlugInstaller is a WordPress plugin-management plugin that eliminates the need to download a plugin archive, upload it to your FTP server and unpack it manually. With PlugInstaller, you can easily enter a plugin URL (e.g. directly from a plugin repository website like the WordPress.org plugin repository) within the admin interface which is in turn automatically downloaded and installed on your server or you can upload a file from your local file system which is also automatically installed. If you do no longer like one or more plugins that you previously installed, you can uninstall those plugins with a single click.

To stay informed about plugin updates, there is an automatic update checking mechanism built into your plugins management page. This plugin completely replaces the standard plugins management
page to incorporate all functions from PlugInstaller into a redesigned and more usable plugin management page.

With PlugInstaller you will no longer have to use any other software than your WordPress administration interface to install or uninstall plugins. In addition to that, plugin installation will hardly last more than 5 to 10 seconds with PlugInstaller, as there is no user interference required. You can also display the readme file provided along with any installed plugins with a single click from your plugin management page.

As of version 0.2, you will also be able to install critical plugin packages that do not contain the proper directory names inside the zip package.

== Installation ==

IMPORTANT: 	This plugin DOES NOT work on a windows server, as there are no commandline tools
to unpack archives, but it works great when connecting from windows to a linux/unix server.

PREREQUISITES: 	Make sure your server has the following tools installed and readily available within the
search path: "tar", "gzip" and "unzip".
Also, set your wp-content/plugins directory to world-writeable (chmod -R 777 plugins) or at least writeable to your httpd user.

Carefully follow the following steps as it is probably the last time you will ever do this:

1. Upload the plugin package to your wp-content/plugins directory
1. Unzip the package (unzip pluginst.zip)
1. Go to your "plugins" administration page and activate the PlugInstaller plugin

== Frequently Asked Questions ==

= What does 'does not work with windows' mean? =

This just means that you cannot use this plugin on a server that is based on a windows operating system. You can, of course, use it on your linux server
if you browse the internet from a windows-based client computer.

= Where is my plugins management page gone? =

PlugInstaller completely replaces the plugin management page. You will find your plugins listed in a more space-saving way, as this space is used to display additional information.

= Where are my deactivated plugins gone? =

The deactivated plugins will be listed at the bottom of the plugins management page, not along with the active plugins.

= How can I check for updated plugins? =

Press the "Check for updated plugins" button at the bottom of the list of deactivated plugins. Checking may take a little while, so be patient. If updates
are found, a message will appear on the screen telling you that updates are available. Additionally, a yellow "Update"-flag will appear behind those plugins
that have updates available.

= How do I upgrade plugins? =

Click on the yellow "Update" flag behind updateable plugins (be sure to check for new updates in regular intervals). This will automatically upgrade the selected plugin.

= Why do not all updateable plugins show up? =

Updates are only working with a restricted set of plugins from the wordpress.org plugins repository. PlugInstaller takes measures against those problems when installing a plugin
the first time. If you experience plugins that are hosted on wordpress.org but that do not support automatic updating, just uninstall them and install them again using
PlugInstaller. After this step, automatic updates should be supported on those plugins.

= What can I do to make my plugin compatible to PlugInstaller? =

I am continuously working to make PlugInstaller compatible to almost every plugin. But you as a plugin developer can follow a few guidelines to ensure your plugin's compatibility with
PlugInstaller.

* Include a readme file: Be sure to name that file "readme.txt" and place it in the trunk path of your svn repository. Be sure to name that file exactly "readme.txt", no capital letters!
* Refrain from using subdirectories: Placing your main plugin file inside a subdirectory of the trunk or the stable tag confuses not only wordpress, but also PlugInstaller. You can, of course, use
subdirectories for additional files such als libaries, images and so on.
* Make your plugin work from the standard directory: The wp.org plugin repository creates a zip file containing a directory that is named after your project's main path. The PlugInstaller ZIP file, for example,
contains a directory named "pluginstaller". Don't make your plugin require to be in a directory named other than your project's svn name (e.g. providing PlugInstaller in a directory called "pluginst").
* Comply to the wordpress.org plugin conventions.

= Why is PlugInstaller unable to upgrade itself? =

Well, did you ever try to exchange your old car with a new one while you were driving on the motorway? It's exactly the same problem: PlugInstaller is needed to update plugins, but
if you update a package, this package will first be deactivated (you cannot delete active plugins without throwing lots of error messages). But how should PlugInstaller upgrade itself
if it is deactivated?

= What can I do if a plugin did not install properly? =

Below the list of deactivated plugins you will find a list of "broken plugins". Broken plugins are subdirectories inside your plugin directory that do not contain any
plugin files. If a plugin shows up on that list after it has been installed, this means that the plugin cannot be installed by PlugInstaller. You may safely delete the
broken plugin by selecting the "delete" link behind the directory name.

== Usage ==

To install a new plugin package go to "Plugins" in your administration interface. You can either enter a download URL for a plugin (i.e. a download URL form the wordpress.org plugins repository) or
select a file from your local computer to be updated. I recommend to install plugins directly from online sources, as this uses most of the advanced features of PlugInstaller, like the automatic
update checking for plugins.

You may activate and deactivate plugins by selecting the appropriate links from the plugin management page. If you want to view the readme file of a plugin, click the "View Readme" link. To display
more information on a plugin, click the "+" sign directly before the plugin's name.

To uninstall a plugin, you will first have to deactivate it. Then, select "uninstall" from the list of inactive plugins.

If you want to check your installed plugins for newer versions (currently only works with those plugins hosted on wordpress.org), hit the "Check for updated plugins" button below the list
of active plugins. Be warned that this might take a while, as checking for updates heavily relies on the wordpress.org svn server which may be slow at times.

To update a plugin that shows the yellow "update" flag, just click on that flag. PlugInstaller will automatically install the update for this plugin. Don't forget to reactivate
the plugin after you have updated it!

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

