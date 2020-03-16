<?php

class Problem {

    // values
    public $error_types = array(
        'missing_url' => 'Missing "url" metadata in the shortcode\n\nPlease see the example at https://github.com/ilies-t/import-github-readme-wordpress',
        'bad_link' => 'The link you entered is incorrect\n\nPlease see the example at https://github.com/ilies-t/import-github-readme-wordpress',
        'parse_error' => 'The link you entered is correct but the plugin encountered some problems with GitHub\n\ncontact me at https://twitter.com/ilies_t or https://github.com/ilies-t',
        'unknow' => 'An unknown error has occurred\n\ncontact me at https://twitter.com/ilies_t or https://github.com/ilies-t'
    );

    // return an error in browser console
    private function log_console_error(string $value) : string {
        return "<script>console.error(`" . $value . "`);</script>";
    }

    // errors
    public function new_error(string $type) : string {
        $message = "\n\n(Import GitHub Readme plugin)\n\n " . $this->error_types[$type];
        return $this->log_console_error($message);
    }

}

?>
