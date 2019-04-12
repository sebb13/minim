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
bootbox.setDefaults({
  locale: (getLang() === 'GB' ? 'en' : getLang().toLowerCase()),
});
$('#qn').quicknote({
	theme: 'dark',
	pos: 'right',
	storage: true, 
});
if($('form#loginForm').is(':visible')) {
	$('button.userHeaderButton').hide();
} else {
	$('button.userHeaderButton').show();
}
var sStartPage = window.location.pathname.substring(window.location.pathname.lastIndexOf('/')+1);
if(!$('form#loginForm').is(':visible') && sStartPage === 'login.html') {
	setHistoryAndMenu('home');
}
$('.menu').on('click', 'a.sub_menu', function(){
	$('li.dropdown').removeClass('open');
});
$('div#ajaxFrame').on('click', 'button.backHomeButton', function(){
	window.location = '/';
});
$('.summernote').summernote({airMode: true});
$('.note-editable').first().focus();

getErrorLogsGraph();
$(document).on('change', 'meta[name=app_current_page]', function(){
	if(getCurrentPage() === 'system_errorLogs' || getCurrentPage() === 'system_summary') {
		var chartTarget = '#chart_div';
		var chartWidth = $('#chart_div').parent().parent()-20;
		getErrorLogsGraph(chartTarget, chartWidth);
	} 
	if(getCurrentPage() === 'home') {
		var chartTarget = '#chart_div_home';
		var chartWidth = $('#chart_div_home').parent().parent()-20;
		getErrorLogsGraph(chartTarget, chartWidth);
	}
	bootbox.setDefaults({
		locale: (getLang() === 'GB' ? 'en' : getLang().toLowerCase()),
	});
	$('.summernote').summernote({airMode: true});
	$('.note-editable').first().focus();
});
$(document.body).on('click', 'button,radio', function(){
	$(this).blur();
	return true;
});
$(document.body).on('click', 'button.close, a.ajaxLink', function(){
	$('meta[name=app_current_page]').trigger('change');
	return true;
});


$('div#ajaxFrame').on('click', '#to-copy', function(e){
	$('#to-copy-target').select();
	document.execCommand( 'copy' );
	return false;
});

