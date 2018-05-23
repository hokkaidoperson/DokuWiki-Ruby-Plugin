<?php
/**
 * DokuWiki Ruby Plugin
 *
 * Provide a ruby annotation, which is used to indicate the pronunciation
 * or meaning of the corresponding characters.
 * This kind of annotation is often used in Japanese publications.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Hokkaidoperson <dosankomali@yahoo.co.jp>
 *
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_ruby extends DokuWiki_Syntax_Plugin {

    function getType(){
        return 'substition';
    }

    function getSort(){
        return 150;
    }

    function connectTo($mode) {
      $this->Lexer->addSpecialPattern('\{\{ruby\|[^}]*\}\}',$mode,'plugin_ruby');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        return explode('|', substr($match, strlen('{{ruby|'), -2));
    }

    function render($format, Doku_Renderer $renderer, $data) {
        $renderer->doc .= '<ruby><rb>' .htmlspecialchars($data[0]) . '</rb><rp>' . $this->getConf('parenthesis') . '</rp><rt>' .htmlspecialchars($data[1]) . '</rt><rp>' . $this->getConf('parenthesisClosing') . '</rp></ruby>';
    }
}
