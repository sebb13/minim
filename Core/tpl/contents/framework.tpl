<!-- container -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="home.html">Accueil</a></li>
		<li class="active">Le framework</li>
	</ol>
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-12 maincontent">
			<header class="page-header">
				<h1 class="page-title">Le framework <i class="fa fa-cogs"></i></h1>
				<h3>Peu de code, beaucoup de possibilités</h3>
			</header>
		</article>
		<!-- /Article -->
	</div>
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-12 maincontent">
			<h3>Conventions - normes</h3>
		</article>
		<article class="col-sm-8 maincontent">
			<h4>Les classes PHP</h4>
			<p>Plusieurs types de fichiers PHP peuvent être intégrés en respectant ces quelques normes :</p>
			<ul class="cog-tools">
				<li><strong>class.MyClass.php :</strong> Vos classes métier ou de gestion de modèle (pris en charge par l'autoloader).</li>
				<li><strong>svc.MyService.php :</strong> Vos services. En effet, minim respecte une structure <strong>MVC</strong>, tout ce qui concerne les vues doit être appelé via un service (pris en charge par l'autoloader).</li>
				<li><strong>package.MyPackage.php :</strong> permet de regrouper plusieurs classes similaires comme pour la gestion des sessions. Il faut pour les intégrer les inclure dans le fichier <strong>inc.coreAutoConfig.php</strong>.</li>
				<li><strong>Toolz_MyTool.php :</strong> Vous pouvez ajouter vos classes d'outils dans le répertoire <strong>Toolz</strong>. Pour les appeler, <code class="language-php">Toolz_MyTool::myMethod();</code></li>
				<li><strong>Attention :</strong> Toutes les classes des modules installés doivent être copiées dans <strong>/Core/lib/modules/</strong> afin d'être prisent en compte.</li>
			</ul>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p class="txt-center">
					<a href="/img/contents/doc/autoloader.png" rel="cbox" title="autoload" class="cbox">
						<img src="/img/contents/doc/autoloader.png" alt="autoloader" class="img-rounded" />
					</a>
				</p>
			</div>
		</aside>
		<!-- /Sidebar -->
		<article class="col-sm-8 maincontent">
			<h4>Les variables</h4>
			<p>Afin de faciliter la relecture du code, nous utilisons une convention de nommage des variables assez simple à comprendre.</p>
			<ul class="cog-tools">
				<li>Le nom des variables est en anglais</li>
				<li>Les variables qui contiennent une valeur de type <strong>int</strong> commencent par un "<strong>i</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>float</strong> commencent par un "<strong>f</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>string</strong> commencent par un "<strong>s</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>array </strong>commencent par un "<strong>a</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>objet</strong> commencent par un "<strong>o</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>ressource</strong> commencent par un "<strong>r</strong>" minuscule.</li>
				<li>Les variables qui contiennent une valeur de type <strong>mixte</strong> commencent par un "<strong>m</strong>" minuscule.</li>
			</ul>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p>
					<a href="/img/contents/doc/convention_variables.png" rel="cbox" title="convention de nommage des variables" class="cbox">
						<img src="/img/contents/doc/convention_variables.png" alt="convention de nommage des variables" class="img-rounded" />
					</a>
				</p>
				<p>
					Ce type de nommage permet aussi de simplifier l'utilisation d'opérateurs de comparaison strictes ("===", "!==").
				</p>
			</div>
		</aside>
		<article class="col-sm-8 maincontent">
			<h3>MVC</h3>
			<p>Cerise sur le gâteau, minim vous propose un système <a href="https://fr.wikipedia.org/wiki/Mod%C3%A8le-vue-contr%C3%B4leur" target="_blank">MVC</a></p>
			<p>Modèle-vue-contrôleur ou MVC est un motif d'architecture logicielle destiné aux interfaces graphiques lancé en 1978 et très populaire pour les applications web. Le motif est composé de trois types de modules ayant trois responsabilités différentes : les modèles, les vues et les contrôleurs.</p>
			<ul class="cog-tools">
				<li>Un modèle (Model) contient les données à afficher.</li>
				<li>Une vue (View) contient la présentation de l'interface graphique.</li>
				<li>Un contrôleur (Controller) contient la logique concernant les actions effectuées par l'utilisateur.</li>
			</ul>
			<p class="thin-author">Source : wikipedia.</p>
			<p>Dans minim :</p>
			<ul class="cog-tools">
				<li>Toutes les vues et pages sont dans des templates, certains sont construits à la volée.</li>
				<li>Les traductions à injecter dans les templates sont rangées par langue dans des fichiers <strong>XML</strong>.</li>
				<li>Il convient de créer des classes spécifiques à la gestion des données (<strong>CRUD</strong>).</li>
				<li>Au niveau PHP, ce sont les services (<strong>svc.MyService.php</strong>) qui génèrent les vues.</li>
				<li>Enfin, les classes de modèle et les classes métiers se nomment <strong>class.MyClass.php</strong>.</li>
			</ul>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<p>
					<a href="/img/contents/doc/ModeleMVC.png" rel="cbox" title="Le modèle MVC" class="cbox">
						<img src="/img/contents/doc/ModeleMVC.png" alt="Le modèle MVC" class="img-rounded" />
					</a>
				</p>
				<p class="thin-author">
					Source : wikipedia.
				</p>
			</div>
		</aside>
	</div>
    <hr />
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h3>Internationalisation</h3>
			<p>Comme vous pouvez le voir dans l'arborescence en illustration, les traductions du front se trouvent à la racine, les traductions du backoffice se trouvent dans le dossier admin et enfin les traductions communes dans le répertoire common.</p>
			<p>L'organisation est la même dans les modules.</p>
			<h4>Exemple de traduction :</h4>
			<pre><code class="language-markup">
				&#60?xml version="1.0" encoding="UTF-8"?&#62
				&#60translations&#62
					&#60TOOLS&#62
						<[CDATA[Outils]]>
					&#60/TOOLS&#62
				&#60/translations&#62
			</code></pre>
			<h4>Exemple d'utilisation dans les templates :</h4>
			<pre><code class="language-markup">
				&#60ul&#62
					&#60li&#62
						{__TOOLS__}
					&#60/li&#62
				&#60/ul&#62
			</code></pre>
			<p><strong>Attention :</strong> les fichiers de traductions doivent porter le même nom que le template associé.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<img src="/img/contents/doc/localesDir.png" alt="arborescence des fichiers de traductions" class="img-rounded" />
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
	<hr />
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h3>Sécurité</h3>
			<p>Pour la sécurité, nous avons mis en place :</p>
			<ul class="cog-tools">
				<li>Un système d'authentification (backoffice) personnalisable.</li>
				<li>La possibilité d'une double authentification (backoffice) par email.</li>
				<li>Un token d'application.</li>
				<li>Des formulaires sans captcha.</li>
			</ul>
			<p>Vous pouvez bien sûr adapter tout cela à vos besoins !</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<div class="widget">
					<img src="/img/contents/doc/security.jpg" alt="Sécurité" class="img-rounded" />
				</div>
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
    <hr />
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h3>Cache</h3>
			<p>Le système de cache est apparut naturellement avec l'internationalisation.</p>
			<p>Devoir lire le template, le fichier de traduction correspondant en fonction de la langue demandée et générer le html adapté demandait alors beaucoup de travail pouvant être évité.</p>
			<p>Le cache a donc été mis en place dans ce sens à la base, puis a été étendu autant que possible, épargnant de fait la charge du serveur hôte.</p>
			<p>Come vous pouvez vous en douter, les fichiers de cache du backoffice se trouvent dans le répertoire "admin" et les fichiers de cache du noyau se trouvent dans le répertoire "core".</p>
			<p>Dans le cache du noyau, pour l'instant un seul fichier : <strong>modulesAvailable.list</strong>. Ce fichier contient la liste des modules installés.</p>
			<p>Un autre type de cache est à prendre en compte : les classes PHP construites dynamiquement.</p>
			<p>En effet, pour la configuration des pages (métas, robots...), une classe abstraite est montée afin de répondre aux questions de configuration des pages le plus rapidement possible sans à avoir à parser un fichier de configuration. Il en est de même pour les routes.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<div class="widget">
				<img src="/img/contents/doc/cacheDir.png" alt="le cache" class="img-rounded" />
			</div>
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
    <hr />
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h3>AJAX</h3>
			<p>En ajoutant la classe CSS <strong>ajaxLink</strong> à un lien et en précisant la page demandée dans l'attribut "id", vous déclenchez la navigation en <strong>AJAX</strong>.</p>
			<p>Si plusieurs liens doivent cohabiter sur une même page, vous devez ajouter des "_" en préfixe du nom de la page demandée pour éviter d'avoir plusieurs "id" identiques.</p>
			<p>Un meilleur solution est en cours d'étude, à savoir utiliser des attributs data-*.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<div class="widget">
					<p>
						<a href="/img/contents/doc/menu.png" rel="cbox" title="Le template du menu" class="cbox">
							<img src="/img/contents/doc/menu.png" alt="Le template du menu" class="img-rounded" />
						</a>
					</p>
					<p>Exemples de liens de navigation en AJAX.</p>
					<p>L'attribut "href" est renseigné afin d'assurer la navigation si le javascript est désactivé.</p>
				</div>
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
    <hr />
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h3>Données</h3>
			<h4>Stockage</h4>
			<p>L'arborescence de minim a évolué au fil du temps et a été pensée pour être la plus instinctive possible.</p>
			<p>Aussi, il ne faut garder dans le répertoire "data" du noyau que les fichiers de données de minim.</p>
			<p>Pour stocker des fiches produit, des articles ou autre, il convient d'utiliser le répertoire "data" du modules dont dépendent ces données.</p>
			<h4>Types de données</h4>
			<p>On peut distinguer plusieurs types de données :</p>
			<ul class="cog-tools">
				<li>Les fichiers de configuration.</li>
				<li>Certains fichiers de cache très spécifiques.</li>
				<li>Certains templates spécifiques (Google analytics par exemple).</li>
				<li>Les fichiers de version (.version, mais pas encore vraiment utilisés).</li>
				<li>Des pages en HTML (oui, carrément).</li>
			</ul>
			<p>Rapide tour des fichiers présents dans les données de minim :</p>
			<ul class="cog-tools">
				<li><strong>class.PagesConf.php :</strong> classe PHP construite à la volée lors des paramétrages depuis le backoffice pour les configurations des pages.</li>
				<li><strong>class.RoutesConf.php :</strong> classe PHP construite à la volée lors des paramétrages depuis le backoffice pour les routes.</li>
				<li><strong>codeCounter.html :</strong> gadget qui stock en cache le nombre de ligne de code par type d'extension.</li>
				<li><strong>concat_css.xml, css.xml, concat_js.xml, js.xml :</strong> fichiers d'inclusions des css et des js.</li>
				<li><strong>google-analytics.tpl :</strong> templates pour google analytics. Il ne se trouve pas dans les templates, puisque sa valeur change d'une application à une autre.</li>
				<li><strong>minim.conf.xml :</strong> configuration globale de minim  (backoffice).</li>
				<li><strong>minim.routes.back.xml :</strong> routes utilisées sur le front  (backoffice).</li>
				<li><strong>minim.routes.front.xml :</strong> routes utilisées dans le backoffice  (backoffice).</li>
				<li><strong>pagesList.xml :</strong> liste des pages de l'application  (backoffice).</li>
				<li><strong>rights.xml :</strong> droits des utilisateurs (backoffice).</li>
			</ul>
			<p>Il va de soit que si vous ajoutez des fonctionnalités au noyau nécessitant des données variables, ces dernières ont leur place ici.</p>
		<!-- /Article -->
		</article>
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<img src="/img/contents/doc/data.png" alt="les données dans minim" class="img-rounded" />
			</div>
		</aside>
		<!-- /Sidebar -->
	</div>
    <hr />
	<h3>Plugins</h3>
    <div class="row">
		<!-- Article main content -->
		<article class="col-sm-8 maincontent">
			<h4>Intégration des plugins</h4>
			<p>Minim est devenu un framework à force de réutiliser du code qui avait fait ses preuves.</p>
			<p>A mesure, la notion de garder un noyau propre et réutilisable à volonté d'un côté et des modules plus spécifiques de l'autre est devenue évidente.</p>
			<p>Penser une structure propre pour les modules sans s'appuyer sur le fonctionnement du noyau aurait compliqué la compréhension globale du fonctionnement mais surtout pourquoi réinventer la roue...</p>
			<p>Les modules respectent donc à peu près la structure du noyau, avec juste quelques répertoires en moins.</p>
			<p>La gestion des modules est native dans minim, ce dernier étant considéré comme un module lui-même.</p>
			<h4>Choses importantes à savoir pour intégrer un module (et surtout qu'il fonctionne) :</h4>
			<ul class="cog-tools">
				<li>Les templates ainsi que les traductions correspondantes (s'il y en a) des pages statiques, et donc non appelées via une route doivent être placées dans les répertoires "tpl" et "locales" du noyau.</li>
				<li>Les classes PHP doivent être copiées dans le répertoire lib/modules du noyau.</li>
				<li>Les css et les js doivent être déclarés dans les fichiers concat_css.xml et concat_js.xml présents dans le répertoire "data" du noyau.</li>
				<li>Il n'y a plus qu'à coder !</li>
			</ul>
		</article>
		<!-- /Article -->
		<aside class="col-sm-4 sidebar sidebar-right">
			<div class="widget">
				<img src="/img/contents/doc/module.png" alt="exemple d'arborescence d'un module" class="img-rounded" />
			</div>
		</aside>
	</div>
</div>
<!-- /container -->