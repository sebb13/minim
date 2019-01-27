$('div#ajaxFrame').on('click', '.contact-send-button', function(e){
	e.preventDefault();
	e.stopPropagation();
	$(".alert").css('display', 'none');
	if(checkContactForm()) {
		$('#contactForm').append('<input type="hidden" name="app_token" value="'+getToken()+'" />');
		$('#contactForm').append('<input type="hidden" name="content" value="'+getCurrentPage()+'" />');
		$('#contactForm').append('<input type="hidden" name="exw_action" value="Contact::addMsg" />');
		var formdata = (window.FormData) ? new FormData($('#contactForm')[0]) : null;
		var data = (formdata !== null) ? formdata : $('#contactForm').serialize();
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
	jQuery('html, body').animate({scrollTop: 0}, 500);
});