$(function(){ 
	var navMain = $(".navbar-collapse"); // avoid dependency on #id
	// "a:not([data-toggle])" - to avoid issues caused
	// when you have dropdown inside navbar
	navMain.on("click", "a:not([data-toggle])", null, function () {
		navMain.collapse('hide');
    });
});
function updateArticle(article_id) {
	if (!$.browser.msie || parseInt($.browser.version) >= 10) {
		history.pushState(null, 'documentation/'+article_id, '/'+getLang()+'/'+'documentation/'+article_id+'.html');
	}
	$(".cbox").colorbox({rel:'cbox'});
	Prism.highlightAll();
	if(typeof gtag !== 'undefined') {
		gtag('config', 'UA-132033845-1', {
			page_title : article_id,
			page_path: '/'+getLang()+'/documentation/'+article_id
		 });
	 }
	 updateMenu("documentation");
}