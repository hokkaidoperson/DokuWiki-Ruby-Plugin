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
        //Get the texts
        //$texts[0] ... normal text / $texts[1] ... ruby
        $texts = explode('|', substr($match, strlen('{{ruby|'), -2));

        //Get the data of parentheses
        $prts = $this->getConf('parentheses');

        //If there is only one character in $prts, rp tags won't be outputted
        if (mb_strlen($prts) <= 1) {
            $lprt = null;
            $rprt = null;
        } else {
            $lprt = mb_substr($prts,0,1);
            $rprt = mb_substr($prts,1,1);
        }
        
        return array($texts[0],$texts[1],$lprt,$rprt);
    }

    function render($format, Doku_Renderer $renderer, $data) {
        if ($data[2] == null) {
            $renderer->doc .= '<ruby><rb>' . htmlspecialchars($data[0]) . '</rb><rt>' . htmlspecialchars($data[1]) . '</rt></ruby>';
        } else {
            $renderer->doc .= '<ruby><rb>' . htmlspecialchars($data[0]) . '</rb><rp>' . htmlspecialchars($data[2]) . '</rp><rt>' . htmlspecialchars($data[1]) . '</rt><rp>' . htmlspecialchars($data[3]) . '</rp></ruby>';
        }
    }
}
