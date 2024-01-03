/**
 * Plugin: BP Star Ratings
 *
 * Description: js for the wordpress plugin BP Star Ratings.
 *
 * @package BP Star Ratings
 * @subpackage WordPress Plugin
 * @author WPEra
 * @plugin_uri http://wakeusup.com/2011/05/bp-star-ratings/
 */

(function($, window, document, undefined){

	$.fn.bpstarratings = function(options)
	{
		$.fn.bpstarratings.options = $.extend({
			ajaxurl   : null,
			nonce     : null,
			func      : null,
			grs       : false,
			msg       : 'Rate this post',
			fuelspeed : 400,
			thankyou  : 'Thank you for rating.',
			error_msg : 'An error occured.',
			tooltip   : true,
			tooltips  : {
				0 : {
					tip   : "Poor",
					color : "red"
				},
				1 : {
					tip   : "Fair",
					color : "brown"
				},
				2 : {
					tip   : "Average",
					color : "orange"
				},
				3 : {
					tip   : "Good",
					color : "blue"
				},
				4 : {
					tip   : "Excellent",
					color : "green"
				}
			}
		}, $.fn.bpstarratings.options, options ? options : {});

		var Objs = [];
		this.each(function(){
			Objs.push($(this));
		});

		$.fn.bpstarratings.fetch(Objs, 0, '0%', $.fn.bpstarratings.options.msg, true);
		return this.each(function(){});

    };

	$.fn.bpstarratings.animate = function(obj)
	{
		if(!obj.hasClass('disabled'))
		{
			// Disable hover() on mobile, otherwise the first click event is captured by hover() and a user has to click a second time to rate.
			var isMobile = {
				Android: function() {
					return navigator.userAgent.match(/Android/i);
				},
				BlackBerry: function() {
					return navigator.userAgent.match(/BlackBerry/i);
				},
				iOS: function() {
					return navigator.userAgent.match(/iPhone|iPad|iPod/i);
				},
				Opera: function() {
					return navigator.userAgent.match(/Opera Mini/i);
				},
				Windows: function() {
					return navigator.userAgent.match(/IEMobile/i);
				},
				any: function() {
						return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
				}
			};
			if (!isMobile.any()) {
				var legend = $('.bpsr-legend', obj).html(),
					fuel = $('.bpsr-fuel', obj).css('width');
				$('.bpsr-stars a', obj).hover( function(){
					var stars = $(this).attr('href').split('#')[1];
					if($.fn.bpstarratings.options.tooltip!=0)
					{
						if($.fn.bpstarratings.options.tooltips[stars-1]!=null)
						{
							$('.bpsr-legend', obj).html('<span style="color:'+$.fn.bpstarratings.options.tooltips[stars-1].color+'">'+$.fn.bpstarratings.options.tooltips[stars-1].tip+'</span>');
						}
						else
						{
							$('.bpsr-legend', obj).html(legend);
						}
					}
					$('.bpsr-fuel', obj).stop(true,true).css('width', '0%');
					$('.bpsr-stars a', obj).each(function(index, element) {
						var a = $(this),
							s = a.attr('href').split('#')[1];
						if(parseInt(s)<=parseInt(stars))
						{
							$('.bpsr-stars a', obj).stop(true, true);
							a.hide().addClass('bpsr-star').addClass('orange').fadeIn('fast');
						}
					});
				}, function(){
					$('.bpsr-stars a', obj).removeClass('bpsr-star').removeClass('orange');
					if($.fn.bpstarratings.options.tooltip!=0) $('.bpsr-legend', obj).html(legend);
					$('.bpsr-fuel', obj).stop(true,true).animate({'width':fuel}, $.fn.bpstarratings.options.fuelspeed);
				}).unbind('click').click( function(){
					return $.fn.bpstarratings.click(obj, $(this).attr('href').split('#')[1]);
				});
			} else {
				$('.bpsr-stars a', obj).unbind('click').click( function(){
					return $.fn.bpstarratings.click(obj, $(this).attr('href').split('#')[1]);
				});
			}
		}
		else
		{
			$('.bpsr-stars a', obj).unbind('click').click( function(){ return false; });
		}
	};

	$.fn.bpstarratings.update = function(obj, per, legend, disable, is_fetch)
	{
		if(disable=='true')
		{
			$('.bpsr-fuel', obj).removeClass('yellow').addClass('orange');
		}
		$('.bpsr-fuel', obj).stop(true, true).animate({'width':per}, $.fn.bpstarratings.options.fuelspeed, 'linear', function(){
			if(disable=='true')
			{
				obj.addClass('disabled');
				$('.bpsr-stars a', obj).unbind('hover');
			}
			if(!$.fn.bpstarratings.options.grs || !is_fetch)
			{
				$('.bpsr-legend', obj).stop(true,true).hide().html(legend?legend:$.fn.bpstarratings.options.msg).fadeIn('slow', function(){
					$.fn.bpstarratings.animate(obj);
				});
			}
			else
			{
				$.fn.bpstarratings.animate(obj);
			}
		});
	};

	$.fn.bpstarratings.click = function(obj, stars)
	{
		$('.bpsr-stars a', obj).unbind('hover').unbind('click').removeClass('bpsr-star').removeClass('orange').click( function(){ return false; });
		
		var legend = $('.bpsr-legend', obj).html(),
			fuel = $('.bpsr-fuel', obj).css('width');
		$.fn.bpstarratings.fetch(obj, stars, fuel, legend, false);
		return false;
	};

	$.fn.bpstarratings.fetch = function(obj, stars, fallback_fuel, fallback_legend, is_fetch)
	{
		var postids = [];
		$.each(obj, function(){
			postids.push($(this).attr('data-id'));
		});
		$.ajax({
			url: $.fn.bpstarratings.options.ajaxurl,
			data: 'action='+$.fn.bpstarratings.options.func+'&id='+postids+'&stars='+stars+'&_wpnonce='+$.fn.bpstarratings.options.nonce,
			type: "post",
			dataType: "json",
			beforeSend: function(){
				$('.bpsr-fuel', obj).animate({'width':'0%'}, $.fn.bpstarratings.options.fuelspeed);
				if(stars)
				{
					$('.bpsr-legend', obj).fadeOut('fast', function(){
						$('.bpsr-legend', obj).html('<span style="color: green">'+$.fn.bpstarratings.options.thankyou+'</span>');
					}).fadeIn('slow');
				}
			},
			success: function(response){
				$.each(obj, function(){
					var current = $(this),
						current_id = current.attr('data-id');
					if(response[current_id].success)
					{
						$.fn.bpstarratings.update(current, response[current_id].fuel+'%', response[current_id].legend, response[current_id].disable, is_fetch);
					}
					else
					{
						$.fn.bpstarratings.update(current, fallback_fuel, fallback_legend, false, is_fetch);
					}
				});
			},
			complete: function(){
				
			},
			error: function(e){
				$('.bpsr-legend', obj).fadeOut('fast', function(){
					$('.bpsr-legend', obj).html('<span style="color: red">'+$.fn.bpstarratings.options.error_msg+'</span>');
				}).fadeIn('slow', function(){
					$.fn.bpstarratings.update(obj, fallback_fuel, fallback_legend, false, is_fetch);
				});
			}
		});
	};

	$.fn.bpstarratings.options = {
		ajaxurl   : bepassive_plugin_bpsr_js.ajaxurl,
		func      : bepassive_plugin_bpsr_js.func,
		nonce     : bepassive_plugin_bpsr_js.nonce,
		grs       : bepassive_plugin_bpsr_js.grs,
		tooltip   : bepassive_plugin_bpsr_js.tooltip,
		tooltips  : bepassive_plugin_bpsr_js.tooltips,
		msg       : bepassive_plugin_bpsr_js.msg,
		fuelspeed : bepassive_plugin_bpsr_js.fuelspeed,
		thankyou  : bepassive_plugin_bpsr_js.thankyou,
		error_msg : bepassive_plugin_bpsr_js.error_msg
	};
   
})(jQuery, window, document);

jQuery(document).ready( function($){
	$('.bp-star-ratings').bpstarratings();
});