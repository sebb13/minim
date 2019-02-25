function checkAddFileForm() {
	var errors = '';
	if($("#addFileId").val() === '') {
		errors += '<li>'+$("#error_missing_file_id").val()+'</li>';
	}
	$('.removeFile').each(function(){
		if($(this).attr('id').replace('remove-', '') === $("#addFileId").val()) {
			errors += '<li>'+$("#error_file_id_already_exist").val()+'</li>';
		}
	});
	if(!isNaN($("#addFileId").val().substr(0, 1))) {
		errors += '<li>'+$("#error_file_id_must_begin_with_letter").val()+'</li>';
	}
	var exp = new RegExp("^[a-zA-Z0-9]$","g");
	if(exp.test($("#addFileId").val())) {
		errors += '<li>'+$("#error_file_id_must_contain_only_nums_and_letters").val()+'</li>';
	}
	if (errors !== '') {
		addMsg('danger', errors);
		return false;
	} else {
		return true;
	}
}