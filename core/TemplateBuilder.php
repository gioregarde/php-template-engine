<?php

/**
 * TemplateBuilder.php
 * 
 * This is developed to help display dynamic variables using custom tag elements in html files
 * 
 * Custom Javascript Variable Initialization
 * {{ variable }}
 * 
 * Custom Attributes
 *   tb:<attr>=""
 * 
 * Custom Tags
 *   tb:jscript
 *   tb:include attr=""
 *   tb:set var="" val=""
 *   tb:print var=""
 *   tb:if var=""
 *   tb:for item="" var=""
 * 
 * TODO Tags
 *   else if - (to be considered)
 *   check how to remove unrecognized custom end tag
 *   attribute range (for loop)
 * 
 * @author gio regarde <gioregarde@outlook.com>
 */
class TemplateBuilder {

    const TAG_EXT                              = 'tb:';

    const REGEX_FIND_TAG                       = '/<'.self::TAG_EXT.'.*?>/s';
    const REGEX_FIND_TAG_END                   = '/<\/'.self::TAG_EXT.'.*?>/s';

    const REGEX_FIND_FOR_TAG_FULL              = '/<'.self::TAG_EXT.'for.*?<\/'.self::TAG_EXT.'for>/s';
    const REGEX_FIND_FOR_TAG_STR               = '/<'.self::TAG_EXT.'for.*?>/s';
    const REGEX_FIND_FOR_TAG_END               = '/<\/'.self::TAG_EXT.'for>/s';

    const REGEX_FIND_IF_TAG_FULL               = '/<'.self::TAG_EXT.'if.*?<\/'.self::TAG_EXT.'if>/s';
    const REGEX_FIND_IF_TAG_STR                = '/<'.self::TAG_EXT.'if.*?>/s';
    const REGEX_FIND_IF_TAG_END                = '/<\/'.self::TAG_EXT.'if>/s';

    const REGEX_FIND_JSCRIPT_TAG_FULL          = '/<'.self::TAG_EXT.'jscript.*?<\/'.self::TAG_EXT.'jscript>/s';
    const REGEX_FIND_JSCRIPT_TAG_STR           = '/<'.self::TAG_EXT.'jscript.*?>/s';
    const REGEX_FIND_JSCRIPT_TAG_END           = '/<\/'.self::TAG_EXT.'jscript>/s';

    const REGEX_FIND_VAR_ATTR                  = '/var=\".*?\"/s';
    const REGEX_FIND_VAL_ATTR                  = '/val=\".*?\"/s';
    const REGEX_FIND_ITEM_ATTR                 = '/item=\".*?\"/s';
    const REGEX_FIND_FILE_ATTR                 = '/file=\".*?\"/s';

    const REGEX_FIND_CUS_ATTR                  = '/'.self::TAG_EXT.'.*?=\".*?\"/s';
    const REGEX_FIND_JSCRIPT_VAR               = '/{{.*?}}/s';

    const TAG_INCLUDE                          = self::TAG_EXT.'include';
    const TAG_SET                              = self::TAG_EXT.'set';
    const TAG_PRINT                            = self::TAG_EXT.'print';
    const TAG_IF                               = self::TAG_EXT.'if';
    const TAG_FOR                              = self::TAG_EXT.'for';
    const TAG_JSCRIPT                          = self::TAG_EXT.'jscript';

    const JSCRIPT_START_TAG                    = '<script type="text/javascript">';
    const JSCRIPT_END_TAG                      = '</script>';

    const METHOD_GET                           = 'get';

    const DEFAULT_VIEW                         = 'view does not exist';

    /**
     * sets key value pair variable
     *
     * @param object $key - object key
     * @param object $val - object value
     * @return null
     */
    function setVariable($key, $val) {
        $this->{$key} = $val;
    }

    /**
     * Renders HTML
     *
     * @param string $view - filename to be used to display rendered html
     * @return none
     */
    function render($view) {
        echo $this->processFile($view, self::DEFAULT_VIEW); // TODO add setDefaultView function
    }

    /**
     * Process Layout File
     *
     * @param string $file        - filename to be used to display rendered html
     * @param string $default     - fallback display
     * @return string $default    - output html
     */
    private function processFile($file, $default) {
        if (file_exists($file)) {
            $default = $this->processTemplate(file_get_contents($file));
        }
        return $default;
    }

