<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Common {
	public static function activeIfRoute($name = '') {
		return Route::current()->action['prefix'] == "/$name" ? 'active ' : '';
	}

	public static function selectIfValue($array = array(), $key = '', $value) {
		if ( $value == '' && !isset($array[$key]) ) {
			return 'selected="selected"';
		}
		return isset($array[$key]) && $array[$key]  == $value ? 'selected="selected"' : '';
	}
	public static function selectDefaultStatus($array = array(), $key = '', $value,$MNGFG) {
            if (!isset($array[$key]) && $MNGFG == "0") {
                if ($value === "1" && !isset($array['entry']) && count($array) == 0) {
                    return 'selected="selected"';
                } else if ($value === "" && count($array) > 0) {
                    return 'selected="selected"';
                }
            }
            return isset($array[$key]) && $array[$key]  == $value ? 'selected="selected"' : '';
	}

	public static function checkIfValue($array = array(), $key = '', $value) {
		if ( $value == '' && !isset($array[$key]) ) {
			return 'checked="checked"';
		}
		return isset($array[$key]) && $array[$key]  == $value ? 'checked="checked"' : '';
	}

	public static function _e($arr = array(), $key = '', $default = '') {
		if(is_array($key)){
			$temp = $arr;
			foreach($key as $k){
				if(isset($arr[$k])){
					$temp = $temp[$k];
				}else{
					return $default;
				}
			}
			return $temp;
		}else{
			return isset($arr[$key])?$arr[$key]:$default;
		}
	}

	public static function calculateAge($birthday = 'yyyy-mm-dd') {
        $from = new \DateTime($birthday);
        $to   = new \DateTime();
        return $from->diff($to)->y;
    }

    public static function renderBirthday($birthday = 'yyyy-mm-dd', $format = 'Y/m/d') {
        return ( new \DateTime($birthday) )->format($format);
    }

    public static function previous($routeName = '') {
//		// Revert source
//		get history from session (history get from middleware retrieve url)
//		$history = Session::get('history');
//		$previous = isset($history['previous']) ? $history['previous'] : URL::previous();
//		return $previous;
//		 BC-98 19620

		 return Session::get($routeName) ? Session::get($routeName) : URL::previous();
    }

    public static function getDayFromDate($date = 'yyyy-mm-dd') {
	    $dateOfWeek = ['日', '月', '火', '水', '木', '金', '土'];
	    return $dateOfWeek[(new \DateTime($date))->format('w')];
    }

    public static function downloadNotASIIFileName($filePath,$fileName, $removeSpace = true){
    	urldecode($filePath);
        $mimeType = mime_content_type($filePath);
        $arrfile  = explode("/", $filePath);
        $filename = array_pop($arrfile);
        $ua = "";
        if(!empty($_SERVER["HTTP_USER_AGENT"])){
                $ua = $_SERVER["HTTP_USER_AGENT"];
        }

        if(preg_match("/(?:msie)/i", $ua)){$filename = mb_convert_encoding($filename, "sjis-win", "UTF-8");}
		if(preg_match("/(?:trident)/i", $ua)){
//			$filename = urlencode($filename);
			$arr = explode('+', $filename);
			foreach ($arr as $key => $item) {
				$item = urlencode($item);
				$item = str_replace('+', ' ', $item);
				$arr[$key] = $item;
			}
			$filename = implode('+', $arr);
		}

        if( preg_match("/Trident/", $ua) && preg_match("/rv:11.0/", $ua) ) { $filename = mb_convert_encoding($filename, "sjis-win", "UTF-8"); }

		$removeSpace && $filename = str_replace(" ", "", $filename);

        header( "Content-Description: File Transfer" );
        header( "Content-Type: ".$mimeType);

		if(preg_match("/(?:Edge)/i", $ua))  {
			$arr = explode('+', $filename);
			foreach ($arr as $key => $item) {
				$item = urlencode($item);
				$item = str_replace('+', ' ', $item);
				$arr[$key] = $item;
			}
			$filename = implode('+', $arr);
		}

        header( "Content-Disposition: attachment; filename=" . $filename);
        header( "Content-Transfer-Encoding: binary" );
        header( "Expires: 0" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Pragma: public" );
        header( "Content-Length: " . filesize($filePath) );
        readfile($filePath);
    }

    public static function renderHeaderSort($typeName, $typeCompare, $sort = 'asc') {
    	$html = '';
    	if ($typeName == $typeCompare) {
			if ($sort == 'asc') {
				$sort = 'desc';
				$html = '<i class="fa fa-caret-up" aria-hidden="true"></i>';
			} else {
				$sort = 'asc';
				$html = '<i class="fa fa-caret-down" aria-hidden="true"></i>';
			}
		} else {
			$sort = 'asc';
		}
		$html .= '<input readonly disabled name="'.$typeName.'" value="'.$sort.'" hidden>';
    	return $html;
	}

	public static function renderFieldSort($typeName = 'orderType', $sortByName = 'orderSort', $typeValue = '', $sortValue = '') {
    	return '<div class="sort-component"><input type="text" class="type" hidden name="'.$typeName.'" value="'.$typeValue.'"><input class="value" type="text" hidden name="'.$sortByName.'" value="'.$sortValue.'"></div>';
	}

    public static function convertToKana($string,$type=null)
    {
        // to hiragana 2 byte
        if(is_null($type))
        {
            if($string !="")
            {
                if(preg_match('/[^ァ-ヶー]+/i',$string) || preg_match('/[^ｦ-ﾟ]+/i', $string))
                {
                    $string = mb_convert_kana($string, 'VHc', 'UTF-8');
                }
            }
        }
        else // to katakana 2 byte
        {
            if($string !="")
            {
                if(preg_match('/[^ぁ-ん]+/i',$string) || preg_match('/[^ｦ-ﾟ]+/i', $string))
                {
                    $string = mb_convert_kana($string, 'KVHC', 'UTF-8');
                }
            }
        }

        return $string;
    }
}