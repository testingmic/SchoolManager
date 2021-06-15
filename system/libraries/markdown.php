<?php

#
#
# (c) BlakeTurner
# https://gist.github.com/BlakeTurner/5826280
#
# (c) Gafflin
#  https://gist.github.com/gaffling/94eca5d545b1781a2ea34324b1cf7a6c
#

class Markdown {

    public function convert($string, $clean_up=true, $tidy_up=true) {

        // INI
        $markdown = $string;
        // USE ONLY THE BODY OF A WEBPAGE
        if ($clean_up == true) {
            // CORRECT THE HTML - or use https://www.barattalo.it/html-fixer/
            $dom = new DOMDocument(); // FIX ENCODING https://stackoverflow.com/a/8218649
            @$dom->loadHTML(mb_convert_encoding($markdown, 'HTML-ENTITIES', 'UTF-8'));
            $markdown = $dom->saveHTML();
            // preg_match() IS NOT SO NICE, BUT WORKS FOR ME
            preg_match("/<body[^>]*>(.*?)<\/body>/is", $markdown, $matches);
            $markdown = $matches[1];
            $markdown = str_replace(array("\r","\n","\t",'  '), array('','','',' '), $markdown);
        }
        // GET RID OF ATTRIBUTES! PARTICULARLY USEFUL WHEN CONVERTING GARBAGE WYSIWYG CODE TO MARKDOWN
        if ($tidy_up == true) {
            $elements = array(
                'table', 'tr', 'th', 'td', 
                'ul', 'ol', 'li',
                'p', 'blockquote',
                'strong', 'b', 'em', 'i', 'hr',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
            );
            foreach ($elements as $element) {
                $markdown = preg_replace("/<$element .*?>/", "<$element>", $markdown);
            }
        }
        // REPLACE SIMPLE TAGS WITH MARKDOWN EQUIVALENT
        $regexMap = array(
            'p'         => '',
            '\/p'       => PHP_EOL,
            'span'      => '',
            'span.*?'   => '',
            '\/span'    => '',
            'div'       => '',
            'div.*?'    => '',
            '\/div'     => '',
            'h1'        => '# ',
            'h2'        => '## ',
            'h3'        => '### ',
            'h4'        => '#### ',
            'h5'        => '##### ',
            'h6'        => '###### ',
            '\/h\d'     => PHP_EOL,
            'br'        => PHP_EOL,
            'br\s\/'    => PHP_EOL,
            'hr'        => PHP_EOL.'---'.PHP_EOL.PHP_EOL,
            'hr.*?'     => PHP_EOL.'---'.PHP_EOL.PHP_EOL,
            'strong'    => '__',
            '\/strong'  => '__',
            'b'         => '__',
            '\/b'       => '__',
            'em'        => '_',
            '\/em'      => '_',
            'i'         => '_',
            '\/i'       => '_',
        );
        foreach ($regexMap as $el => $replacement) {
            $markdown = preg_replace("/\<$el\>/i", $replacement, $markdown);
        }
        // IMAGES
        if (preg_match_all('/<img.*src="([^\s"]*?)".*>?/i', $markdown, $matches)) {
            foreach ($matches[0] as $i => $img_markup) {
            $url = $matches[1][$i];
            $alt = '';
            if (preg_match('/alt="([^"]*?)"/i', $img_markup, $alt_match)) {
                $alt = $alt_match[1];
            }
            if ($alt == '' and preg_match('/title="([^"]*?)"/i', $img_markup, $alt_match)) {
                $alt = $alt_match[1];
            }
            $img_markdown = '!['.$alt.']('.$url.')';
            $markdown = str_replace($img_markup, $img_markdown, $markdown);
            }
        }
        // LINKS
        if (preg_match_all('/<a.*?href="([^\s"]*?)".*?>(.*?)?<\/a>/is', $markdown, $matches)) {
            foreach ($matches[0] as $i => $a_markup) {
            $href = $matches[1][$i];
            $text = $matches[2][$i];
            $a_markdown = '['.$text.']('.$href.')';
            $markdown = str_replace($a_markup, $a_markdown, $markdown);
            }
        }
        // UNORDERED LISTS
        if (preg_match_all('/<ul>(.*?)<\/ul>/is', $markdown, $ul_matches)) {
            $markdown = preg_replace('/<ul>|<\/ul>/i', '', $markdown);
            foreach ($ul_matches[0] as $ul) {
            if (preg_match_all('/[\t ]?<li>(.*?)<\/li>/is', $ul, $list_items_ul)) {
                foreach ($list_items_ul[0] as $i => $ulli_markup) {
                $ulli_inner = trim($list_items_ul[1][$i]);
                $ulli_markdown = ' * ' . $ulli_inner.PHP_EOL;
                $markdown = str_replace($ulli_markup, $ulli_markdown, $markdown);
                }
            }
            }
        }
        // ORDERED LISTS
        if (preg_match_all('/<ol>(.*?)<\/ol>/is', $markdown, $ol_matches)) {
            $markdown = preg_replace('/<ol>|<\/ol>/i', '', $markdown);
            foreach ($ol_matches[0] as $ol) {
            if (preg_match_all('/[\t ]?<li>(.*?)<\/li>/is', $ol, $list_items_ol)) {
                foreach ($list_items_ol[0] as $i => $olli_markup) {
                $olli_inner = trim($list_items_ol[1][$i]);
                $olli_markdown = ' ' . ($i+1) . '. ' . $olli_inner.PHP_EOL;
                $markdown = str_replace($olli_markup, $olli_markdown, $markdown);
                }
            }
            }
        }
        // TABLE
        if (preg_match_all('/<table>(.*?)<\/table>/s', $markdown, $matches)) {
            $markdown = preg_replace('/<table>|<\/table>/', '', $markdown);
            foreach ($matches[0] as $table) {
            if (preg_match_all('/<tr>(.*?)<\/tr>/s', $table, $table_items)) {
                foreach ($table_items[0] as $i => $tr_markup) {
                if (preg_match_all('/.*?<(th|td)>(.*?)<\/(th|td)>.*?/s', $tr_markup, $tr_items)) {
                    $colums = count($tr_items[1]);
                    foreach ($tr_items[0] as $i => $tr_markup) {
                    $td_inner = $tr_items[2][$i];
                    $td_markdown = $td_inner;
                    if ($colums!=($i+1)) $td_markdown = '|'.$td_inner.'|';
                    $markdown = str_replace($tr_markup, $td_markdown, $markdown);
                    }
                }
                }
            }
            }
            $head = PHP_EOL.'|'.str_repeat('---|', $colums).PHP_EOL;
            $markdown = preg_replace('/<\/tr>/', '|'.$head, $markdown, 1);
            $markdown = preg_replace('/<\/tr>/', '|'.PHP_EOL, $markdown);
        }
        // BLOCKQUOTES
        if (preg_match_all('/<blockquote>(.*?)<\/blockquote>/is', $markdown, $matches)) {  
            $blockquote_markdown = '';
            foreach ($matches[1] as $i => $inner_html) {
            $blockquote_markup = $matches[$i];
            $blockquote_markdown = '';
            $lines = explode(PHP_EOL, $inner_html);
            foreach ($lines as $line) {
                $blockquote_markdown .= '> ' . $line . PHP_EOL;
            }
            $markdown = str_replace($blockquote_markup, $blockquote_markdown, $markdown);
            }
        }
        return trim($markdown);
        }
}