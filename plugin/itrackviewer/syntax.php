<?php
/**
 * Plugin ITrackViewer: Allow Display a Google Map, BikeMap-Track, GPSies-Track or a GPX-Track in a wiki page.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Juergen A. Lamers <jaloma.ac@googlemail.com>
 * @updater		Jonathan Tsai <tryweb@ichiayi.com>
 * 2012/4/7 
 *	1. Modify BikeMap Display
 *	2. Add EveryTrail Display : use 'everymap' keyword 
 * 2012/4/8
 *  1. Add CardioTrainer Display : use 'cardiotab' keyword
 *	Exp. (http://www.noom.com/cardiotrainer/tracks.php?trackId=708522736&sig=24c803725f414d40656ddb2ad93a1fe5aaa14d27)
 * 2012/4/10
 *  1. Modify EverTrial : add (everykey) conf['everykey'], 'everymode'='flash|noflash|flash1'
 *  2. Add Picasa Web Albums Display : use 'picasauser'+'albumid' (picasakey) keyword, conf['picasakey']
 *
 * <googlemap kml="http://www.ich-bin-am-wandern-gewesen.de/tracks/20080127_NettersheimTour.kmz" width="600px" height="400px" lat="50.471986" lon="6.643214">
</googlemap>

<googlemap kml="http://www.ich-bin-am-wandern-gewesen.de/tracks/20080224_ErftTour.kmz" lat="50.70939" lon="6.809865" width="600px" height="425px">
</googlemap>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_itrackviewer extends DokuWiki_Syntax_Plugin {

  var $dflt = array(
		    'zoom' => 8,
		    'alt' => 'Ein Track...',
		    'gpx' => 'off',
		    'kml' => 'off',
			// Toursprung-Portale
		    'bikemap' => 'off',
			'wandermap' => 'off',
			'runmap' => 'off',
			'inlinemap' => 'off',
			'mopedmap' => 'off',
			'everymap' => 'off',
			'everymode' => 'off',
			'everykey' => 'off',
			'cardiotab' => 'off',
			'sig' => 'off',
			'qrcode' => 'off',
			'picasauser' => 'off',
			'albumid' => 'off',
			'picasakey' => 'off',
			
		    'quality' => 'high',
		    'allowScriptAccess' => 'sameDomain',
		    'width' => 425,
		    'height' => 400,
		    'kmlsubtitle' => 'off',
		    'mapkey' => 'AARTsJqRkryeAr7c_b0hTdzckxU0KnKhBg', /* Get from GoogleMap... */
		    'type' => 'p', /* p = Gelaende, h = Hybrid, k = Satellit, m = Karte, o = OSM */
			// GSPies
		    'gpsies' => 'off',
			'picasa' => 'off'
		    );
  
    /**
     * return some info
     */
  function getInfo(){
    return array(
		 'author' => 'Jürgen A. Lamers',
		 'email'  => 'jaloma.ac@googlemail.com',
		 'date'   => '2012-04-10',
		 'name'   => 'ITrackViewer Plugin',
		 'desc'   => 'Add maps to your wiki
                              Syntax: <itrackviewer params></itrackviewer>',
		 'url'    => 'http://jaloma.ac.googlepages.com/plugin:itrackviewer'
		 );
  }

    /**
     * What kind of syntax are we?
     */
  function getType() { return 'substition'; }
  function getPType() { return 'block'; }
    /**
     * Where to sort in to parse?
     */
  function getSort() { return 900; } 

    /**
    * Connect pattern to lexer
    */
  function connectTo($mode) { 
    $this->Lexer->addSpecialPattern('<itrackviewer ?[^>\n]*>.*?</itrackviewer>',$mode,'plugin_itrackviewer'); 
  }

 
  function postConnect() {
    $this->Lexer->addExitPattern('</itrackviewer>','plugin_itrackviewer');
  }

    /**
     * Handle the match
     */
  function handle($match, $state, $pos, &$handler){
    // break matched cdata into its components
    list($str_params,$str_points) = explode('>',substr($match,13,-15),2);
    $gmap = $this->_extract_params($str_params);
    return array($gmap);
  }

    /**
     * Create output
     */
  function render($mode, &$renderer, $data) {
    static $initialised = false;//true;//    // set to true after script initialisation
    if ($mode == 'xhtml') {
      list($param) = $data;

      $script = '';
      if (!$initialised) {
		$initialised = true;
		$script = $this->getConf('script').$this->getConf('key');
		$script = '<script type="text/javascript" src="'.$script.'"></script>';
      }
      if (isset($param['kml']) && $param['kml'] != 'off') {
        $renderer->doc .= $this->googlemaps_iframe($script,$param);
      } else if (isset($param['bikemap']) && $param['bikemap'] != 'off') {
		$renderer->doc .= $this->toursprung_iframe("bikemap", "Bikemap",'bikemap', $param);
	  } else if (isset($param['wandermap']) && $param['wandermap'] != 'off') {
		$renderer->doc .= $this->toursprung_iframe("wandermap", "Wandermap",'wandermap', $param);
	  } else if (isset($param['runmap']) && $param['runmap'] != 'off') {
		$renderer->doc .= $this->toursprung_iframe("runmap", "Runmap", 'runmap',$param);
	  } else if (isset($param['mopedmap']) && $param['mopedmap'] != 'off') {
		$renderer->doc .= $this->toursprung_iframe("mopedmap", "Mopedmap", 'mopedmap', $param);
	  } else if (isset($param['inlinemap']) && $param['inlinemap'] != 'off') {
		$renderer->doc .= $this->toursprung_iframe("inlinemap", "Inlinemap", 'inlinemap', $param);
	  } else if (isset($param['everymap']) && $param['everymap'] != 'off') {
		$renderer->doc .= $this->everytrial_iframe($param);
	  } else if (isset($param['cardiotab']) && $param['cardiotab'] != 'off') {
		$renderer->doc .= $this->cardiotrainer_iframe($param);
	  } else if (isset($param['picasauser']) && $param['picasauser'] != 'off') {
		$renderer->doc .= $this->picasa_iframe($param);
      } else if (isset($param['gpsies']) && $param['gpsies'] != 'off') {
		$renderer->doc .= $this->gpsies_iframe($param);
      } else if (isset($param['gpx']) && $param['gpx'] != 'off') {
        $renderer->doc .= $this->gpxviewer_iframe($script, $param);
      }
    }
    return false;
  }

	function gpsies_iframe($param) {
		/*
		 * # Straßenkarte, Wert: "n", Parameter: "&mapType=n"
# Satellitenkarte, Wert: "k", Parameter: "&mapType=k"
# Hybridkarte, Wert: "h", Parameter: "&mapType=h"
# Geländekarte, Wert: "p", Parameter: "&mapType=p"
		 * # Google Earth Plugin, Wert: "ge", Parameter: "&mapType=ge"
# OpenStreetMap Standard, Wert: "mapnik", Parameter: "&mapType=mapnik"
# OpenStreetMap Fahrrad/Cycle, Wert: "cycle", Parameter: "&mapType=cycle"
		 */
		$mapType = '';
		if ($param['type'] != 'off') {
		$mapType = '&mapType='.$param['type'];
		}
		//&picasa=false"
		$picasa = '&picasa=false';
		if ($param['gpsies_picasa'] != 'off') {
			$picasa = '&picasa=true';
		}
		$txt = '<iframe align="left" valign="top" title="'.$param['alt'].
		'" src="http://www.gpsies.de/mapOnly.do?fileId='.$param['gpsies'].
		$mapType.
		$picasa.
		'"'.
		' width="'.$param['width'].'"'.
		' height="'.$param['height'].'"'.
		' style="width:'.$param['width'].'; height: '.$param['height'].';">'.$param['alt'].'</iframe>';
		/* Der Download bei GPSies gestaltet sich etwas komplizierter (Formular) */
		//if ($param['qrcode'] != 'off') {
		//	$txt .= $this->makeQRCode('www.'.$webSpace.'.net/route/'.$param[$tourSprungKey].'/export.gpx',null,null);
		//}		  
		return $txt;
	}
	
	function toursprung_iframe($webSpace, $webSpaceDesc, $tourSprungKey, $param)  {
		// maptype = 0 : Gelaende
		// maptype = 1 : Satellit
		// maptype = 2 : Hybrid
		// maptype = 3 : Karte
		// maptype = 4 : OSM
		/* p = Gelaende, h = Hybrid, k = Satellit, m = Karte, o = OSM */
		$maptype = 0;
		switch ($param['type']) {
			case 'p' : $maptype = 0; break;
			case 'k' : $maptype = 1; break;
			case 'h' : $maptype = 2; break;
			case 'm' : $maptype = 3; break;
			case 'o' : $maptype = 4; break;
			default :
			$maptype = 0;
		}
		$w_width = str_replace("px","",$param['width']);
		$w_height = str_replace("px","",$param['height']);
		$w_height_desc = $w_height+150;
		$txt = '<div style="margin-top:2px;margin-bottom:2px;width:'.$w_width.'px;font-family:Arial,Helvetica,sans-serif;font-size:9px;color:#535353;background-color:#ffffff;border:2px solid #6db466;font-style:normal;text-align:right;padding:0px;padding-bottom:3px !important;">';
		$txt .= '<iframe src="http://www.'.$webSpace.'.net/route/'.$param[$tourSprungKey].'/widget?width='.$w_width.'&amp;height='.$w_height.'&amp;maptype='.$maptype.'&amp;extended=true&amp;unit=km&amp;redirect=no" width="'.$w_width.'" height="'.$w_height_desc.'" border="0" frameborder="0" marginheight="0" marginwidth="0"  scrolling="no">'.$param['alt'].'</iframe>';
		$txt .= '<br />Route <a style="color:#6db466; text-decoration:underline;" href="http://www.'.$webSpace.'.net/route/'.$param[$tourSprungKey].'">'.$param[$tourSprungKey].'</a> - powered by <a style="color:#6db466; text-decoration:underline;" href="http://www.'.$webSpace.'.net/">'.$webSpaceDesc.'</a>&nbsp;';
		$txt .= '</div>';
		if ($param['qrcode'] != 'off') {
			$txt .= $this->makeQRCode('www.'.$webSpace.'.net/route/'.$param[$tourSprungKey].'/export.gpx',null,null);
		}
		return $txt;
	} 

	function makeQRCode($destLink, $caption, $alt) {
		global $conf;
		$txt = '';
		$txt .= '<table>';
		$txt .= '<tr>'; 
		$txt .= '<td>'; 
		$txt .= '<a target="external" href="http://de.wikipedia.org/wiki/QR_Code">';
		$inigma = '<img border="0" style="vertical-align:top;" src="http://encode.i-nigma.com/QRCode/img.php?d=http%3A%2F%2F'.$destLink.'&s=3"';
		$qrcode = '<img border="0" style="vertical-align:top;" src="http://qrcode.kaywa.com/img.php?d=http%3A%2F%2F'.$destLink.'&s=3"';
		if ($this->getConf('qrcode') == 'inigma') {
			$txt .= $inigma;
		} else if ($this->getConf('qrcode') == 'all') {
			$txt .= "<span class='2dcode'>".$qrcode."</span>".
			"<span class='2dcode'>".$inigma."</span>";
		} else {
			$txt .= $qrcode;
		}
		if ($caption != null && $caption != "") {
			$txt .= '&c='.$caption;
		}
		if ($alt == null || $alt == "") {
			$alt = "Track";
		}
		$txt .= ' alt="'.$alt.'" />';
		$txt .= '</a>';
		$txt .= '</td>'; //style="vertical-align:top;"
		$txt .= '<td valign="top">';
		$txt .= 'Get this track directly to your mobile!.<br/>Dont have barcode reader ? <a href="http://www.freewarepocketpc.net/ppc-tag-barcode.html" target="external"> click here</a>';
		$txt .= '<br/>Get some information about <a target="external" href="http://de.wikipedia.org/wiki/QR_Code">QR_Code</a> (WikiPedia.de)';
		$txt .= '</td>';
		$txt .= '</tr>';
		$txt .= '</table>';
		return $txt;
	}

	// 21:49 2012/4/10
	function everytrial_iframe($param)  {
		$maptype = 'Terrain';
		switch ($param['type']) {
		/* p = Gelaende, h = Hybrid, k = Satellit, m = Karte */
			case 'p' : $maptype = 'Terrain'; break;
			case 'k' : $maptype = 'Satellite'; break;
			case 'h' : $maptype = 'Hybrid'; break;
			case 'm' : $maptype = 'Map'; break;
			default :
			$maptype = 'Terrain';
		}
		$everkey = (isset($param['everkey']) && $param['everykey']!='')?$param['everykey']:$conf['plugin']['itrackviewer']['everykey'];
		$param['everymode'] = isset($param['everymode'])?$param['everymode']:'flash';
		$w_width = str_replace("px","",$param['width']);
		$w_height = str_replace("px","",$param['height']);
		if ($param['everymode']=='noflash') {
			$txt = '<iframe src="http://www.everytrail.com/iframe2.php?trip_id='.$param['everymap'].'&width='.$w_width.'&height='.$w_height.'" marginheight="0" marginwidth="0" frameborder="0" scrolling="no" width="'.$w_width.'" height="'.$w_height.'"></iframe>';
		} else if ($param['everymode']=='flash1') {
			$txt = '<div style="width:'.$w_width.'px;height:'.$w_height.'px;border:2px solid #ACD7F5;padding:5px;">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="main" width="100%" height="100%" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab"><param name="movie" value="http://www.everytrail.com/swf/main.swf" /><param name="FlashVars" value="tripId='.$param['everymap'].'&picDim=250&mapType='.$maptype.'&units=metric&isWidget=true&key='.$everykey.'&host=http://www.everytrail.com/get_data.php"><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="always" /><embed src="http://www.everytrail.com/swf/main.swf" quality="high" bgcolor="#ffffff" width="100%" height="100%" name="main" align="middle" FlashVars="tripId='.$param['everymap'].'&picDim=250&includeElevation=&mapType='.$maptype.'&units=metric&isWidget=true&key='.$everykey.'&host=http://www.everytrail.com/get_data.php" play="true" loop="false" quality="high" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed></object>
</div>';
		} else { 
			$txt = '<object width="'.$w_width.'" height="'.$w_height.'" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab"><param name="movie" value="http://www.everytrail.com/swf/widget.swf"/><param name="FlashVars" value="units=metric&mode=0&key='.$everykey.'&tripId='.$param['everymap'].'&mapType='.$maptype.'&"><embed type="application/x-shockwave-flash" src="http://www.everytrail.com/swf/widget.swf" quality="high" width="'.$w_width.'" height="'.$w_height.'" FlashVars="units=metric&mode=0&key='.$everykey.'&tripId='.$param['everymap'].'&mapType='.$maptype.'&" play="true"  quality="high"  pluginspage="http://www.adobe.com/go/getflashplayer"></embed></object>';
		}
		$txt .= '<br />Route <a style="color:#6db466; text-decoration:underline;" href="http://www.everytrail.com/view_trip.php?trip_id='.$param['everymap'].'">'.$param['everymap'].'</a> - powered by <a style="color:#6db466; text-decoration:underline;" href="http://www.everytrial.com/">EveryTrail</a><br />';
		
		if ($param['qrcode'] != 'off') {
			$txt .= $this->makeQRCode('http://www.everytrail.com/downloadGPX.php?trip_id='.$param['everymap'],null,null);
		}
		return $txt;
	} 

	// 21:48 2012/4/10
	//
	function picasa_iframe($param)  {

		$picasakey = (isset($param['picasakey']) && $param['picasakey']!='')?$param['picasakey']:$conf['plugin']['itrackviewer']['picasakey'];
		$w_width = str_replace("px","",$param['width']);
		$w_height = str_replace("px","",$param['height']);
		$w_lang = 'zh_TW';
		$txt = '<div style="width:'.$w_width.'px;font-family:arial,sans-serif;font-size:13px;"><embed type="application/x-shockwave-flash" src="https://picasaweb.google.com/s/c/bin/slideshow.swf" width="'.$w_width.'" height="'.$w_height.'" flashvars="host=picasaweb.google.com&hl='.$w_lang.'&feat=flashalbum&RGB=0x000000&feed=https%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F'.$param['picasauser'].'%2Falbumid%2F'.$param['albumid'].'%3Falt%3Drss%26kind%3Dphoto%26authkey%3D'.$picasakey.'%26hl%3D'.$w_lang.'" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></div>';
		return $txt;
	} 
	
	// 18:32 2012/4/8
	// url - http://www.noom.com/cardiotrainer/tracks.php?trackId=708522736&sig=24c803725f414d40656ddb2ad93a1fe5aaa14d27
	// <itrackviewer cardiotab='708522736' sig='24c803725f414d40656ddb2ad93a1fe5aaa14d27'></itrackviewer>
	// $param['cardiotab'] $param['sig']
	function cardiotrainer_iframe($param)  {
		# check meta data for the CardioTrainer TrackID Info
		$dfile   = metaFN(md5($param['cardiotab'].$param['sig']), '.cardio');
		if (file_exists($dfile)) {
			$cardio_data = unserialize(@file_get_contents($dfile));
		}
		else {
			$cardio_url = 'http://www.noom.com/cardiotrainer/tracks.php?trackId='.$param['cardiotab'].'&sig='.$param['sig'];
			$url_data = $this->_getHttpResponseCode($cardio_url);
			$url_data = $this->_str_cut($url_data, 'var trackData = {', 'left');
			$url_data = $this->_str_cut($url_data, '"trackInterval"', 'right');
			if (strpos($url_data, $param['cardiotab'])!=1) {
				return "Something Wrong";
			}
			$url_data = $this->_str_cut($url_data, $param['sig'].'",', 'left');
			$url_data = str_replace('<br\/>', ' ', $url_data);
			#"duration":"03:12:28","distance":46.34,"date":"Saturday Apr.7, 2012 03:46 pm","minSpeed":0,"maxSpeed":29.68,"avgSpeed":14.45,"climb":786.03,"calories":1475,"exercise_type":"exercise_type_biking","track_name":null,
			$url_data = str_replace(',"', '&', $url_data);
			$url_data = str_replace('":', '=', $url_data);
			$url_data = substr($url_data, 1);
			parse_str($url_data, $cardio_data);
			$cardio_data['duration'] = str_replace('"', '', $cardio_data['duration']);
			$cardio_data['date'] = str_replace('"', '', $cardio_data['date']);
			$cardio_data['exercise_type'] = str_replace('"', '', $cardio_data['exercise_type']);
			$cardio_data['exercise_type'] = str_replace('exercise_type_', '', $cardio_data['exercise_type']);
			$cardio_data['exercise_type'] = strtoupper(substr($cardio_data['exercise_type'],0,1)).substr($cardio_data['exercise_type'],1);
			$avgpace = 60 / $cardio_data['avgSpeed'];
			$pace_min = (int)$avgpace;
			$pace_sec = (int)(($avgpace-$pace_min)*60);
			$cardio_data['pace'] = sprintf("%d:%02d", $pace_min, $pace_sec);
			
			$fh = fopen($dfile, 'w');
			fwrite($fh, serialize($cardio_data));
			fclose($fh);			
		}
		# display Table
		$txt = '<div class="table cardioTab"><table class="inline">	<tr class="row0">
		<th class="col0 leftalign"> Date:      </th><td class="col1 leftalign" colspan="3">'.$cardio_data['date'].'</td><th class="col4"> Distance: </th><td class="col5 leftalign">'.$cardio_data['distance'].' km </td><th class="col6"> Duration: </th><td class="col7">'.$cardio_data['duration'].' </td></tr>';
		$txt .= '<tr class="row1">	<th class="col0 leftalign"> Climb:     </th><td class="col1 leftalign">'.$cardio_data['climb'].' m </td><th class="col2 leftalign"> Calories burnt:  </th><td class="col3 leftalign">'.$cardio_data['calories'].' kcal </td><th class="col4 leftalign"> Avg Pace:  </th><td class="col5">'.$cardio_data['pace'].' min/km </td><th class="col6 leftalign"> Name:  </th><td class="col7">'.$cardio_data['track_name'].' </td></tr>';
		$txt .= '<tr class="row2">	<th class="col0"> Avg Speed: </th><td class="col1"> '.$cardio_data['avgSpeed'].' km/h </td><th class="col2 leftalign"> Max Speed:  </th><td class="col3">'.$cardio_data['maxSpeed'].' km/h </td><th class="col4"> Min Speed: </th><td class="col5 leftalign"> '.$cardio_data['minSpeed'].' </td><th class="col6 leftalign"> Exercise Type:  </th><td class="col7">'.$cardio_data['exercise_type'].' </td></tr></table></div>';

		return $txt;
	} 

	function gpxviewer_iframe($script, $param) {
        return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="400" height="300" id="gpxviewer" align="middle">'.
    	'<param name="swLiveConnect" value="true">'.
		'<param name="allowScriptAccess" value='.$param['allowScriptAccess'].' />'.
		'<param name="movie" value="swf/gpxviewer.swf?gpx='.$param['gpx'].'&amp;zoom='.$param['zoom'].'" />'.
		'<param name="quality" value="'.$param['quality'].'" />'.
		'<param name="scale" value="noscale" />'.
		'<param name="salign" value="lt" />'.
		'<param name="bgcolor" value="#ffffff" />'.
		'<embed src="swf/gpxviewer.swf?gpx='.$param['gpx'].'&amp;zoom='.$param['zoom'].'" '.
		       'quality="high" '.
		       'scale="noscale" '.
		       'salign="lt" '.
		       'bgcolor="#ffffff" '.
		       'width="400" '.
		       'height="300" '.
		       'name="gpxviewer" '.
		       'align="middle" '.
		       'allowScriptAccess='.$param['allowScriptAccess'].''.
		       'type="application/x-shockwave-flash" '.
		       'swliveconnect="true" '.
		       'pluginspage="http://www.macromedia.com/go/getflashplayer" />'.
		'</object>'.
		'	';
	}

	function googlemaps_iframe($script, $param) {
		$maptype = $param['type'];
		if ('o' == $maptype) {
			$maptype = 'p'; // Zurückschalten, da GoogleMaps bestimmt kein OSM anbietet ;-)
		}
		$googleurl = 'http://maps.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;time=&amp;date=&amp;ttype=&amp;q='.$param['kml'].'&amp;ie=UTF8&amp;om=1&amp;s='.$param['mapkey'].'&amp;z='.$param['zoom'].'&amp;t='.$maptype.'&amp;hl=de&amp;';
		$txt ='<iframe width="'.$param['width'].'" height="'.$param['height'].'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$googleurl.'output=embed"></iframe>';//ll=50.5443,6.221945&amp;spn=0.038181,0.072956
		if ($param['kmlsubtitle'] != 'off') {
	  		$txt .= '<br /><small><a href="'.$googleurl.'&amp;source=embed"  style="color:#0000FF;text-align:left">Gr&ouml;&szlig;ere Kartenansicht</a></small>';
	    //	  '<br /><small><a href="'.$url.'&amp;ie=UTF8&amp;om=1&amp;z='.$param['zoom'].'&amp;t='.$param['type'].'&amp;source=embed" style="color:#0000FF;text-align:left">Gr��ere Kartenansicht</a></small>';
	  //&amp;ll=50.5443,6.221945&amp;spn=0.038181,0.072956
		}
		return $txt;
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

	function _getHttpResponseCode($url)
	{
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		#@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		#@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = @curl_exec($ch);
		@curl_close($ch);
		return $response;
	}

	
  /**
   * extract parameters for the googlemap from the parameter string
   *
   * @param   string    $str_params   string of key="value" pairs
   * @return  array                   associative array of parameters key=>value
   */
  function _extract_params($str_params) {
    $param = array();
    preg_match_all('/(\w*)="(.*?)"/us',$str_params,$param,PREG_SET_ORDER);
    if (sizeof($param) == 0) {
      preg_match_all("/(\w*)='(.*?)'/us",$str_params,$param,PREG_SET_ORDER);
    }
    // parse match for instructions, break into key value pairs      
    $gmap = $this->dflt;
    foreach($param as $kvpair) {
      list($match,$key,$val) = $kvpair;
//    $key = strtolower($key);
      if (isset($gmap[$key])) $gmap[$key] = $val;        
    }

    return $gmap;
  }
} /* CLASS */
