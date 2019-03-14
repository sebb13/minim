/*
 *	minim - PHP framework
    Copyright (C) 2019  Sébastien Boulard

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; see the file COPYING. If not, write to the
    Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
window.addEventListener("popstate", function(e) {
	loadHtml(location.pathname.substr('/FR/'.length).replace('.html', ''));
	$('meta[name=app_current_page]').trigger("change");
});
function getToken()			{return $('meta[name=app_token]').attr("content");}
function getLang()			{return $('meta[name=app_lang]').attr("content");}
function getLangAvailable()	{return $('meta[name=app_lang_available]').attr("content");}
function getCurrentPage()	{return $('meta[name=app_current_page]').attr("content");}
function setLang(sLang) {
	$('meta[name=app_lang]').attr("content", sLang);
	$('meta[name=app_lang]').trigger("change");
	$('html').attr("lang", sLang);
}
function addMetaToForm(form, exw_actions) {
	
}
$.ajaxQ = (function(){
	var id = 0, Q = {};
	$(document).ajaxSend(function(e, jqx){
		jqx._id = ++id;
		Q[jqx._id] = jqx;
	});
	$(document).ajaxComplete(function(e, jqx){
		delete Q[jqx._id];
	});
	return {
		abortAll: function(){
			if(console && console.log) {
				console.log('abort');
			}
			var r = [];
			$.each(Q, function(i, jqx){
				r.push(jqx._id);
				jqx.abort();
			});
			return r;
		}
	};
})();
var localCache = {
    /**
     * timeout for cache in millis
     * @type {number}
     */
    timeout: 30000,
    /** 
     * @type {{_: number, data: {}}}
     **/
    data: {},
    remove: function (url) {
        delete localCache.data[url];
    },
    exist: function (url) {
        return !!localCache.data[url] && ((new Date().getTime() - localCache.data[url]._) < localCache.timeout);
    },
    get: function (url) {
		if(console && console.log) {
			console.log('Getting in cache for url ' + url);
		}
        return localCache.data[url].data;
    },
    set: function (url, cachedData, callback) {
        localCache.remove(url);
        localCache.data[url] = {
            _: new Date().getTime(),
            data: cachedData
        };
        if ($.isFunction(callback)) callback(cachedData);
    }
};
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    if (options.cache) {
        var complete = originalOptions.complete || $.noop,
            url = originalOptions.url;
        //remove jQuery cache as we have our own localCache
        options.cache = false;
        options.beforeSend = function () {
            if (localCache.exist(url) && originalOptions.cache === true) {
                complete(localCache.get(url));
                return false;
            }
            return true;
        };
        options.complete = function (data, textStatus) {
            localCache.set(url, data, complete);
        };
    }
	return false;
});
function addMsg(sType, sContents) {
	var sDiv = '<div class="alert alert-'+sType+' alert-dismissible" role="alert">'+
	'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
	sContents+'</div>';
	$('div#ajaxFrame').prepend(sDiv);
	return true;
}
function genericRequest(data, sPage, sLang) {
	if(typeof sPage === 'undefined') sPage = getCurrentPage();
	if(typeof sLang === 'undefined') sLang = getLang();
	sPage = sPage.replace('/', '_');
	return $.ajax({
			url: "https://"+window.location.hostname+"/index.php?page="+sPage+"&lang="+sLang,
			method: "POST",
			async: true,
			data: data
		});
}
function loadHtml(sPage) {
	sPage = sPage.replace('/', '_');
	$('body').css({'cursor':'wait'});
	var sLang = getLang();
	var promise = genericRequest({
					app_token: getToken(), 
					content: sPage
				}, sPage, sLang);
	promise.success(function(data) {
		if (sPage === 'menu') {
            $("ul.menu").fadeOut('500', function() {
				$("ul.menu").html(data);
                $("ul.menu").fadeIn('500');
			});
			return true;
		} else {
			/*$("#ajaxFrame").html(data);*/
			$("#ajaxFrame").fadeOut('500', function() {
				$("#ajaxFrame").html(data);
				updateLangSwitcher(sPage);
				setHistoryAndMenu(sPage);
				ajaxFrameRebind();
				$("#ajaxFrame").fadeIn('500');
			});
		}
		/* google analytics */
		if(sPage !== 'menu' && sPage !== 'player' && typeof(gtag) !== 'undefined') {
			gtag('config', $('meta[name=GOOGLE_ANALYTICS_TAG]').attr("content"), {
				'page_title' : 'sPage',
				'page_path': '/'+sLang+'/'+sPage.replace('_', '/')+'.html'
			 });
		}
		$('html, body').animate({
			scrollTop: 0
		});
		$('body').css({'cursor':'default'});
		return true;
	});
	promise.error(function(request,error) {
		if(console && console.log) {
			console.log('AJAX error');
		}
		$('body').css({'cursor':'default'});
		return false;
	});
}
function getWaitContents(oElmt, sClass) {
	sClass = sClass || 'loader';
	return oElmt.html('<div class="'+sClass+'"></div>');
}
function updateMenu(sPage) {
	/*main menu coloration*/
	$('.ajaxLink').each(function(){
		$(this).removeClass('currentPage');
		$(this).parent().removeClass('active');
	});
	$('#'+sPage).addClass('currentPage');
	$('#'+sPage).parent().addClass('active');
	/*sub menu coloration*/
	if($('#'+sPage).is('.sub_menu')){
		$('a:first', $('#'+sPage).closest('ul').closest('li')).addClass('currentPage');
	}
	return true;
}
/*JS NAVIGATION*/
function setHistoryAndMenu(sPage) {
	/*menu*/
	updateMenu(sPage);
	/*history*/
	if (!$.browser.msie || parseInt($.browser.version) >= 10) {
		history.pushState(null, sPage.replace('_', '/'), '/'+getLang()+'/'+sPage.replace('_', '/')+'.html');
	}
	/*set current page*/
	$('meta[name=app_current_page]').attr("content", sPage);
	$('meta[name=app_current_page]').trigger('change');
	/* title */
	$('title').text(sPage.replace('_', '/')+' - '+window.location.host);
	return true;
}
function updateLangSwitcher(sPage) {
	$.each($.parseJSON(getLangAvailable()), function(iIndex,sLangName) {
		$('#'+sLangName).attr('href', '/'+sLangName+'/'+sPage.replace('_', '/')+'.html');
	});
}
function getHasChanges(withSelect) {
    var hasChanges = false;
    $(":input:not(:button):not([type=hidden])").each(function () {
        if ((this.type === "text" || this.type === "textarea" || this.type === "hidden") && this.defaultValue !== this.value) {
            hasChanges = true;
            return false;             
		} else {
            if ((this.type === "radio" || this.type === "checkbox") && this.defaultChecked !== this.checked) {
                hasChanges = true;
                return false;                 
			} else {
                if ((this.type === "select-one" || this.type === "select-multiple") && withSelect) {
                    for (var x = 0; x < this.length; x++) {
                        if (this.options[x].selected !== this.options[x].defaultSelected) {
                            hasChanges = true;
                            return false;
                        }
                    }
                }
            }
        }
    });
    return hasChanges;
}
function ajaxFrameRebind() {
	$('.noscript').hide();
	/* tooltips */
	$('[data-toggle="tooltip"]').tooltip();
	/* images */
	if ($(".cbox")) {
		$(".cbox").colorbox({rel:'cbox'});
	}
}
/**
 * Get the ISO week date week number
 */
Date.prototype.getWeek = function () {
	// Create a copy of this date object
	var target  = new Date(this.valueOf());
	// ISO week date weeks start on monday
	// so correct the day number
	var dayNr   = (this.getDay() + 6) % 7;
	// ISO 8601 states that week 1 is the week
	// with the first thursday of that year.
	// Set the target date to the thursday in the target week
	target.setDate(target.getDate() - dayNr + 3);
	// Store the millisecond value of the target date
	var firstThursday = target.valueOf();
	// Set the target to the first thursday of the year
	// First set the target to january first
	target.setMonth(0, 1);
	// Not a thursday? Correct the date to the next thursday
	if (target.getDay() !== 4) {
		target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
	}
	// The weeknumber is the number of weeks between the 
	// first thursday of the year and the thursday in the target week
	return 1 + Math.ceil((firstThursday - target) / 604800000); // 604800000 = 7 * 24 * 3600 * 1000
};
/**
* Get the ISO week date year number
*/
Date.prototype.getWeekYear = function () 
{
	// Create a new date object for the thursday of this week
	var target	= new Date(this.valueOf());
	target.setDate(target.getDate() - ((this.getDay() + 6) % 7) + 3);
	return target.getFullYear();
};
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}