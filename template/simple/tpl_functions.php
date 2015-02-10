<?php
/**
 * DokuWiki Template Simple Functions
 * 
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Michael Klier <chi@chimeric.de>
 */

/**
 * Renders the topbar
 *
 * @author Michael Klier <chi@chimeric.de>
 */
function tpl_topbar() {
    global $ID;

    $found = false;
    $tbar  = '';
    $path  = explode(':', $ID);

    while(!$found && count($path) >= 0) {
        $tbar = implode(':', $path) . ':' . 'topbar';
        $found = @file_exists(wikiFN($tbar));
        array_pop($path);
        // check if nothing was found
        if(!$found && $tbar == ':topbar') return;
    }

    if($found && auth_quickaclcheck($tbar) >= AUTH_READ) {
        print p_wiki_xhtml($tbar,'',false);
    }
}

// View Counter http://www.dokuwiki.org/tips:viewcounter
function tpl_newpageinfo(){
    // global $lang;
    global $ID;
    $v_adminid = 'jonathan';
 
    $pinfo = tpl_pageinfo(true);
    if ($pinfo === false) return false;
 
    $viewcnt = p_get_metadata($ID, "viewcnt");
    if ($viewcnt == null) $viewcnt = 0;
    if ($_SERVER['REMOTE_USER']!=$v_adminid) {
      $viewcnt++;
    }
    p_set_metadata($ID, array('viewcnt' => $viewcnt));
 
    //$pinfo = str_replace(' &middot; ', ' ('.$viewcnt.' '.$lang['views'].') &middot; ', $pinfo);
    $pinfo = str_replace(' &middot; ', ' ('.$viewcnt.') &middot; ', $pinfo);
    echo $pinfo;
}
