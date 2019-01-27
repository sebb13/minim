<style type="text/css">		
	#devBanner { 
		position:relative; 
		top:0px; 
		right:0px; 
		width:100%; 
		padding:10px; 
		background: #ff0000 url("{##STATIC_SERVER_URL##}img/inDev.gif") left top no-repeat;
	}		
</style>	
<div id="devBanner" class="text-right">
	<img src="{##STATIC_SERVER_URL##}img/minim_transparent_back.png" title="minim" alt="minim" />
	<a href="{##WEB_PATH##}{##LANG##}/{##PAGE##}.html?noDevBanner=1">
		<button type="button" class="btn btn-danger btn-xs">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
	</a>
</div>