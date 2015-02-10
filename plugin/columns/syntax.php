<?php
/**
 * Columns Plugin: Arrange information in mulitple columns
 *                 Based on plugin by Michael Arlt <michael.arlt [at] sk-schwanstetten [dot] de>
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <spambox03@mail.ru>
 * @version    2008-09-14
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_columns extends DokuWiki_Syntax_Plugin {

    var $block;
    var $columns;
    var $align;

    /**
     * function constructor
     */
    function syntax_plugin_columns(){
        $this->block = 0;
        $this->columns = array();
    }

    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'Mykola Ostrovskyy',
            'email'  => 'spambox03@mail.ru',
            'date'   => '2008-09-14',
            'name'   => 'Columns Plugin',
            'desc'   => 'Arrange information in multiple columns',
            'url'    => 'http://wiki.splitbrain.org/plugin:columns',
        );
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'container';
    }

    function getPType(){
        return 'block';
    }

    /**
     * What modes are allowed within our mode?
     */
    function getAllowedTypes() {
        return array (
            'container',
            'substition',
            'protected',
            'disabled',
            'formatting',
            'paragraphs'
        );
    }

    /**
     * Where to sort in?
     */
    function getSort() {
        return 65;
    }

    function connectTo($mode) {
        $kwcolumns = $this->_getKwColumns();
        $this->Lexer->addEntryPattern('<' . $kwcolumns . '.*?>(?=.*?</' . $kwcolumns . '>)', $mode, 'plugin_columns');
        $this->Lexer->addPattern($this->_getKwNewColumn(), 'plugin_columns');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('</' . $this->_getKwColumns() . '>', 'plugin_columns');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler)
    {
        switch ($state) {
            case DOKU_LEXER_ENTER :
                $this->block++;
                $this->columns[$this->block] = 1;
                $width['table'] = '-';
                $width['col'] = array();

                preg_match('/<' . $this->_getKwColumns() . '(.*?)>/', $match, $match);

                if (array_key_exists(1, $match)) {
                    $temp = preg_split('/\s+/', $match[1], -1, PREG_SPLIT_NO_EMPTY);

                    if (count($temp) > 0) {
                        $width['table'] = array_shift($temp);
                        $width['col'] = $temp;
                    }
                }

                return array($state, $this->block, $width);

            case DOKU_LEXER_MATCHED :
                $this->columns[$this->block]++;
                return array($state, $this->block, $this->columns[$this->block]);

            case DOKU_LEXER_UNMATCHED :
                return array($state, $match);

            case DOKU_LEXER_EXIT :
                return array($state, $this->block, $this->columns[$this->block]);
        }
        return false;
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml') {
            switch ($data[0]) {
                case DOKU_LEXER_ENTER:
                    $renderer->doc .= $this->_renderTable($data[2]['table']);

                    $columns = $this->_getColumns($data[1]);
                    $colWidth = $data[2]['col'];

                    if (count($colWidth) < $columns) {
                        $colWidth = array_pad($colWidth, $columns, '-');
                    }

                    $column = 0;
                    $this->align = array();

                    foreach($colWidth as $width) {
                        $this->align[++$column] = $this->_getAlignment($width);
                        $renderer->doc .= $this->_renderCol(trim($width, '*'));
                    }

                    $renderer->doc .= '<tr>' . $this->_renderTd($this->align[1], 'first');
                    break;

                case DOKU_LEXER_MATCHED:
                    $html = '</td>';

                    if ($data[2] < $this->_getColumns($data[1])) {
                        $html .= $this->_renderTd($this->align[$data[2]]);
                    }
                    else {
                        $html .= $this->_renderTd($this->align[$data[2]], 'last');
                    }

                    if (strstr(substr($renderer->doc, -5), '<p>') !== false) {
                        $renderer->doc .= '</p>' . $html . '<p>';
                    }
                    else {
                        $renderer->doc .= $html;
                    }
                    break;

                case DOKU_LEXER_UNMATCHED:
                    $renderer->doc .= $renderer->_xmlEntities($data[1]);
                    break;

                case DOKU_LEXER_EXIT:
                    $renderer->doc .= '</td></tr></table>';
                    break;
            }
            return true;
        } elseif ($mode == 'metadata') {
            switch ($data[0]) {
                case DOKU_LEXER_EXIT:
                    $renderer->meta['columns'][$data[1]] = $data[2];
                    break;
            }
            return true;
        }
        return false;
    }

    function _getKwColumns() {
        $keyword = $this->getConf('kwcolumns');
        if ($keyword == '') {
            $keyword = $this->getLang('kwcolumns');
        }
        return $keyword;
    }

    function _getKwNewColumn() {
        $keyword = $this->getConf('kwnewcol');
        if ($keyword == '') {
            $keyword = $this->getLang('kwnewcol');
        }
        if ($this->getConf('wrapnewcol') == 1) {
            $keyword = '<' . $keyword . '>';
        }
        return $keyword;
    }

    function _getColumns($block) {
        if (empty($this->columns)) {
            global $ID;

            $this->columns = p_get_metadata($ID, 'columns');
        }
        return $this->columns[$block];
    }

    function _getAlignment($width) {
        preg_match('/^(\*?).*?(\*?)$/', $width, $match);
        $align = $match[1] . '-' . $match[2];
        switch ($align) {
            case '-':
                return '';

            case '-*':
                return 'left';

            case '*-':
                return 'right';

            case '*-*':
                return 'center';
        }
    }

    function _renderTable($width) {
        if ($width == '-') {
            return '<table class="columns-plugin">';
        }
        else {
            return '<table class="columns-plugin" style="width:' . $width . '">';
        }
    }

    function _renderCol($width) {
        if ($width == '-') {
            return '<col>';
        }
        else {
            return '<col style="width:' . $width . '">';
        }
    }

    function _renderTd($align, $class = '') {
        if ($class == '') {
            $html = '<td';
        }
        else {
            $html = '<td valign=\'top\' class="' . $class . '"';
        }
        if ($align != '') {
            $html .= ' style="text-align:' . $align . ';"';
        }
        return $html . '>';
    }
}