    /**
     * Process Template Tags
     *
     * @param string $contents    - input html
     * @return string             - output html (processed)
     */
    private function processTemplate($contents) {
        preg_match(self::REGEX_FIND_TAG, $contents, $tag_array);
        if (empty($tag_array[0])) {

            // remove custom close tags (check if can be moved tag identification)
            preg_match(self::REGEX_FIND_TAG_END, $contents, $tag_array);
            if (!empty($tag_array[0])) {
                $contents = preg_replace('/'.$this->escapeRegex($tag_array[0]).'/s', '', $contents, 1);
                return $this->processTemplate($contents);
            }

            // process custom attributes
            preg_match(self::REGEX_FIND_CUS_ATTR, $contents, $attr_array);
            if (!empty($attr_array[0])) {
                $contents = $this->processCustomAttr($attr_array[0], $contents);
                return $this->processTemplate($contents);
            }

            return $contents;
        }
        if (strpos($tag_array[0], self::TAG_INCLUDE) !== false) {
            $contents = $this->processIncludeTag($tag_array[0], $contents);
        } elseif (strpos($tag_array[0], self::TAG_SET) !== false) {
            $contents = $this->processSetTag($tag_array[0], $contents);
        } elseif (strpos($tag_array[0], self::TAG_PRINT) !== false) {
            $contents = $this->processPrintTag($tag_array[0], $contents);
        } elseif (strpos($tag_array[0], self::TAG_IF) !== false) {
            preg_match(self::REGEX_FIND_IF_TAG_FULL, $contents, $tag_array);
            $contents = $this->processIfTag($tag_array[0], $contents);
        } elseif (strpos($tag_array[0], self::TAG_FOR) !== false) {
            preg_match(self::REGEX_FIND_FOR_TAG_FULL, $contents, $tag_array);
            $contents = $this->processForTag($tag_array[0], $contents);
        } elseif (strpos($tag_array[0], self::TAG_JSCRIPT) !== false) {
            preg_match(self::REGEX_FIND_JSCRIPT_TAG_FULL, $contents, $tag_array);
            $contents = $this->processJscriptTag($tag_array[0], $contents);
        } else {
            // TODO check if can remove end tag
            $contents = preg_replace('/'.$this->escapeRegex($tag_array[0]).'/s', '', $contents, 1);
        }
        return $this->processTemplate($contents);
    }

