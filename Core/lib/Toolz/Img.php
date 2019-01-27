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
final class Toolz_Img {
	
	public static function resize($sImgSrcPath, $iMaxSize, $sOrient='', $sImgDstPath='', $iQuality=100) {
		ini_set('gd.jpeg_ignore_warning', 1);
		if(!is_int($iMaxSize)) {
			throw new Toolz_Img_Exception('ERROR_MAX_SIZE_MUST_BE_A_NUMBER');
		}
		if(empty($sImgDstPath)) {
			$sImgDstPath = $sImgSrcPath;
		}
		$sExt = strtolower(substr($sImgSrcPath, -3));
		switch($sExt) {
		    case 'jpg':
		    case 'peg':
				try {
					$rImgSrcResource = imagecreatefromjpeg($sImgSrcPath);
				} catch(Exception $e) {
					try {
						$rImgSrcResource= imagecreatefromstring(file_get_contents($sImgSrcPath));
					} catch (Exception $e) {
						throw new Toolz_Img_Exception('INVALID_SOS_PARAMETERS_FOR_SEQUENTIAL_JPEG');
					}
				}
		        break;
		    case 'gif':
    		    $rImgSrcResource = imagecreatefromgif($sImgSrcPath);
        		break;
		    case 'png':
    		    $rImgSrcResource = imagecreatefrompng($sImgSrcPath);
    		    break;
    		default :
    		    throw new Toolz_Img_Exception('ERROR_BAD_IMAGE_FORMAT' );
		}
		if(empty($rImgSrcResource)) {
			return false;
		}
		$iImgSrcHeight = imagesy($rImgSrcResource);
	    $iImgSrcWidth = imagesx($rImgSrcResource);
		if(($iImgSrcHeight > $iMaxSize) || ($iImgSrcWidth > $iMaxSize)) {
			switch($sOrient) { //$sOrient défini si la taille max concerne la hauteur ou la largeur
				case '':
			    	if($iImgSrcHeight > $iImgSrcWidth) {
			    	    $fRatio = $iImgSrcHeight / $iImgSrcWidth;
			    	    $iImgDstHeight = $iMaxSize;
			    	    $iImgDstWidth = $iImgDstHeight / $fRatio;
			    	} else {
			    	    $fRatio = $iImgSrcWidth / $iImgSrcHeight;
			    	    $iImgDstWidth = $iMaxSize;
			    	    $iImgDstHeight = $iImgDstWidth / $fRatio;
			    	}
					break;
				case 'width':
					$iImgDstWidth = $iMaxSize; 
					$iImgDstHeight = round(($iMaxSize / $iImgSrcWidth) * $iImgSrcHeight);
					break;
				case 'height':
					$iImgDstHeight = $iMaxSize; 
					$iImgDstWidth = round(($iMaxSize / $iImgSrcHeight) * $iImgSrcWidth);
					break;	
			}
			$imgDstResource = imagecreatetruecolor($iImgDstWidth, $iImgDstHeight);
			imagecopyresampled($imgDstResource, $rImgSrcResource, 0, 0, 0, 0, $iImgDstWidth, $iImgDstHeight, $iImgSrcWidth, $iImgSrcHeight);
			$rHandle = fopen($sImgDstPath, "w");
			if(!$rHandle) {
			    throw new Toolz_Img_Exception('UNABLE_TO_WRITE_IMAGE'.$sImgDstPath);
			}
			fclose($rHandle);
			switch($sExt) {
			    case 'jpg':
			    case 'peg':
					// Le 3ème argument défini le niveau de compression. S'il n'est pas fourni la valeur 75 est utilisée
			        imagejpeg($imgDstResource, $sImgDstPath, $iQuality);
			        break;
			    case 'gif':
			        imagegif($imgDstResource, $sImgDstPath);
			        break;
			    case 'png':
			       imagepng($imgDstResource, $sImgDstPath);
			       break;
			}
		} else {
			unset($iImgSrcHeight, $iImgSrcWidth, $rImgSrcResource);
		}
		return true;
	}
	
	public static function getSize($sImgSrcPath='') {
		if(empty($sImgSrcPath)) {
			throw new Toolz_Img_Exception('EMPTY PATH');
		}
		switch(strtolower(substr($sImgSrcPath, -3))) {
		    case 'jpg':
		    case 'peg':
		        $rImgSrcResource = imagecreatefromjpeg($sImgSrcPath);
		        break;
		    case 'gif':
	   		    $rImgSrcResource = imagecreatefromgif($sImgSrcPath);
	       		break;
		    case 'png':
	   		    $rImgSrcResource = imagecreatefrompng($sImgSrcPath);
	   		    break;
	   		default :
	   		    echo 'ERROR_BAD_IMAGE_FORMAT';
	   		    break;
		}
		if(empty($rImgSrcResource)) {
			return false;
		}
		$aImg = array(
					'height' => imagesy($rImgSrcResource),
					'width' => imagesx($rImgSrcResource)
				);
	    unset($rImgSrcResource);
	    return $aImg;
	}
}