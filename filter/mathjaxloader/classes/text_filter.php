<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace filter_mathjaxloader;

use core\url;

/**
 * This filter provides automatic support for MathJax
 *
 * @package    filter_mathjaxloader
 * @copyright  2013 Damyon Wiese (damyon@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    /**
     * Perform a mapping of the moodle language code to the equivalent for MathJax.
     *
     * @param string $moodlelangcode - The moodle language code - e.g. en_pirate
     * @return string The MathJax language code.
     */
    public function map_language_code($moodlelangcode) {

        // List of language codes found in the MathJax/localization/ directory.
        $mathjaxlangcodes = [
            'ar', 'ast', 'bcc', 'bg', 'br', 'ca', 'cdo', 'ce', 'cs', 'cy', 'da', 'de', 'diq', 'en', 'eo', 'es', 'fa',
            'fi', 'fr', 'gl', 'he', 'ia', 'it', 'ja', 'kn', 'ko', 'lb', 'lki', 'lt', 'mk', 'nl', 'oc', 'pl', 'pt',
            'pt-br', 'qqq', 'ru', 'scn', 'sco', 'sk', 'sl', 'sv', 'th', 'tr', 'uk', 'vi', 'zh-hans', 'zh-hant',
        ];

        // List of explicit mappings and known exceptions (moodle => mathjax).
        $explicit = [
            'cz' => 'cs',
            'pt_br' => 'pt-br',
            'zh_tw' => 'zh-hant',
            'zh_cn' => 'zh-hans',
        ];

        // If defined, explicit mapping takes the highest precedence.
        if (isset($explicit[$moodlelangcode])) {
            return $explicit[$moodlelangcode];
        }

        // If there is exact match, it will be probably right.
        if (in_array($moodlelangcode, $mathjaxlangcodes)) {
            return $moodlelangcode;
        }

        // Finally try to find the best matching mathjax pack.
        $parts = explode('_', $moodlelangcode, 2);
        if (in_array($parts[0], $mathjaxlangcodes)) {
            return $parts[0];
        }

        // No more guessing, use English.
        return 'en';
    }

    #[\Override]
    public function setup($page, $context) {
        if (!$page->requires->should_create_one_time_item_now('filter_mathjaxloader-scripts')) {
            return;
        }

        $url = get_config('filter_mathjaxloader', 'httpsurl');
        $lang = $this->map_language_code(current_language());
        $url = new url($url);

        // Let's still get this config even if the value is null due to the setting being set as default.
        // For the config we can set based on the needs when we need from:
        // https://docs.mathjax.org/en/v3.2-latest/web/configuration.html#web-configuration.
        $config = get_config('filter_mathjaxloader', 'mathjaxconfig');
        $wwwroot = new url('/');
        $config = str_replace('{wwwroot}', $wwwroot->out(true), $config);
        $params = [
            'mathjaxurl' => $url->out(false),
            'mathjaxconfig' => $config,
            'lang' => $lang,
        ];

        // Let's still send the config and lang to the loader.
        $page->requires->js_call_amd('filter_mathjaxloader/loader', 'configure', [$params]);
    }

    #[\Override]
    public function filter($text, array $options = []) {
        global $PAGE;

        $legacy = get_config('filter_mathjaxloader', 'texfiltercompatibility');
        $extradelimiters = explode(',', get_config('filter_mathjaxloader', 'additionaldelimiters'));
        if ($legacy) {
            // This replaces any of the tex filter maths delimiters with the default for inline maths in MathJAX "\( blah \)".
            // E.g. "<tex.*> blah </tex>".
            $text = preg_replace('|<(/?) *tex( [^>]*)?>|u', '[\1tex]', $text);
            // E.g. "[tex.*] blah [/tex]".
            $text = str_replace('[tex]', '\\(', $text);
            $text = str_replace('[/tex]', '\\)', $text);
            // E.g. "$$ blah $$".
            $text = preg_replace('|\$\$([\S\s]*?)\$\$|u', '\\(\1\\)', $text);
            // E.g. "\[ blah \]".
            $text = str_replace('\\[', '\\(', $text);
            $text = str_replace('\\]', '\\)', $text);
        }

        $hasextra = false;
        foreach ($extradelimiters as $extra) {
            if ($extra && strpos($text, $extra) !== false) {
                $hasextra = true;
                break;
            }
        }

        $hasdisplayorinline = false;
        if ($hasextra) {
            // Convert the HTML tag wrapper inside the equation to entities.
            $text = $this->escape_html_tag_wrapper($text);
            // If custom dilimeters are used, wrap whole text to prevent autolinking.
            $text = '<span class="nolink">' . $text . '</span>';
        } else if (preg_match('/\\\\[[(]/', $text) || preg_match('/\$\$/', $text)) {
            // Convert the HTML tag wrapper inside the equation to entities.
            $text = $this->escape_html_tag_wrapper($text);
            // Only parse the text if there are mathjax symbols in it. The recognized
            // math environments are \[ \] and $$ $$ for display mathematics and \( \)
            // for inline mathematics.
            // Note: 2 separate regexes seems to perform better here than using a single
            // regex with groupings.

            // Wrap display and inline math environments in nolink spans.
            // Do not wrap nested environments, i.e., if inline math is nested
            // inside display math, only the outer display math is wrapped in
            // a span. The span HTML inside a LaTex math environment would break
            // MathJax. See MDL-61981.
            [$text, $hasdisplayorinline] = $this->wrap_math_in_nolink($text);
        }

        if ($hasdisplayorinline || $hasextra) {
            if ($PAGE->requires->should_create_one_time_item_now('filter_mathjaxloader-typeset')) {
                $PAGE->requires->js_call_amd('filter_mathjaxloader/loader', 'typeset');
            }
            return '<span class="filter_mathjaxloader_equation">' . $text . '</span>';
        }
        return $text;
    }

    /**
     * Find math environments in the $text and wrap them in no link spans
     * (<span class="nolink"></span>). If math environments are nested, only
     * the outer environment is wrapped in the span.
     *
     * The recognized math environments are \[ \] and $$ $$ for display
     * mathematics and \( \) for inline mathematics.
     *
     * @param string $text The text to filter.
     * @return array An array containing the potentially modified text and
     * a boolean that is true if any changes were made to the text.
     */
    protected function wrap_math_in_nolink($text) {
        $i = 1;
        $len = strlen($text);
        $displaystart = -1;
        $displaybracket = false;
        $displaydollar = false;
        $inlinestart = -1;
        $changesdone = false;
        // Loop over the $text once.
        while ($i < $len) {
            if ($displaystart === -1) {
                // No display math has started yet.
                if ($text[$i - 1] === '\\' && $text[$i] === '[') {
                    // Display mode \[ begins.
                    $displaystart = $i - 1;
                    $displaybracket = true;
                } else if ($text[$i - 1] === '$' && $text[$i] === '$') {
                    // Display mode $$ begins.
                    $displaystart = $i - 1;
                    $displaydollar = true;
                } else if ($text[$i - 1] === '\\' && $text[$i] === '(') {
                    // Inline math \( begins, not nested inside display math.
                    $inlinestart = $i - 1;
                } else if ($text[$i - 1] === '\\' && $text[$i] === ')' && $inlinestart > -1) {
                    // Inline math ends, not nested inside display math.
                    // Wrap the span around it.
                    $text = $this->insert_span($text, $inlinestart, $i);

                    $inlinestart = -1; // Reset.
                    $i += 28; // The $text length changed due to the <span>.
                    $len += 28;
                    $changesdone = true;
                }
            } else {
                // Display math open.
                if (
                    ($text[$i - 1] === '\\' && $text[$i] === ']' && $displaybracket) ||
                        ($text[$i - 1] === '$' && $text[$i] === '$' && $displaydollar)
                ) {
                    // Display math ends, wrap the span around it.
                    $text = $this->insert_span($text, $displaystart, $i);

                    $displaystart = -1; // Reset.
                    $displaybracket = false;
                    $displaydollar = false;
                    $i += 28; // The $text length changed due to the <span>.
                    $len += 28;
                    $changesdone = true;
                }
            }

            ++$i;
        }
        return [$text, $changesdone];
    }

    /**
     * Wrap a portion of the $text inside a no link span
     * (<span class="nolink"></span>). The whole text is then returned.
     *
     * @param string $text The text to modify.
     * @param int $start The start index of the substring in $text that should
     * be wrapped in the span.
     * @param int $end The end index of the substring in $text that should be
     * wrapped in the span.
     * @return string The whole $text with the span inserted around
     * the defined substring.
     */
    protected function insert_span($text, $start, $end) {
        return substr_replace(
            $text,
            '<span class="nolink">' . substr($text, $start, $end - $start + 1) . '</span>',
            $start,
            $end - $start + 1
        );
    }

    /**
     * Escapes HTML tags within a string.
     *
     * This function replaces HTML tags enclosed in curly brackets with their respective HTML entities.
     *
     * @param string $text The input string containing HTML tags.
     * @return string Returns the input string with HTML tags escaped.
     */
    private function escape_html_tag_wrapper(string $text): string {
        return preg_replace_callback('/\{([^}]+)\}/', function (array $matches): string {
            $search = ['<', '>'];
            $replace = ['&lt;', '&gt;'];
            return str_replace($search, $replace, $matches[0]);
        }, $text);
    }
}
