<?php
/**
 * PhotoWidget Plugin:  Generate feed photowidget format XML output
 * 
 * @author	Jonathan Tsai <tryweb@ichiayi.com>
 * @date	10:57 2012/9/11
 * 2012/4/13 
 *	1. Release first version
 * 2012/9/11
 *  1. Bug Fix for Debian, Thanks for Andre
 * 
 */

	extract($_GET, EXTR_PREFIX_ALL, 'p');
	extract($_POST, EXTR_PREFIX_ALL, 'p');
	$w_feed = str_replace("_andand_", "&", urldecode($p_feed));

	if (substr($w_feed, 0, 28) == 'https://picasaweb.google.com') {
	# Picasa - https://picasaweb.google.com/data/feed/base/user/105702831509661581714/albumid/5730701044821175185?alt=rss&kind=photo&hl=zh_TW
		$arr_image = parsing_picasa_rss($w_feed);
	}
	else if (substr($w_feed, 0, 21) == 'http://api.flickr.com') {
	# Filckr - http://api.flickr.com/services/feeds/photoset.gne?set=72157607663864583&nsid=76823408@N00&lang=zh-hk
		$arr_image = parsing_filckr_rss($w_feed);
	}
	else {
	# Gallery3 - http://photos.ichiayi.com/gallery3/index.php/rss/feed/gallery/album/588
		$arr_image = parsing_gallery3_rss($w_feed);
	}

	$xmlhead = '<?xml version="1.0" encoding="utf-8"?>';
	$xmldata = '';
	foreach ($arr_image as $w_url => $w_img) {
		$xmldata .= '<image href="'.$w_url.'">'.$w_img.'</image>'."\n";
	}
	echo $xmlhead."\n<images>".$xmldata."</images>";
 
	// 14:25 2012/4/13
	function parsing_picasa_rss($p_feed)  {
		$p = xml_parser_create();
		xml_parse_into_struct($p, file_get_contents($p_feed), $vals, $index);
		xml_parser_free($p);

		for($i=0;$i<count($vals);$i++){
			if ($vals[$i]['tag']=='DESCRIPTION' && $vals[$i]['level']==4) {
				$p = xml_parser_create();
				xml_parse_into_struct($p, $vals[$i]['value'], $imgvals);
				xml_parser_free($p);
				for($j=0;$j<count($imgvals);$j++){
					if ($imgvals[$j]['tag']=='A' && $imgvals[$j]['type']=='open') {
						$url = $imgvals[$j]['attributes']['HREF'];
					}
					if ($imgvals[$j]['tag']=='IMG') {
						$img = $imgvals[$j]['attributes']['SRC'];
					}
				}
				#echo "[$url]=>$img\n";
				$arr_result[$url] = $img;
			}
		}
		
		return($arr_result);
	}

	// 20:13 2012/4/13
	function parsing_filckr_rss($p_feed) {
		$p = xml_parser_create();
		xml_parse_into_struct($p, file_get_contents($p_feed), $vals, $index);
		xml_parser_free($p);

		for($i=0;$i<count($vals);$i++){
			if ($vals[$i]['tag']=='CONTENT') {
				$str = $vals[$i]['value'];
				$str = _str_cut($str, '</p>', 'left');
				$str = _str_cut($str, '<p><a href="', 'left');
				$url = _str_cut($str, '"', 'right');
				$str = _str_cut($str, '<img src="', 'left');
				$img = _str_cut($str, '"', 'right');
				#echo "[$url]=>$img\n";
				$arr_result[$url] = $img;
			}
		}

		return($arr_result);
	}

	// 20:39 2012/4/13
	function parsing_gallery3_rss($p_feed) {
		$p = xml_parser_create();
		xml_parse_into_struct($p, file_get_contents($p_feed), $vals, $index);
		xml_parser_free($p);

		for($i=0;$i<count($vals);$i++){
			if ($vals[$i]['tag']=='LINK' && $vals[$i]['level']==4) {
				#echo "[$i]--LINK Level4\n";
				$url = $vals[$i]['value'];
			}
			if ($vals[$i]['tag']=='MEDIA:THUMBNAIL') {
				$img = $vals[$i]['attributes']['URL'];
				#echo "[$url]=>$img\n";
				$arr_result[$url] = $img;
				$url = '';
			}
		}

		return($arr_result);
	}

	/* 00:04 2012/4/9 Jonathan Tsai 
	Add for CardioTrainer
	*/
	function _str_cut($src, $keyword, $mode)
    {
        $keyword_pos = strpos($src, $keyword);
        if ($mode == 'right') {
                $result = substr($src, 0, $keyword_pos);
        }
        else {
                $result = substr($src, $keyword_pos+strlen($keyword));
        }
        return $result;
    }

?>