<?php
final class langException extends GenericException {
	const LOCALE_LANG_NOT_FOUND				= 'Language __LOC__ was not found';
	const LOCALE_MOD_NOT_FOUND				= 'Module __MOD__ was not found';
	const LOCALE_TRANS_NOT_FOUND			= 'Translation __TRANS__ was not found';
	const DEFAULT_LOCALE_LANG_NOT_FOUND		= 'Default language __LOC__ was not found';
	const DEFAULT_LOCALE_TRANS_NOT_FOUND	= 'Default translation __TRANS__ was not found';
	const DEFAULT_LOCALE_MOD_NOT_FOUND		= 'Default language module __MOD__ was not found';
	const PROP_NOT_SETTABLE					= 'Property __PROP__ is not a settable property';
	const PROP_NOT_GETTABLE					= 'Property __PROP__ is not a gettable property';
	const INVALID_PATH						= '__PATH__ is not a valid path';
}