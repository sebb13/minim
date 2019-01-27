<form action="#" method="post" id="SetLinksDisplay">
	<label for="sectionTitleStyle">{__SECTION_TITLE_STYLE_LABEL__}</label>
	<input type="text" id="sectionTitleStyle" value="{__SECTION_TITLE_STYLE_VALUE__}"/>
	<label for="linkTitleStyle">{__LINK_TITLE_STYLE_LABEL__}</label>
	<input type="text" id="linkTitleStyle" value="{__LINK_TITLE_STYLE_VALUE__}"/>
	<label for="linkStyle">{__LINK_STYLE_LABEL__}</label>
	<input type="text" id="linkStyle" value="{__LINK_STYLE_VALUE__}"/>
	<input type="hidden" name="exw_action" value="LinksMgr::saveStyleConfig" />
	<input type="hidden" name="app_token" value="{##APP_TOKEN##}" />
	<input type="submit" value="{__SAVE__}" />
</form>
<form action="#" method="post" id="SetLinks">

</form>