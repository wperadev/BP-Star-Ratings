// JavaScript Document
(function() {
	tinymce.create('tinymce.plugins.bpStarRatings', {
		init : function(ed, url) {

			// Register button and click event
			ed.addButton('bpstarratings', {
				title : 'BP Star Ratings',
				cmd : 'mcebpStarRatings',
				image: url + 'assets/images/icon.png',
				onClick : function(){
					ed.execCommand('mceReplaceContent', false, "[bpstarratings]");
				}});
		},

		getInfo : function() {
			return {
				longname : 'BP Star Ratings',
				author : 'BePassive',
				authorurl : 'http://wpera.com',
				infourl : 'http://wpera.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bpstarratings', tinymce.plugins.bpStarRatings);

})();
