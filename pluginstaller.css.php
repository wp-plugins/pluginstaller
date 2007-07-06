<?php
  header("Content-type: text/css");
?>

#installer-box h3.dbx-handle {
	margin-left: 7px;
	margin-bottom: -7px;
	padding: 6px 1em 0 3px;
	height: 19px;
	font-size: 12px;
	background: #2685af url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-head-right.gif) no-repeat top right;
}

#installer-box div.dbx-h-andle-wrapper {
	margin: 0 0 0 -7px;
	background: #fff url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-head-left.gif) no-repeat top left;
}

#installer-box div.dbx-content {
	margin-left: 8px;
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-bg-right.gif) repeat-y right;
	padding: 10px 10px 15px 0;
}

#installer-box div.dbx-c-ontent-wrapper {
	margin-left: -7px;
	margin-right: 0;
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-bg-left.gif) repeat-y left;
}

#installer-box fieldset.dbx-box {
	padding-bottom: 9px;
	margin-left: 6px;
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-butt-right.gif) no-repeat bottom right;
}

#installer-box div.dbx-b-ox-wrapper {
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-butt-left.gif) no-repeat bottom left;
}

#installer-box .dbx-box-closed div.dbx-c-ontent-wrapper {
	padding-bottom: 2px;
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-butt-left.gif) no-repeat bottom left;
}

#installer-box .dbx-box {
	background: url(<?php echo $_GET['siteurl'] ?>/wp-admin/images/box-butt-right.gif) no-repeat bottom right;
}
	
#installer-box a.dbx-toggle, #installer-box a.dbx-toggle-open:visited {
	height: 22px;
	width: 22px;
	top: 3px;
	right: 5px;
	background-position: 0 -3px;
}

#installer-box a.dbx-toggle-open, #installer-box a.dbx-toggle-open:visited {
	height: 22px;
	width: 22px;
	top: 3px;
	right: 5px;
	background-position: 0 -28px;
}

#installer-box fieldset {
	margin-bottom: 1em;
}

#installer-box h3 {
	padding: 3px;
	font-weight: normal;
	font-size: 13px;
}

#installer-box div {
	margin-top: .5em;
}