    /**
     * Process Custom Javascript Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processJscriptTag($tag, $contents) {
        preg_match_all(self::REGEX_FIND_JSCRIPT_VAR, $tag, $var_array);

        $tag_parse = preg_replace(self::REGEX_FIND_JSCRIPT_TAG_STR, self::JSCRIPT_START_TAG, $tag);
        $tag_parse = preg_replace(self::REGEX_FIND_JSCRIPT_TAG_END, self::JSCRIPT_END_TAG, $tag_parse);

        if (empty($var_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', $tag_parse, $contents, 1);
        }

        foreach ($var_array[0] as $var_name) {
            $var_name_parse = substr($var_name, 2, -2);
            $var_name_val = $this->getValue($var_name_parse, $var_name_parse);
            if (!is_string($var_name_val)) {
                $var_name_val = $var_name_parse;
            }
            $tag_parse = preg_replace('/'.$var_name.'/s', $var_name_val, $tag_parse, 1);
        }

        return preg_replace('/'.$this->escapeRegex($tag).'/s', $tag_parse, $contents, 1);
    }

    /**
     * Process Custom Attributes
     *
     * @param string $attr
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processCustomAttr($attr, $contents) {
        $attr_name = $this->stripAttr($attr);
        $attr_val = $this->getValue($attr_name, $attr_name);
        return preg_replace('/'.$this->escapeRegex($attr).'/s', str_replace($attr_name, $attr_val, substr($attr, strlen(self::TAG_EXT))), $contents, 1);
    }

    /**
     * Process Custom Include Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processIncludeTag($tag, $contents) {
        preg_match(self::REGEX_FIND_FILE_ATTR, $tag, $file_array);
        if (empty($file_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }
        $file = $this->stripAttr($file_array[0]);
        $file_val = $this->getValue($file, $file);
        if (!is_string($file_val)) {
            $file_val = $file;
        }

        return preg_replace('/'.$this->escapeRegex($tag).'/s', $this->processFile($file_val, ''), $contents, 1);
    }

    /**
     * Process Custom Set Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processSetTag($tag, $contents) {
        preg_match(self::REGEX_FIND_VAR_ATTR, $tag, $var_array);
        if (empty($var_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }
        $var_name = $this->stripAttr($var_array[0]);

        preg_match(self::REGEX_FIND_VAL_ATTR, $tag, $val_array);
        if (empty($val_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }

        $value = $this->stripAttr($val_array[0]);

        $this->{$var_name} = $this->getValue($value, $value);
        return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
    }

    /**
     * Process Custom Print Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processPrintTag($tag, $contents) {
        preg_match(self::REGEX_FIND_VAR_ATTR, $tag, $var_array);
        if (empty($var_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }

        $var_name = $this->stripAttr($var_array[0]);

        $var_name_val = $this->getValue($var_name, '');
        if (is_array($var_name_val)) {
            $var_name_val = implode($var_name_val);
        } elseif (is_object($var_name_val)) {
            $var_name_val = print_r($var_name_val, true);
        }

        return preg_replace('/'.$this->escapeRegex($tag).'/s', $var_name_val, $contents, 1);
    }

    /**
     * Process Custom If Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processIfTag($tag, $contents) {
        preg_match(self::REGEX_FIND_VAR_ATTR, $tag, $var_array);
        if (empty($var_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }
        $var_name = $this->stripAttr($var_array[0]);

        $var_name_val = $this->getValue($var_name, '');
        $process_content = '';
        if (boolval($var_name_val)) {
            $tag_parse = preg_replace(self::REGEX_FIND_IF_TAG_STR, '', $tag);
            $tag_parse = preg_replace(self::REGEX_FIND_IF_TAG_END, '', $tag_parse);
            $process_content = $this->processTemplate($tag_parse);
        }
        return preg_replace('/'.$this->escapeRegex($tag).'/s', $process_content, $contents, 1);
    }

    /**
     * Process Custom For Tags
     *
     * @param string $tag
     * @param string $contents
     * @return string             - output html (processed)
     */
    private function processForTag($tag, $contents) {
        preg_match(self::REGEX_FIND_ITEM_ATTR, $tag, $item_array);
        if (empty($item_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }
        $item = $this->stripAttr($item_array[0]);

        preg_match(self::REGEX_FIND_VAR_ATTR, $tag, $var_array);
        if (empty($var_array[0])) {
            return preg_replace('/'.$this->escapeRegex($tag).'/s', '', $contents, 1);
        }
        $var_name = $this->stripAttr($var_array[0]);

        $tag_parse = preg_replace(self::REGEX_FIND_FOR_TAG_STR, '', $tag);
        $tag_parse = preg_replace(self::REGEX_FIND_FOR_TAG_END, '', $tag_parse);

        $process_content = '';
        foreach ($this->getValue($item, array()) as $item_var) {
            $this->{$var_name} = $item_var;
            $process_content = $process_content.$this->processTemplate($tag_parse);
        }

        return preg_replace('/'.$this->escapeRegex($tag).'/s', $process_content, $contents, 1);
    }

    /**
     * Get Value
     *
     * @param string $src
     * @param string $default
     * @return string             - return value
     */
    private function getValue($src, $default) {
        $src_array = explode(".", $src);
        $src_val = $default;
        foreach ($src_array as $index => $src_var) {
            if ($index == 0) {
                if (isset($this->{$src_var})) {
                    $src_val = $this->{$src_var};
                } else {
                    break;
                }
            } else {
                $method = self::METHOD_GET.ucfirst($src_var);
                if (is_object($src_val) && method_exists($src_val, $method)) {
                    $src_val = $src_val->$method();
                } else {
                    $src_val = $default;
                }
            }
        }
        return $src_val;
    }

    private function stripAttr($attr) {
        return explode("\"", $attr)[1];
    }

    /**
     * Escape Regex for Custom tags and attributes
     *
     * @param string $tag
     * @return string
     */
    private function escapeRegex($tag) {
        $tag = preg_replace('/\"/s', '\\"', $tag);
        $tag = preg_replace('/\//s', '\\/', $tag);
        $tag = preg_replace('/\(/s', '\\(', $tag);
        $tag = preg_replace('/\)/s', '\\)', $tag);
        $tag = preg_replace('/\?/s', '\\?', $tag);
        $tag = preg_replace('/\[/s', '\\[', $tag);
        $tag = preg_replace('/\]/s', '\\]', $tag);
        $tag = preg_replace('/\+/s', '\\+', $tag);
        $tag = preg_replace('/\-/s', '\\-', $tag);
        $tag = preg_replace('/\*/s', '\\*', $tag);
        $tag = preg_replace('/\^/s', '\\^', $tag);
        $tag = preg_replace('/\\$/s', '\\\$', $tag);
        return $tag;
    }

    /**
     * For Debugging
     *
     * @param string $par
     * @return null
     * @deprecated remove
     */
    private function log($par) {
        echo '<script>';
        print_r($par);
        echo '</script>';
    }

}

?>