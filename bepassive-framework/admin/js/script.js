
	var bf_admin = {
		
		'_ajaxurl' : bepassiveplugin_admin_script.ajaxurl,
		'_func' : bepassiveplugin_admin_script.func,
		'_nonce' : bepassiveplugin_admin_script.nonce,
		
		init : function(obj)
		{
			jQuery(document).ready( function($){
				obj = $('.bepassive-framework');
				$('a[rel="save-options"]', obj).click( function(){
					var form = jQuery('form[name="bf_form"]', obj);
					var values = form.serialize();
					bf_admin.ajax_post($, values, bf_admin._func, bf_admin._nonce, '');
					return false;
				});
			});
		},
		
		ajax_post : function($, params, action, nonce, waiting_msg, callback_success)
		{
			$.ajax({
				url: bf_admin._ajaxurl,
				data: params+'&action='+action+'&_wpnonce='+nonce,
				type: "post",
				dataType: "json",
				beforeSend: function(){
					bepassive_lightbox_js.lightbox(waiting_msg, 'busy', false);
				},
				success: function(response){
					if(callback_success)
					{
						callback_success(response);
					}
				},
				complete: function(){
					bepassive_lightbox_js.lightbox('Done', 'unclose', false);
					setTimeout(function(){bepassive_lightbox_js.lightbox_close()}, 1000);
				},
				error: function(){
					
				}
			});
		}
	
	};
	
	bf_admin.init();
