<?php
/**
 * PhotoWidget Action Plugin:   Register PhotoWidget to the toolbar
 * 
 * @author	Jonathan Tsai <tryweb@ichiayi.com>
 * @date	11:41 2012/4/13
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_photowidget extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Jonathan Tsai',
      'email'  => 'tryweb@ichiayi.com',
      'date'   => '2012-04-13',
      'name'   => 'photowidget (toolbar action plugin component)',
      'desc'   => 'photowidget toolbar action functions.',
      'url'    => 'http://www.dokuwiki.org/plugin:photowidget',
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
        'title' => $this->getLang('photowidget'),
        'icon' => '../../plugins/photowidget/toolbar/PhotoWidget.png',
		'open' =>'<photowidget feed="" width="300" height="300">',
        'close' => '</photowidget>',
    );
}
} // class