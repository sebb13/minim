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
ajaxFrameRebind();
$('.nav a').on('click', function(){
	if($('.sr-only').is(':visible')) {
		$('.navbar-toggle').click();
	}
});
$('div#ajaxFrame').on('click', 'button, input[type=button]', function(e){
	$(this).blur();
});
$('meta[name=app_current_page]').change(function(){
	ajaxFrameRebind();
});
$('div#ajaxFrame').on('click', '[data-toggle="tooltip"]', function(e) {
	e.preventDefault();
	return false;
});
var duration = 300;
jQuery(window).scroll(function() {
	if(jQuery(this).scrollTop() > 100) {
		// Si un défillement de 100 pixels ou plus.
		// Ajoute le bouton
		jQuery('.cTop').fadeIn(duration);
	} else {
		// Sinon enlève le bouton
		jQuery('.cTop').fadeOut(duration);
	}
});
jQuery('.cTop').click(function(event) {
	// Un clic provoque le retour en haut animé.
	event.preventDefault();
	jQuery('html, body').animate({scrollTop: 0}, duration);
	return false;
});
/*MENU STATE*/
updateMenu(getCurrentPage());
/*MENU*/
$('ul.menu').on('click', 'a.ajaxLink', function(e) {
	$(this).blur();
	$.ajaxQ.abortAll();
	e.preventDefault();
	e.stopPropagation();
	var sPage = $(this).attr("id");
	return loadHtml(sPage);
});
/*ALTERNATIVES ACCESS*/
$('body').on('click', 'a.ajaxLink', function(e) {
	$(this).blur();
	$.ajaxQ.abortAll();
	e.preventDefault();
	e.stopPropagation();
	var sPage = $(this).attr("id");
	/*if id start by underscore, or more*/
	if(sPage.indexOf("_____") === 0) {
		sPage = sPage.substring(sPage.indexOf("_____")+5);
	}
	else if(sPage.indexOf("____") === 0) {
		sPage = sPage.substring(sPage.indexOf("____")+4);
	}
	else if(sPage.indexOf("___") === 0) {
		sPage = sPage.substring(sPage.indexOf("___")+3);
	}
	else if(sPage.indexOf("__") === 0) {
		sPage = sPage.substring(sPage.indexOf("__")+2);
	}
	else if(sPage.indexOf("_") === 0) {
		sPage = sPage.substring(sPage.indexOf("_")+1);
	}
	return loadHtml(sPage);
});
/*LANGUAGE OBSERVER*/
$('#langSwitcher').on('click', 'a.lang-button', function(e){
	$.ajaxQ.abortAll();
	e.preventDefault();
	e.stopPropagation();
	var sPage = getCurrentPage();
	var sLang = $(this).attr('id');
	$('.lang-button').each(function(){
		$(this).removeClass('on');
		$(this).addClass('off');
	});
	$(this).removeClass('off');
	$(this).addClass('on');
	setLang(sLang);
	loadHtml('menu');
	return loadHtml(sPage);
});
/* dialog */
if($('#systemDialog').length === 1) {
	$('#systemDialog').dialog({
		modal: true,
		buttons: {
			Ok: function() {
				$(this).dialog("close");
			}
		}
	});
}