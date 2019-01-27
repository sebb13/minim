<?php
/*
 *	minim - PHP framework
    Copyright (C) 2019  Sébastien Boulard

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; see the file COPYING. If not, write to the
    Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
final class Websiteproperties extends CoreCommon {
	
	public function __construct() {
		parent::__construct();
	}

	public function getArticle() {
		if(UserRequest::getParams('article_id') === false) {
			UserRequest::setParams('article_id', 'operation');
		}
		UserRequest::setParams('article_id', str_replace('_bottom', '', UserRequest::getParams('article_id')));
		die(Minifier::minifyHtml(
					file_get_contents(
						ModulesMgr::getFilePath(__CLASS__, 'data').'articles/'.UserRequest::getParams('article_id').'.html'
						)
					)
			);
	}
	
	public function getPageArticle() {
		if(UserRequest::getParams('article_id') === false) {
			UserRequest::setParams('article_id', str_replace('documentation_', '', UserRequest::getPage()));
		}
		if(UserRequest::getParams('article_id') === 'documentation') {
			UserRequest::setParams('article_id', 'operation');
		}
		$sContent = str_replace(
						'{__ARTICLE-CONTENTS__}', 
						file_get_contents(
							ModulesMgr::getFilePath(__CLASS__, 'data').'articles/'.UserRequest::getParams('article_id').'.html'
						),
						file_get_contents(
							CONTENT_TPL_PATH.'documentation.tpl'
						)
					);
		return array(
			'content' => $this->oTplMgr->buildSimpleCacheTpl(
												$sContent, 
												ModulesMgr::getFilePath('minim', 'locales', $this->oLang->LOCALE).'documentation.xml'
											),
			'sPage'	=> 'documentation_'.UserRequest::getParams('article_id')
		);
	}
}