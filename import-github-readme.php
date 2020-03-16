<?php
/**
 * @package import-github-readme
 * @version 0.0.1
 */

/*
Plugin Name: Import GitHub Readme
Plugin URI: http://github.com/ilies-t/import-github-readme-wordpress
Description: Import a readme from GitHub repository to a post
Author: Ilies T
Version: 0.0.1
Author URI: https://ilies.ch
*/

// load required file(s)
require_once('_includes/class.problem.php');

/*********************** begining ***********************/

/**
 * construct new Problem()
 * @param type type of problem encountered
 */
function problem(string $type) : string {
    return (new Problem())->new_error($type);
}

/**
 * get HTML from github link
 * @param args metas from wordpress shortcode
 */
function get_readme(array $args) : string {
    try {
        // create an entire url with the `url` meta from shortcode
        $url = 'https://github.com/' . $args['url'];

        // get the HTML from $url and remove the anchors
        $html_request = file_get_contents($url);
        $html_request = preg_replace('#<svg class="octicon octicon-link">(.*?)</svg>#', '', $html_request);

        // if the link return nothing
        if(empty($html_request)) {
            return problem('bad_link');
        } else {
            try {
                $content = new DOMDocument;
                $content->loadHTML(mb_convert_encoding($html_request, 'HTML-ENTITIES', 'UTF-8'));
                
                // parse with xpath to get only `article.markdown-body`
                $finder = new DOMXPath($content);
                $all_elements = $finder->query("//article[contains(@class, 'markdown-body')]");

                foreach ($all_elements as $key => $value) {
                    $content = $content->saveHTML($value);
                }

                // replace relative link by absolute link for `a` and `img` tags
                $doc = new DOMDocument;
                $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

                $replacable = array(
                    "a" => "href",
                    "img" => "src"
                );

                foreach ($replacable as $key => $value) {

                    foreach ($doc->getElementsByTagName($key) as $current_tag) {
                        $relative_link = $current_tag->getAttribute($value);

                        // check if this is a relative link
                        // warning for `./` links
                        if (strpos($relative_link, '/') === 0) {

                            // replace relative link of absolute link
                            $current_tag->setAttribute($value, 'https://github.com' . $relative_link);
        
                            // save modification
                            $content = $doc->saveHTML();
                        }
                    };
                }

                return $content;

            } catch(Exception $e) {
                return problem('parse_error');
            };
        };

    } catch (Exception $e) {
        return problem('unknow');
    };
}

// register function to the `github-readme` shortcode
add_shortcode('github-readme', 'get_readme');

?>