$(document.body).on('click', 'button#editUserButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'user',
			exw_action: 'User::getUserPage',
		}, 'user');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		setHistoryAndMenu('user');
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button#addUserButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'User::addUser',
			login: $('input#user_login').val(),
			pwd: $('input#user_pwd').val(),
			role: $('select#user_role').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.removeUserButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var user = $(this).prev().val();
	var sConfirmText = $('input#confirmDeleteUserAccountMsg').val() + ' ('+user+')';
	bootbox.confirm(sConfirmText, function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'User::deleteUser',
					user: user
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('meta[name=app_current_page]').trigger('change');
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button#updateUserButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	bootbox.confirm($('input#confirmUpdateUserAccountMsg').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'User::updateUser',
					user: $('input#currentUserLogin').val(),
					login: $('input#login').val(),
					pwd: $('input#pwd').val(),
					currentPwd: $('input#currentPwd').val()
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('#editUserButton').html($('input#login').val());
				$('meta[name=app_current_page]').trigger('change');
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button#updateUsersButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	bootbox.confirm($('input#confirmUpdateUsersAccountMsg').val(), function(result){
		if(result) {
			var promise = genericRequest({
				app_token: getToken(), 
				content: getCurrentPage(),
				exw_action: 'User::updateUsers',
				users: $('#updateUsersForm').serializeArray()
			});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('meta[name=app_current_page]').trigger('change');
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('keyup', 'input[name^="login"]', function(e){
	var regex = new RegExp(/([^A-Za-z0-9\-])/);
	if (regex.test($(this).val())) {
		$(this).css({color:'#fff', 'background-color':'#c83834'});
		return false;
	} else {
		$(this).css({color:'#555555', 'background-color':'#fff'});
	}
});
$('div#ajaxFrame').on('submit', 'form.checkUpdateForm', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Core::getPageConfig',
			pageToConfigure: $('select#pageToConfigure').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.checkUpdateButton', function(){
	getWaitContents($(this).parent().prev());
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Updates::checkUpdate',
			sModuleToCheck: $(this).prev().val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.generateUpdatesButton', function(){
	var sMainVersion = $(this).prev().parent().find('input[name=main-version]').val();
	var sSubVersion = $(this).parent().parent().find('input[name=sub-version]').val();
	var sRelease = $(this).parent().parent().find('input[name=release]').val();
	var sModuleName = $(this).parent().parent().find('input[name=module-name]').val();
	var spinnerDiv = $(this).parent();
	bootbox.confirm($('input#confirmGenerateUpdates').val(), function(result){
		if(result) {
			getWaitContents(spinnerDiv);
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Updates::generateUpdates',
					sMainVersion: sMainVersion,
					sSubVersion: sSubVersion,
					sRelease: sRelease,
					sModuleName: sModuleName
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.updateSystemButton', function(){
	var sModuleName = $(this).prev().val();
	var sConfirmMsg = $(this).prev().prev().val();
	var spinnerDiv = $(this).parent();
	bootbox.confirm(sConfirmMsg, function(result){
		if(result) {
			getWaitContents(spinnerDiv);
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Updates::installUpdates',
					sModuleName: sModuleName
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.rollback', function(){
	var sModuleName = $(this).parent().parent().find('input[name=module_name]').val();
	var sConfirmMsg = $(this).parent().parent().find('input[name=confirmRollbackMsg]').val();
	var sVersion = $(this).parent().parent().find('input[name=version]').val();
	var spinnerDiv = $(this).parent();
	bootbox.confirm(sConfirmMsg, function(result){
		if(result) {
			getWaitContents(spinnerDiv);
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Updates::rollback',
					sModuleName: sModuleName,
					sVersion: sVersion
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.purgeBackup', function(){
	var sModuleName = $(this).parent().parent().find('input[name=module_name]').val();
	var sConfirmMsg = $(this).parent().parent().find('input[name=confirmPurgeBackupMsg]').val();
	var sVersion = $(this).parent().parent().find('input[name=version]').val();
	var spinnerDiv = $(this).parent();
	bootbox.confirm(sConfirmMsg, function(result){
		if(result) {
			getWaitContents(spinnerDiv);
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Updates::purgeBackup',
					sModuleName: sModuleName,
					sVersion: sVersion
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('submit', 'form.systemForm', function(e){
	e.preventDefault();
	e.stopPropagation();
	var aPage = $(this).attr("action").split("/");
	var sPage = (aPage.length === 4) ? aPage[2] + '_' + aPage[3] : aPage[2];
	sPage = sPage.substring(0, sPage.indexOf("."));
	loadHtml(sPage);
	setHistoryAndMenu(sPage);
	return false;
});
$('div#ajaxFrame').on('change', 'select#pageToConfigure', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Pages::getPageConfig',
			pageToConfigure: $('select#pageToConfigure').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return true;
});
$('div#ajaxFrame').on('click', 'input#addPage', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Pages::addPageToConfigure',
			newPageName: $('input#newPageName').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	return true;
});
$('div#ajaxFrame').on('click', 'input#deleteConf', function(e){
	bootbox.confirm($('input#deletePageConfConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Pages::deletePageToConfigure',
					deletePageName: $('select#pageToConfigure').val()
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('meta[name=app_current_page]').trigger('change');
			});
		}
		return true;
	});
});
$('div#ajaxFrame').on('click', 'input#last-mod', function(e){
	bootbox.confirm($('input#regenerateSitemapConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Pages::regenerateSitemap'
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
		}
		return true;
	});
});
$('div#ajaxFrame').on('click', 'button.deletePageToIgnore', function(e){
	var pagename = $(this).parent().parent().find('.pageToIgnorename').val();
	bootbox.confirm($('input#deletePageToIgnoreConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Pages::deletePageToIgnore',
					pagename: pagename
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
		}
		return true;
	});
});
$('div#ajaxFrame').on('click', 'button#addPageToIgnoreButton', function(e){
	var pagename = $('#addPageToIgnore').val();
	bootbox.confirm($('input#addPageToIgnoreConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Pages::addPageToIgnore',
					pagename: pagename
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
		}
		return true;
	});
});
$('div#ajaxFrame').on('change', 'select#sFileToTranslate, select#sRefLang, select#sLangToTranslate', function(e){
	if (getHasChanges(false) === true) {
		if (!confirm('Êtes vous sûr de ne pas vouloir enregistrer vos modifications ?')) {
			for (var x = 0; x < this.length; x++) {
				this.options[x].selected = this.options[x].defaultSelected;
			}
			return false;
		}
	}
	getWaitContents($('#translations_container'));
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Translations::getTranslationsInterface',
			sFileToTranslate: $('select#sFileToTranslate').val(),
			sRefLang: $('select#sRefLang').val(),
			sLangToTranslate: $('select#sLangToTranslate').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return true;
});
$('div#ajaxFrame').on('click', '#resetTranslationsDraft', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Translations::resetDraft',
			sFileToTranslate: $('select#sFileToTranslate').val(),
			sRefLang: $('select#sRefLang').val(),
			sLangToTranslate: $('select#sLangToTranslate').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('.summernote').summernote({airMode: true});
		jQuery('html, body').animate({scrollTop: 0}, 300);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', '#saveTranslationsButton', function(e){
	var oTranslates = {};
	$('.summernote').each(function(){ 
	   oTranslates[$(this).attr('id')] = $(this).next().find('.note-editable').html();
	});
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Translations::saveTranslations',
			translates:oTranslates,
			sFileToTranslate: $('select#sFileToTranslate').val(),
			sRefLang: $('select#sRefLang').val(),
			sLangToTranslate: $('select#sLangToTranslate').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('.summernote').summernote({airMode: true});
		jQuery('html, body').animate({scrollTop: 0}, 300);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', '#publishTranslationsDraft', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Translations::publishTranslations',
			sFileToTranslate: $('select#sFileToTranslate').val(),
			sRefLang: $('select#sRefLang').val(),
			sLangToTranslate: $('select#sLangToTranslate').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('.summernote').summernote({airMode: true});
		jQuery('html, body').animate({scrollTop: 0}, 300);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('change', 'select#moduleToConfigure, select#sideToConfigure', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Routing::getRoutingInterface',
			sModuleToConfigure: $('select#moduleToConfigure').val(),
			sSideToConfigure: $('select#sideToConfigure').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button#getNewRouteEntries', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Routing::getNewRouteEntries',
			sType: $('#type').val(),
			nbRoutesToAdd: $('#nbRoutesToAdd').val(),
			sModuleToConfigure: $('select#moduleToConfigure').val(),
			sSideToConfigure: $('select#sideToConfigure').val()
		});
	promise.success(function(data) {
		$('div#newRoutes').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.removeRoute', function(e){
	e.preventDefault();
	e.stopPropagation();
	var sPageToRemove = $(this).attr('id');
	bootbox.confirm($('input#removeRouteConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Routing::removeRoute',
					sType: $('#type').val(),
					sPageToRemove: sPageToRemove,
					sModuleToConfigure: $('select#moduleToConfigure').val(),
					sSideToConfigure: $('select#sideToConfigure').val()
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', '#saveRoutesButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Routing::updateRoutes',
			routes:$('#routingForm').serialize(),
			sModuleToConfigure: $('select#moduleToConfigure').val(),
			sSideToConfigure: $('select#sideToConfigure').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.draftPreviewButton', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Drafts::getDraftUrl',
			sPage: $(this).prev().val(),
			sLang: $(this).parent().prev().find('select').val(),
			sSide: getCurrentPage() === 'translations_back' ? 'BACK' : 'FRONT'
		});
	promise.success(function(data) {
		window.open(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.versionPreviewButton', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Versions::getVersionUrl',
			sPage: $(this).prev().val(),
			sVersion: $(this).prev().prev().val(),
			sLang: $(this).parent().prev().find('select').val()
		});
	promise.success(function(data) {
		window.open(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.applyVersionButton', function(e){
	var sPage = $(this).prev().val();
	var sVersion = $(this).prev().prev().val();
	bootbox.confirm($('input#applyVersionConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Versions::applyVersion',
					sPage: sPage,
					sVersion: sVersion
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.purgeVersionsButton', function(e){
	bootbox.confirm($('input#purgeVersionsConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Versions::purgeVersions',
					sPage: $(this).prev().val()
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.deleteVersionButton', function(){
	var sPage = $(this).prev().val();
	bootbox.confirm($('input#deleteVersionConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Versions::deleteVersion',
					sPage: sPage
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'a.errorLogsEntry', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Logs::getErrorLogs',
			sDay:$(this).attr('alt')
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#purgeOldLogsButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	bootbox.confirm($('input#purgeOldLogsConfirm').val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: getCurrentPage(),
					exw_action: 'Logs::purgeOldLogs'
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('meta[name=app_current_page]').trigger('change');
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
	return false;
});
