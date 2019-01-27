if($('.deleteMsgButton').is(':visible')) {
	$('.showMessagesButton').show();
	$('.showArchivedMessagesButton').hide();
} else {
	$('.showMessagesButton').hide();
	$('.showArchivedMessagesButton').show();
}
if($('.archiveMsgButton').is(':visible')) {
	$('.showMessagesButton').hide();
	$('.showArchivedMessagesButton').show();
} else {
	$('.showMessagesButton').show();
	$('.showArchivedMessagesButton').hide();
}
//navigation
$('div#ajaxFrame').on('click', 'button.showMessagesButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'contact_messagesReceived',
			exw_action: 'Contact::getMsgsPage'
		},'contact_messagesReceived');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
		setHistoryAndMenu('contact_messagesReceived');
		$('.showMessagesButton').hide();
		$('.showArchivedMessagesButton').show();
	});
	promise.error(function() {
		bootbox.alert('error');
	});
});
$('div#ajaxFrame').on('click', 'button.showArchivedMessagesButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'contact_archivedMessages',
			exw_action: 'Contact::getArchivesPage'
		},'contact_archivedMessages');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
		setHistoryAndMenu('contact_archivedMessages');
		$('.showArchivedMessagesButton').hide();
		$('.showMessagesButton').show();
	});
	promise.error(function() {
		bootbox.alert('error');
	});
});
// archive delete restore
$('div#ajaxFrame').on('click', 'input.archiveMsgButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var sMsgId = $(this).closest('.msgForm').find('.contact_id').val();
	bootbox.confirm($("[name='confirmArchiveConfirm']").val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: 'contact_messagesReceived',
					exw_action: 'Contact::archiveMsg',
					sMsgId: sMsgId
				}, 'contact_messagesReceived');
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
$('div#ajaxFrame').on('click', 'input.restoreMsgButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'contact_messagesReceived',
			exw_action: 'Contact::restoreMsg',
			sMsgId: $(this).closest('.msgForm').find('.contact_id').val()
		}, 'contact_messagesReceived');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.deleteMsgButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var sMsgId = $(this).closest('.msgForm').find('.contact_id').val();
	bootbox.confirm($("[name='confirm_delete']").val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: 'contact_archivedMessages',
					exw_action: 'Contact::deleteMsg',
					sMsgId: sMsgId
				}, 'contact_archivedMessages');
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
//comments
$('div#ajaxFrame').on('click', 'input.updateCommentButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Contact::getEditComment',
			sMsgId: $(this).closest('.msgForm').find('.contact_id').val(),
			sComment: $(e.target).closest('.msgForm').find('.msgComment').text()
		});
	promise.success(function(data) {
		$(e.target).closest('.msgForm').find('.comment-zone').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.saveCommentButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Contact::saveComment',
			sMsgId: $(this).closest('.msgForm').find('.contact_id').val(),
			sComment: $(this).closest('.msgForm').find('.msgComment').val()
		});
	promise.success(function(data) {
		$(e.target).closest('.msgForm').find('.comment-zone').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#msgSearchButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'contact_messagesReceived',
			exw_action: 'Contact::searchMsg',
			sKeyword: $('#msgKeyword').val()
		}, 'contact_messagesReceived');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
	});
	promise.error(function() {
		bootbox.alert('error');
	});
	return false;
});