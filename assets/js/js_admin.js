
// Object bf_admin is available to use

(function($){
	
	var bf_admin_plugin_bpsr = {
		init : function()
		{
			jQuery(document).ready( function($){
				bf_admin_plugin_bpsr.reset_ratings($('.bepassive-framework'), bepassive_plugin_bpsr_js_admin.func_reset, bepassive_plugin_bpsr_js_admin.nonce)
			});
		},
		
		reset_ratings : function(obj, func, nonce)
		{
			$('a[rel="bpsr-reset"]', obj).click( function(){
				var form = jQuery('form[name="bf_form"]', obj);
				var values = form.serialize();
				bf_admin.ajax_post($, values, func, nonce, 'Flushing');
				$('._bpsr_reset._on', obj).parent().fadeOut('slow');
				return false;
			});
		
			$('a[rel="bpsr-reset-all"]', obj).click( function(){
				$('._bpsr_reset', obj).removeClass('_off').addClass('_on').val('1');
				return false;
			});
		
			$('a[rel="bpsr-reset-none"]', obj).click( function(){
				$('._bpsr_reset', obj).removeClass('_on').addClass('_off').val('0');
				return false;
			});
		}
	};
	
	bf_admin_plugin_bpsr.init();
   
})( jQuery );

