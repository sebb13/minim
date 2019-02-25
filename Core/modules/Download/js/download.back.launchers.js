$('div#ajaxFrame').on('click', 'button.showFilesButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'download_manageFiles',
			exw_action: 'Download::getManageFilesPage'
		},'download_manageFiles');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
		setHistoryAndMenu('download_manageFiles');
	});
	promise.error(function() {
		bootbox.alert('error');
	});
});
$('div#ajaxFrame').on('change', 'input[type="file"]', function() {
	var files = $(this)[0].files;
	if (files.length > 0) {
		// On part du principe qu'il n'y a qu'un seul fichier
		$.each(files, function(iKey, oFile) {
			$.each(oFile, function(sKey, sVal) {
				if(sKey === 'name') {
					if($('#addFileId').val() === '') {
						var prefix = '';
						var inputValue = sVal.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
						if(!isNaN(inputValue.substr(0, 1))) {
							prefix = 'f';
						}
						$('#addFileId').val(prefix+inputValue);
					}
					return true;
				}
			});
		});
	}
});
$('div#ajaxFrame').on('click', 'button.removeFile', function(e){
	e.preventDefault();
	e.stopPropagation();
	var fileId = $(this).attr('id').replace('remove-', '');
	bootbox.confirm($("[name='confirmDeleteFile']").val(), function(result){
		if(result) {
			var promise = genericRequest({
					app_token: getToken(), 
					content: 'download_manageFiles',
					exw_action: 'Download::deleteFile',
					fileId: fileId
				});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
			});
			promise.error(function() {
				bootbox.alert('error');
			});
		}
	});
});
$('div#ajaxFrame').on('click', '#addFileButton', function(e){
	e.preventDefault();
	e.stopPropagation();
	if(checkAddFileForm()) {
		$(".alert").css('display', 'none');
		$('#addFileForm').append('<input type="hidden" name="app_token" value="'+getToken()+'" />');
		$('#addFileForm').append('<input type="hidden" name="content" value="'+getCurrentPage()+'" />');
		$('#addFileForm').append('<input type="hidden" name="exw_action" value="Download::addFile" />');
		var formdata = (window.FormData) ? new FormData($('#addFileForm')[0]) : null;
		var data = (formdata !== null) ? formdata : $('#addFileForm').serialize();
		$(".progress").css('display', 'block');
		$.ajax({
			type: 'POST',
			cache:'false',
			contentType: false,
			processData: false,
			url: "https://"+window.location.hostname+"/index.php?page="+getCurrentPage()+"&lang="+getLang(),
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						$("#progressbar").attr('aria-valuenow', Math.round(percentComplete*100));
						$("#progressbar").text(Math.round(percentComplete*100)+'%');
						$("#progressbar").css('width', Math.round(percentComplete*100)+'%');
					}
				}, false);
				return xhr;
			},
			data: data,
			success: function(data){
				$('div#ajaxFrame').html(data);
			},
			error:function(){
				alert('error');
			}
		});
	}
});
$('div#ajaxFrame').on('click', 'button.to-copy', function(e){
	e.preventDefault();
	e.stopPropagation();
	var toCopy = $('#'+$(this).attr('data-to-copy'));
	toCopy.select();
	document.execCommand('copy');
	return false;
});