<?php
/**
 * Plugin PhotoWidget: Allow Display a Gallery3, Flicker, Picassa Photo Widget in a wiki page.
 * photowidget.swf is from Roytanck.com ( http://www.roytanck.com/get-my-flickr-widget/ )
 * 
 * @license		GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author		Jonathan Tsai <tryweb@ichiayi.com>
 * 2012/4/13 
 *	1. Release first version
 * 2012/4/14
 *  1. Add Album Name Function : Get <photowidget feed='xx'>Album Name</photowidget>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_photowidget extends DokuWiki_Syntax_Plugin {

  var $dflt = array(
		    'feed' => 'off',
		    'width' => 300,
		    'height' => 300
		    );
  
    /**
     * return some info
     */
  function getInfo(){
    return array(
		'author' => 'Jonathan Tsai',
		'email'  => 'tryweb@ichiayi.com',
		'date'   => '2012-04-14',
		'name'   => 'photowidget Plugin',
		'desc'   => 'Add Photo Widget to your wiki
                              Syntax: <photowidget params>Album Name</photowidget>',
		'url'    => 'http://www.dokuwiki.org/plugin:photowidget'
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
    $this->Lexer->addSpecialPattern('<photowidget ?[^>\n]*>.*?</photowidget>',$mode,'plugin_photowidget'); 
  }

 
  function postConnect() {
    $this->Lexer->addExitPattern('</photowidget>','plugin_photowidget');
  }

    /**
     * Handle the match
     */
  function handle($match, $state, $pos, &$handler){
    // break matched cdata into its components
    list($str_params,$str_albumname) = explode('>',substr($match,13,-14),2);
    $gmap = $this->_extract_params($str_params);
	$gmap['name']=$str_albumname;

    return array($gmap);
  }

    /**
     * Create output
     */
  function render($mode, &$renderer, $data) {
    if ($mode == 'xhtml') {
		list($param) = $data;

		$w_width = str_replace("px","",$param['width']);
		$w_height = str_replace("px","",$param['height']);
		$str_name =(isset($param['name']) && trim($param['name'])!='')?$param['name']:'';
		$w_height0=($str_name!='')?$w_height+25:$w_height;
		$renderer->doc .= '<div class="g-block-content" style="width:'.$w_width.'px;height:'.$w_height0.'px;border:1px solid #ACD7F5;padding:5px;">';
		$renderer->doc .= '<ccenter>'.$str_name.'</center>';
		if (isset($param['feed']) && $param['feed'] != 'off') {
			$renderer->doc .= $this->photowidget_iframe($param, $w_width, $w_height);
		}
		$renderer->doc .= '</div>';
	}
	return false;
  }


	// 17:58 2012/4/13
	function photowidget_iframe($param, $p_width, $p_height)  {

		$w_swfLoc = DOKU_BASE.'lib/plugins/photowidget/photowidget.swf';
		$w_feedurl = 'http://'.$_SERVER['HTTP_HOST'].DOKU_BASE.'lib/plugins/photowidget/photowidget.php?feed='.urlencode(str_replace("&", "_andand_",$param['feed']));

		$txt = '<div><object type="application/x-shockwave-flash" data="'.$w_swfLoc.'" width="'.$p_width.'" height="'.$p_height.'">';
		$txt .= '<param name="movie" value="'.$w_swfLoc.'" />';
		$txt .= '<param name="bgcolor" value="#ffffff" />';
		$txt .= '<param name="AllowScriptAccess" value="always" />';
		$txt .= '<param name="flashvars" value="feed='.$w_feedurl.'" />';
		$txt .= '<p>This widget requires Flash Player 9 or better</p>';
		$txt .= '</object></div>';
		return $txt;
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
