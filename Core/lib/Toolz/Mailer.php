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
final class Toolz_Mailer {
	const CHARSET = 'utf-8';
	const ADDITIONAL_PARAMETERS = '';
	/**
	 * Envoi un mail HTML avec ou sans pièce jointe, cc et bcc.
	 *	$aOptions = array(
				'from' => 'test <test@test.fr>',
				'replyto' => 'test@test.fr',
				'to' => 'jimmy pellitteri <jimmy@pellitteri.fr>',
				'subject' => 'test envoi mail',
				'message' => 'corps du message',
				'attachement' => 'nomdufichier.extension',
				'cc' => 'autre destinataire <autre@destinaire.fr>',
				'bcc' => 'autre destinataire caché <autre@destinaire.fr>',
				'additional_parameters' => 'additional_parameters',
				'charset' => 'utf-8',
				);
	 * @param array $aOptions 
	 * @return boolean
	 */
	public static function send($aOptions) {
		// -- on change la casse des clés pour être plus souple
		$aOptions = array_change_key_case($aOptions);
		// -- options obligatoires 
		if(!isset($aOptions['from'], $aOptions['to'], $aOptions['message'], $aOptions['subject'])) {
			throw new Toolz_Mail_Exception('Il manque des options obligatoires : <strong>from</strong>, <strong>to</strong>, <strong>message</strong> et/ou <strong>subject</strong>');
		}
		// -- options par default
		if(!isset($aOptions['additional_parameters'])){
			$aOptions['additional_parameters'] = self::ADDITIONAL_PARAMETERS;
		}
		if(!isset($aOptions['charset'])){
			$aOptions['charset'] = self::CHARSET;
		}
		$sMailChunk = '-----=' . md5(uniqid(mt_rand()));
		// -- création du header
		$sMailHeaders = 'From: ' . $aOptions['from'] . "\n";
		$aOptions['replyto'] = isset($aOptions['replyto'])?$aOptions['replyto']:$aOptions['from'];
		$sMailHeaders .= 'Reply-To: ' . $aOptions['replyto'] . "\n";
		if(isset($aOptions['cc'])) {
			$sMailHeaders .= 'Cc: ' . $aOptions['cc'] . "\n";
		}
		if(isset($aOptions['bcc'])) {
			$sMailHeaders .= 'Bcc: ' . $aOptions['bcc'] . "\n";
		}
		$sMailHeaders .= 'MIME-Version: 1.0' . "\n";
		$sMailHeaders .= "X-Mailer: PHP/" . phpversion() . "\n";
		$sMailHeaders .= isset($aOptions['attachement'])?'Content-Type: multipart/mixed; boundary="'.$sMailChunk.'"':'Content-Type: text/html; charset="'.$aOptions['charset'].'"' . "\n";
		
	
		// -- s'il y a une pièce jointe
		if(isset($aOptions['attachment'])) {
			$sPDFMailBody = 'This is a multi-part message in MIME format.' . "\n\n";
			$sPDFMailBody .= '--' . $sMailChunk . "\n";
			$sPDFMailBody .= 'Content-Type: text/html; charset="'.$aOptions['charset'].'"' . "\n";
			$sPDFMailBody .= 'Content-Transfer-Encoding: 8bit' . "\n\n";
			$sPDFMailBody .= $aOptions['message'] . "\n";
			$sPDFMailBody .= '--' . $sMailChunk . "\n";
			$sPDFMailBody .= "Content-Type: application/pdf;name='" . basename($aOptions['attachement']) . "'\n" . "Content-Transfer-Encoding: base64\n" . "Content-Disposition:attachment;filename=" . basename($aOptions['attachement']) . "\n\n";
			$sPDFMailBody .= chunk_split(base64_encode(file_get_contents($aOptions['attachement']))) . "\n\n";
			$aOptions['message'] = $sPDFMailBody;
		}
		return mail($aOptions['to'], $aOptions['subject'], $aOptions['message'], $sMailHeaders, $aOptions['additional_parameters']);
	}
}