<!-- container -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="home.html">Accueil</a></li>
		<li class="active">download</li>
	</ol>
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-12 maincontent">
			<header class="page-header">
				<h1 class="page-title">Télécharger <i class="fa fa-download"></i></h1>
			</header>
		</article>
		<!-- /Article -->
	</div>
	<div class="row">
		<article class="col-sm-8 maincontent">
			<h3>
				Dans les archives disponibles, vous trouverez les sources de ce site.
			</h3>
			<p class="text-left">
				La page home.tpl intègre le système d'internationnalisation pour vous donner un exemple complet. Dans les autres pages, la plupart des textes sont en dur dans les templates puisque ce site ne propose qu'une langue.
			</p>
			<p class="text-left">
				Vous pouvez également récupérer les sources depuis <a href="https://github.com/sebb13/minim" target="_blank">https://github.com</a>.
			</p>
		</article>
		<!-- Sidebar créer un widget -->
		<aside class="col-sm-4 maincontent">
			<p>
				<a class="btn btn-primary btn-large btn-dl" href="https://github.com/sebb13/minim" target="_blank">Récupérer depuis GitHub <i class="fa fa-github" aria-hidden="true"></i></a>
			</p>
			<p>
				<a class="btn btn-primary btn-large btn-dl" href="{##FRONT_URL##}{##LANG##}/downloadFile.html?file_id=minimwebearthquakecomzip">Télécharger les sources (.zip)</a>
			</p>
			<p>
				<a class="btn btn-primary btn-large btn-dl" href="{##FRONT_URL##}{##LANG##}/downloadFile.html?file_id=minimwebearthquakecomtargz">Télécharger les sources (.tar.gz)</a>
			</p>
			<p>
				<a class="btn btn-primary btn-large btn-dl" href="{##FRONT_URL##}{##LANG##}/downloadFile.html?file_id=LICENSEmd">Télécharger la license</a>
			</p>
		</aside>
		<!-- /Sidebar -->
	</div>
	<hr />
	<div class="row">
		<article class="col-sm-8 maincontent">
			<h3>Installation / prérequis</h3>
			<p class="text-left">
				Pour installer minim il vous faut : 
			</p>
			<ul class="cog-tools">
				<li>Un peu de temps.</li>
				<li>Un serveur web avec PHP 5.3 minimum.</li>
				<li>Les droits daccès en (S)FTP.</li>
				<li>La possibilité de créer des domaines, des sous domaine et de paramétrer les entrées DNS afin qu'ells pointent sur le(s) bon(s) répertoire(s) de votre hébergement.</li>
			</ul>
			<p class="text-left">
				L'installation se fait ensuite en suivant les instructions dans la <a href="{##FRONT_URL##}{##LANG##}/documentation/operation.html" id="documentation_operation" class="ajaxLink">documentation</a>.
			</p>
			<p class="text-left">
				Vous pouvez bien sûr l'installer en local en adaptant le fichier <strong>inc.coreAutoConfig.php</strong> à votre environnement.
			</p>
		</article>
		<!-- Sidebar créer un widget -->
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<img src="/img/contents/doc/dns.png" alt="DNS" class="img-rounded" />
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
	<hr />
	<div class="row">
		<article class="col-sm-8 maincontent">
			<h3>Supportez le projet !</h3>
			<p>Vous pouvez participer à l'hébergement de cette documentation (par exemple) !</p>
			<p>Vous pouvez faire un don en bitcoin à cette adresse : 3E7559xyiKDhU4ZJ6N5hLqYt6c18MLScwE</p>
			<p>Vous pouvez aussi être affilié au programme computta via ce lien de parrainage : <a href="https://computta.com/?ref=376437" target="_blank">https://computta.com/?ref=376437</a> et ainsi gagner des bitcoins tout en nous en faisant profiter !</p>
		</article>
		<!-- Sidebar créer un widget -->
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget text-center">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick" />
					<input type="hidden" name="hosted_button_id" value="T9AHTM6CHJJPC" />
					<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
					<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
				</form>
			</div>
			<div class="widget text-center">
				<a href="https://computta.com/?ref=376437" target="_blank" title="computta">
					<img src="/img/contents/300px_computta_blue.png" alt="computta" class="img-rounded" />
				</a>
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
</div>	<!-- /container -->