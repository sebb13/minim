$(".navbar-nav li a").click(function(event) {
	$(".navbar-collapse").collapse('hide');
});
if(getMainPage() !== false) {
	var article_id = getCurrentPage().substring(getCurrentPage().indexOf("_")+1);
	$('.btn-primary').removeClass('btn-active');
	$('#'+article_id).addClass('btn-active');
	$('#'+article_id+'_bottom').addClass('btn-active');
	updateArticle(article_id, getMainPage());
}
$('meta[name=app_current_page]').change(function(){
	if(getMainPage() !== false) {
		var article_id = getCurrentPage().substring(getCurrentPage().indexOf("_")+1);
		if(article_id === '' || article_id === getMainPage()) {
			article_id = $('.btn-active').attr('id').replace('_bottom', '');
		}
		var promise = genericRequest({
			app_token: getToken(), 
			content: article_id,
			exw_action: 'Websiteproperties::getArticle',
			article_id: article_id
		});
		promise.success(function(data) {
			$('#article-contents').html(data);
			$('.btn-primary').removeClass('btn-active');
			$('#'+article_id).addClass('btn-active');
			$('#'+article_id+'_bottom').addClass('btn-active');
			jQuery('html, body').animate({scrollTop: 0}, 300);
			updateArticle(article_id, getMainPage());
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
		content: article_id,
		exw_action: 'Websiteproperties::getArticle',
		article_id: article_id
	});
	promise.success(function(data) {
		$("#article-contents").fadeOut('500', function() {
			$("#article-contents").html(data);
			$('.btn-primary').removeClass('btn-active');
			$('#article-contents').html(data);
			$('body').css({'cursor':'default'});
			$('#'+article_id).addClass('btn-active');
			$('#'+article_id+'_bottom').addClass('btn-active');
			jQuery('html, body').animate({scrollTop: 0}, 300);
			$('#'+article_id).find("div").attr("class", tabClass);
			$('#'+article_id+'_bottom').find("div").attr("class", tabClass);
			updateArticle(article_id, getMainPage());
			$("#article-contents").fadeIn('500');
		});
		
	});
});