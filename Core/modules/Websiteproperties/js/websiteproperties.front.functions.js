$(function(){ 
	var navMain = $(".navbar-collapse"); // avoid dependency on #id
	// "a:not([data-toggle])" - to avoid issues caused
	// when you have dropdown inside navbar
	navMain.on("click", "a:not([data-toggle])", null, function () {
		navMain.collapse('hide');
    });
});
function updateArticle(article_id, mainPage) {
	if (!$.browser.msie || parseInt($.browser.version) >= 10) {
		history.pushState(null, mainPage+'/'+article_id, '/'+getLang()+'/'+mainPage+'/'+article_id+'.html');
	}
	$(".cbox").colorbox({rel:'cbox'});
	if(typeof gtag !== 'undefined') {
		setGoogleAnalytics(article_id, mainPage+'/'+article_id);
	}
	updateMenu(mainPage);
	Prism.highlightAll();
}

function getMainPage() {
	if(getCurrentPage().indexOf("documentation") === 0) {
		return 'documentation';
	}
	if(getCurrentPage().indexOf("plugins") === 0) {
		return 'plugins';
	}
	return false;
}