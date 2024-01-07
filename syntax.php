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

    /**
     * Connect lookup pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{ruby\|[^}]*\}\}', $mode, 'plugin_ruby');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {
        // get ruby base and text component of a ruby annotation
        $data = explode('|', substr($match, strlen('{{ruby|'), -2));
        return $data;
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        static $rp;
        if (!isset($rp)) {
            if (strlen($this->getConf('parentheses')) > 1) {
                // get a pair of ruby parentheses
                $rp[0] = substr($this->getConf('parentheses'), 0, 1);
                $rp[1] = substr($this->getConf('parentheses'), 1, 1);
            } else {
                // set an empty array
                $rp = array();
            }
        }

        if ($format == 'xhtml') {
            // create a Group-ruby annotation
            $renderer->doc .= '<ruby>';
            $renderer->doc .= '<rb>'.hsc($data[0]).'</rb>';
            $renderer->doc .= isset($rp[0]) ? '<rp>'.hsc($rp[0]).'</rp>' : '';
            $renderer->doc .= '<rt>'.hsc($data[1]).'</rt>';
            $renderer->doc .= isset($rp[1]) ? '<rp>'.hsc($rp[1]).'</rp>' : '';
            $renderer->doc .= '</ruby>';
        }
        if ($format == 'metadata') {
            // when the format is "metadata" (abstract)
            if ($renderer->capture) $renderer->doc .= hsc($data[0]) . hsc($rp[0]) . hsc($data[1]) . hsc($rp[1]);
        }

    }
}
