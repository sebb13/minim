<!doctype html>
<html lang="{__LANG__}" itemscope itemtype="http://schema.org/WebPage">
	<head>
		<!-- METAS -->
		{__METATAGS__}
	</head>
    <body>
	<!-- Fixed navbar -->
	<div class="navbar navbar-default navbar-fixed-top headroom home" >
		<div class="container">
			<div class="navbar-header">
				<!-- Button for smallest screens -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> 
				</button>
				<a class="navbar-brand ajaxLink" id="__home" href="home.html">
					<img src="{##SITE_URL##}/img/design/Logo.png" alt="minim">
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav pull-right menu">
                    {__MENU__}
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div> 
	<!-- /.navbar -->
	<div id="ajaxFrame" class="home">
        {__CONTENT__}
    </div>
    <div class="cTop"></div>
    {__COOKIES_CONSENT__}
	<!-- /social links -->
	<footer id="footer" class="top-space">
		<div class="footer1">
			<div class="container">
				<div class="row">
					<div class="col-lg-3 col-sm-6 col-xs-6 widget">
						<h3 class="widget-title">{__FOLLOW__}</h3>
						<div class="widget-body">
							<p class="follow-me-icons text-left">
								<a href="https://www.facebook.com/minimFramework/" target="_blank">
									<img src="/img/design/picto_facebook.png" />
								</a>&nbsp;
								<a href="https://github.com/sebb13/minim.git" target="_blank">
									<img src="/img/design/picto_github.png" />
								</a>
							</p>
							<br />
							<p class="follow-me-icons text-center">
								{__SOCIAL_NETWORKS__}
							</p>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6 col-xs-6 widget">
						<h3 class="widget-title">{__RAPID_ACCESS__}</h3>
						<div class="widget-body row">
							<div class="col-md-3">
								<img src="/img/design/picto_access.png" class="picto_access" />
							</div>
							<div class="col-md-9 col-sm-12 col-xs-12">
								<ul style="padding:0;">
									<li><a href="https://sebastien-boulard.webearthquake.com/" target="_blank">Sébastien Boulard</a></li>
									<li><a href="https://webearthquake.com" target="_blank">webearthquake</a></li>
									<li>Page <a href="/{##LANG##}/contact.html" class="ajaxLink" id="__contact">contact</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12 col-xs-12 widget">
						<h3 class="widget-title">{__QUOTES__}</h3>
						<div class="widget-body row">
							<div class="col-md-2 col-sm-2 col-xs-2 text-center">
								<img src="/img/design/picto_quote.png" class="picto_access" />
							</div>
							<div class="col-md-10 col-sm-10 col-xs-10">
								<p><i>"Écrivez des programmes qui effectuent une seule chose et qui le font bien."</i></p>
								<p class="thin-author">Douglas McIlroy</p>
							</div>
						</div>
					</div>
				</div> <!-- /row of widgets -->
			</div>
		</div>
		<div class="footer2">
			<div class="container">
				<div class="row">
					<div class="col-md-8 widget">
						<div class="widget-body">
							<p class="simplenav">
                                <a href="/{##LANG##}/home.html" class="ajaxLink" id="_home">{__HOME__}</a> | 
                                <a href="/{##LANG##}/framework.html" class="ajaxLink" id="_framework">{__FRAMEWORK__}</a> |
                                <a href="/{##LANG##}/documentation/operation.html" class="ajaxLink" id="_documentation_operation">{__DOCUMENTATION__}</a> | 
                                <a href="/{##LANG##}/plugins/contactPlugin.html" class="ajaxLink" id="_plugins_contactPlugin">{__PLUGINS__}</a> | 
                                <a href="/{##LANG##}/examples.html" class="ajaxLink" id="_examples">{__EXAMPLES__}</a> | 
                                <a href="/{##LANG##}/about.html" class="ajaxLink" id="_about">{__ABOUT__}</a> | 
                                <a href="/{##LANG##}/contact.html" class="ajaxLink" id="_contact">{__CONTACT__}</a> | 
								<a href="/{##LANG##}/legalNotices.html" class="ajaxLink" id="__legalNotices">{__LEGAL_NOTICES__}</a> | 
								<a href="/{##LANG##}/download.html" class="ajaxLink" id="_download"><i class="fa fa-download"></i></a>
							</p>
						</div>
					</div>
					<div class="col-md-4 widget">
						<div class="widget-body">
							<p class="text-right">
								minim by <a href="https://sebastien-boulard.webearthquake.com/" target="_blank">Sébastien Boulard</a> 
							</p>
						</div>
					</div>
				</div> <!-- /row of widgets -->
			</div>
		</div>
	</footer>
	{__CSS__}
	{__JS__}
</body>
</html>
