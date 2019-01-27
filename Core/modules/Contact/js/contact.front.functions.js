function checkContactForm() {
	var errors = '';
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	if($("#contact_name").val() === '') {
		errors += '<li>'+$("#error_missing_name").val()+'</li>';
	}
	if($("#contact_email").val() === '') {
		errors += '<li>'+$("#error_missing_email").val()+'</li>';
	} else if(!reg.test($("#contact_email").val())) {
		errors += '<li>'+$("#error_invalid_email").val()+'</li>';
	}
	if($("#contact_msg").val() === '') {
		errors += '<li>'+$("#error_missing_msg").val()+'</li>';
	}
	if (errors !== '') {
		addMsg('danger', errors);
		return false;
	} else {
		return true;
	}
}