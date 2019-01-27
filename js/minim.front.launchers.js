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
$('form#cookiesConsentAcceptForm').on('click', 'button#cookiesConsentAcceptButton', function() {
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Core::CookiesConsent'
		});
	promise.success(function(data) {
		$('div.cookies-consent-banner').hide();
	});
});

$('meta[name=app_lang]').change(function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Core::getCookiesConsentBanner'
		});
	promise.success(function(data) {
		$('div.cookies-consent-banner').replaceWith(data);
	});
});