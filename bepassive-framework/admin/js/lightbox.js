/* -------------------------------------------------------------
----------------------------------------------------------------
|                                                              |
|  File name : lightbox.js                                     |
|  Usage     : Popup/Lightbox                                  |
|  Version   : 0.1                                             |
|  Author    : WPEra                                      |
|  URI       : http://bepassive.com                             |
|                                                              |
|  Description :                                               |
|  javascript class to easily integrate popups with an overlay |
|                                                              |
----------------------------------------------------------------
------------------------------------------------------------- */
var bepassive_lightbox_js = {
	
	_popup : '',
	_popup_bg : '',
	_popup_exit : '',
	_popup_busy : '',
	_popup_lightbox : '',
	_popup_color : '',
	_popup_opacity : '',
	_popup_active : false,
	
	_init : function(id, color, opacity)
	{
		//id = '.'+id;
		bepassive_lightbox_js._popup = id+' div.bppopup';
		bepassive_lightbox_js._popup_bg = id+' div.bppopup-bg';
		bepassive_lightbox_js._popup_exit = id+' div.bppopup-exit';
		bepassive_lightbox_js._popup_busy = id+' span.bppopup__processing';
		bepassive_lightbox_js._popup_lightbox = 'div.bppopup-lightbox'+id;
		bepassive_lightbox_js._popup_color = color ? color : '#000000';
		bepassive_lightbox_js._popup_opacity = opacity ? opacity : '0.7';
		bepassive_lightbox_js._events();
	},
	
	_events : function()
	{
	    jQuery(window).resize( function(){
			//bepassive_lightbox_js.lightbox_off();
			bepassive_lightbox_js.lightbox_center();
		});
		jQuery(window).scroll( function(){
			bepassive_lightbox_js.lightbox_center();
		});	
	},
	
	lightbox_off : function()
	{
		jQuery(bepassive_lightbox_js._popup_lightbox).css({
			'width' : '0px',
			'height' : '0px'
		});
	},
	
	lightbox_reset : function()
	{
		jQuery(bepassive_lightbox_js._popup_lightbox).css({
			'width' : jQuery(document).width(),
			'height' : jQuery(document).height(),
			'opacity' : bepassive_lightbox_js._popup_opacity,
			'background-color': bepassive_lightbox_js._popup_color
		});
		if(bepassive_lightbox_js._popup_active) { jQuery(bepassive_lightbox_js._popup_lightbox).fadeIn('slow'); }
	},
	
	lightbox_center : function()
	{
		bepassive_lightbox_js.lightbox_off();
		bepassive_lightbox_js.lightbox_reset();
		var width = jQuery(window).width();
		var height = jQuery(window).height();
		var popup = jQuery(bepassive_lightbox_js._popup);
		var epp_width = popup.width();
		var epp_height = popup.height();
		popup.stop(true,true).animate({
			'left' : ((width)/2) - (epp_width/2) + jQuery(document).scrollLeft(),
			'top' : ((height)/2) - (epp_height/2) + jQuery(document).scrollTop()
		}, 'fast', 'linear', function() {
		    jQuery(bepassive_lightbox_js._popup_exit).stop(true,true).animate({
				'left' : parseInt(popup.css('left'))+epp_width+10,
				'top' : parseInt(popup.css('top'))-20
			}, 'fast', 'linear');
			jQuery(bepassive_lightbox_js._popup_bg).stop(true,true).animate({
				'left' : ((width)/2) - (epp_width/2) + jQuery(document).scrollLeft() - 10,
				'top' : ((height)/2) - (epp_height/2) + jQuery(document).scrollTop() - 10
			}, 'fast', 'linear');	
		});
		if(bepassive_lightbox_js._popup_active) { popup.fadeIn('fast', function(){ jQuery(bepassive_lightbox_js._popup_bg).fadeIn('slow'); }); }
	},
	
	lightbox_close : function()
	{
		bepassive_lightbox_js._popup_active = false;
		jQuery(bepassive_lightbox_js._popup_lightbox).fadeOut('slow');
		jQuery(bepassive_lightbox_js._popup).fadeOut('fast',function(){ jQuery(bepassive_lightbox_js._popup_bg).fadeOut(200, function(){ jQuery(bepassive_lightbox_js._popup_exit).fadeOut(200); }); });
	},
	
	lightbox : function(html,arg,callback,w,h)
	{
		bepassive_lightbox_js._popup_active = true;
			
		var popup = jQuery(bepassive_lightbox_js._popup);
		
		popup.css({'width':'auto','height':'auto'});
		
		popup.html((arg=='busy'?jQuery(bepassive_lightbox_js._popup_busy).html():'')+html+(arg=='confirm'?bepassive_lightbox_js.lightbox_confirm(callback):''));

		var popup_width = 0;
		var popup_height = 0;

		if((w && popup.width()>w) || (h && popup.height()>h))
		{
			popup_width = w + 2;
		    popup_height = h;
			popup.css('overflow', 'auto');
		}
		else
		{
			popup_width = popup.width() + 2;
		    popup_height = popup.height();
		}

		popup.css({'width':popup_width+'px','height':popup_height+'px'});

		jQuery(bepassive_lightbox_js._popup_bg).css({
			'opacity': 0.5,
			'width': (popup_width+20)+'px',
			'height': (popup_height+20)+'px'
		});
        
		bepassive_lightbox_js.lightbox_center();

        if(arg!='unclose' && arg!='busy') 
		{
			jQuery(bepassive_lightbox_js._popup_exit).fadeIn('slow');
			
			jQuery(bepassive_lightbox_js._popup_exit+' a').bind('click', function(){
				jQuery(bepassive_lightbox_js._popup_exit+' a').unbind('click');
				bepassive_lightbox_js.lightbox_close();
				return false;
			});
			
			jQuery(bepassive_lightbox_js._popup_lightbox).bind('click', function(){
				jQuery(bepassive_lightbox_js._popup_lightbox).unbind('click');
				bepassive_lightbox_js.lightbox_close();
			});	
		}
		else if(arg)
		    jQuery(bepassive_lightbox_js._popup_exit).fadeOut('slow');
	},
	
	lightbox_confirm : function(callback)
	{
		return '<p><a href="#" title="Yes, I am sure" onclick="'+callback+'();return false;">Yes</a> | <a href="#" title="No, just bluffing" onclick="bepassive_lightbox_js.lightbox_close();return false;">No</a></p>';
	}
	
}

jQuery(document).ready( function($){
	bepassive_lightbox_js._init('.bepassive-lightbox', '#222', '0.7');
});

