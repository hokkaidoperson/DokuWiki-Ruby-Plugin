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
      $this->Lexer->addSpecialPattern('\{\{ruby\|[^}]*\}\}',$mode,'plugin_ruby');
    }

    /**
     * Handle the match
     */
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

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        static $rp;
        if (!isset($rp)) {
            if (utf8_strlen($this->getConf('parentheses')) > 1) {
                // get a pair of ruby parentheses
                $rp[0] = utf8_substr($this->getConf('parentheses'), 0, 1);
                $rp[1] = utf8_substr($this->getConf('parentheses'), 1, 1);
            } else {
                // set an empty array
                $rp = array();
            }
        }

        if ($format == 'xhtml') {
            // create a ruby annotation for words and phrases
            $renderer->doc .= '<ruby>';
            $renderer->doc .= '<rb>'.hsc($data[0]).'</rb>';
            $renderer->doc .= isset($rp[0]) ? '<rp>'.hsc($rp[0]).'</rp>' : '';
            $renderer->doc .= '<rt>'.hsc($data[1]).'</rt>';
            $renderer->doc .= isset($rp[1]) ? '<rp>'.hsc($rp[1]).'</rp>' : '';
            $renderer->doc .= '</ruby>';
        } else {
            // For non-XHTML format mode, render base text
            // omit ruby text if a pair of parentheses is not set
            $renderer->doc .= hsc($data[0]);
            $renderer->doc .= !empty($rp) ? hsc($rp[0].$data[1].$rp[1]) : '';
        }
        return;


        if ($data[2] == null) {
            $renderer->doc .= '<ruby>';
            $renderer->doc .= '<rb>' . htmlspecialchars($data[0]) . '</rb>';
            $renderer->doc .= '<rt>' . htmlspecialchars($data[1]) . '</rt>';
            $renderer->doc .= '</ruby>';
        } else {
            $renderer->doc .= '<ruby>';
            $renderer->doc .= '<rb>' . htmlspecialchars($data[0]) . '</rb>';
            $renderer->doc .= '<rp>' . htmlspecialchars($data[2]) . '</rp>';
            $renderer->doc .= '<rt>' . htmlspecialchars($data[1]) . '</rt>';
            $renderer->doc .= '<rp>' . htmlspecialchars($data[3]) . '</rp>';
            $renderer->doc .= '</ruby>';
        }
    }
}
