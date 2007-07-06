addLoadEvent( function() {var manager = new dbxManager( dbxL10n.manager );} );

addLoadEvent( function()
{

	// Boxes are closed by default. Open the Category box if the cookie isn't already set.
	var catdiv = document.getElementById('installer-box');
	if ( catdiv ) {
		var button = catdiv.getElementsByTagName('A')[0];
		if ( dbx.cookiestate == null && /dbx\-toggle\-closed/.test(button.className) )
			meta.toggleBoxState(button, true);
	}

	var pluginstaller = new dbxGroup(
		'installer-box',
		'vertical',
		'10',
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'10',
		'yes',
		'closed',
		dbxL10n.open,
		dbxL10n.close,
		dbxL10n.moveMouse,
		dbxL10n.toggleMouse,
		dbxL10n.moveKey,
		dbxL10n.toggleKey,
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
		);
});
