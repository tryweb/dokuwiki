<?php
/**
 * JAlbum Action Plugin:   Register JAlbum to the toolbar
 * 
 * @author     Jürgen A.Lamers <jaloma.ac@googlemail.com>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_itrackviewer extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Jürgen A.Lamers',
      'email'  => 'jaloma.ac@googlemail.com',
      'date'   => '2009-01-14',
      'name'   => 'ITrackViewer (toolbar action plugin component)',
      'desc'   => 'ITrackViewer toolbar action functions.',
      'url'    => 'http://www.dokuwiki.org/plugin:itrackviewer',
    );
  }

  function register(&$controller) {
    $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array ());
  }
 

/**
 * Inserts a toolbar button
 */
function insert_button(& $event, $param) {
    $event->data[] = array (
        'type' => 'format',
        'title' => $this->getLang('itrackviewer'),
        'icon' => '../../plugins/itrackviewer/toolbar/kml_file.png',
		'open' =>'<itrackviewer kml="off" mapkey="" kmlsubtitle="off" type="p" gpx="off" '.
			'bikemap="off" '.
			'wandermap="off" '.
			'runmap="off" '.
			'inlinemap="off" '.
			'mopedmap="off" '.
			'qrcode="off" '.
			'gpsies="off" '.
			'picasa="off" '.
			'width="400" height="400" zoom="13" alt="off">',
        'close' => '</itrackviewer>',
    );
}
} // class