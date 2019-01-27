$(".navbar-nav li a").click(function(event) {
	$(".navbar-collapse").collapse('hide');
});
if(getCurrentPage().indexOf("documentation") === 0) {
	var article_id = getCurrentPage().replace('documentation_', '');
	if(article_id === '' || article_id === 'documentation') {
		article_id = 'operation'
	}
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'articles',
			exw_action: 'Websiteproperties::getArticle',
			article_id: article_id
		});
	promise.success(function(data) {
		updateArticle(article_id);
		$('#article-contents').html(data);
		$('.btn-primary').removeClass('btn-active');
		$('#'+article_id).addClass('btn-active');
		$('#'+article_id+'_bottom').addClass('btn-active');
	});
}
$('meta[name=app_current_page]').change(function(){
	if(getCurrentPage().indexOf("documentation") === 0) {
		var article_id = getCurrentPage().substring(getCurrentPage().indexOf("_")+1);
		if(article_id === "documentation") {
			article_id = "operation";
		}
		var promise = genericRequest({
			app_token: getToken(), 
			content: 'documentation',
			exw_action: 'Websiteproperties::getArticle',
			article_id: article_id
		});
		promise.success(function(data) {
			updateArticle(article_id);
			$('#article-contents').html(data);
			$('.btn-primary').removeClass('btn-active');
			$('#'+article_id).addClass('btn-active');
			$('#'+article_id+'_bottom').addClass('btn-active');
			jQuery('html, body').animate({scrollTop: 0}, 300);
			$(".cbox").colorbox({rel:'cbox'});
		});
	}
	Prism.highlightAll();
});
$('div#ajaxFrame').on('click', '.article', function(e){
	e.preventDefault();
	e.stopPropagation();
	$('body').css({'cursor':'wait'});
	var article_id = $(this).attr('id').replace('_bottom', '');
	var tabClass = $('#'+article_id).find("div").attr('class');
	$('#'+article_id).find("div").attr("class", "loader");
	$('#'+article_id+'_bottom').find("div").attr("class", "loader");
	var promise = genericRequest({
		app_token: getToken(), 
		content: 'documentation',
		exw_action: 'Websiteproperties::getArticle',
		article_id: article_id
	});
	promise.success(function(data) {
		updateArticle(article_id);
		$('.btn-primary').removeClass('btn-active');
		$('#article-contents').html(data);
		$('body').css({'cursor':'default'});
		$('#'+article_id).addClass('btn-active');
		$('#'+article_id+'_bottom').addClass('btn-active');
		jQuery('html, body').animate({scrollTop: 0}, 300);
		$('#'+article_id).find("div").attr("class", tabClass);
		$('#'+article_id+'_bottom').find("div").attr("class", tabClass);
	});
});