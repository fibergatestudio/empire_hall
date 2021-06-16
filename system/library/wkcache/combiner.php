<?php

namespace Wkcache;

class Combiner {

    private $db;
    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    // Add min config and admin side condition to make file blank
    public function combineJs($file_path, $admin = false) {
        $file_path_explode = explode('/', $file_path);
        $filename = end($file_path_explode);
        $exists = $this->db->query("SELECT id FROM ".DB_PREFIX."file_combine WHERE type = 'js' AND file_name = '".$this->db->escape($filename)."' AND file_path = '".$this->db->escape($file_path)."' ")->row;
        if(!$exists) {
            $backup = '';
            if(file_exists(DIR_SYSTEM.'combined.js')) {
                if(!$admin) {
                    $backup = file_get_contents(DIR_SYSTEM.'combined.js');
                } else {
                    $backup = '';
                }
            }
            $current_file_content = file_get_contents(DIR_APPLICATION.'../'.$file_path);
            if($this->config->get('module_oc_cache_minify_js')) {
                $current_file_content = $this->minify_js($current_file_content);
            }
            $code = 'try{' . PHP_EOL . $current_file_content . PHP_EOL . '}catch(e){console.log(e.message + " in file: ' . $file_path . '")};';
            file_put_contents(DIR_SYSTEM.'combined.js', $backup.$code);
            $this->db->query("INSERT INTO ".DB_PREFIX."file_combine SET type = 'js', file_name = '".$this->db->escape($filename)."', file_path = '".$this->db->escape($file_path)."' ");
        } else {
            // echo "Already added " . $file_path;
        }
    }

    public function combineCss($file_path, $admin = false) {
        $file_path_explode = explode('/', $file_path);
        $filename = end($file_path_explode);
        $exists = $this->db->query("SELECT id FROM ".DB_PREFIX."file_combine WHERE type = 'css' AND file_name = '".$this->db->escape($filename)."' AND file_path = '".$this->db->escape($file_path)."' ")->row;
        if(!$exists) {
            $backup = '';
            if(file_exists(DIR_SYSTEM.'combined.css')) {
                if(!$admin) {
                    $backup = file_get_contents(DIR_SYSTEM.'combined.css');
                } else {
                    $backup = '';
                }
            }
            $code = file_get_contents(DIR_APPLICATION.'../'.$file_path).PHP_EOL;
            if($this->config->get('module_oc_cache_minify_css')) {
                $code = $this->minify_css($code);
            }
            file_put_contents(DIR_SYSTEM.'combined.css', $backup.$code);
            $this->db->query("INSERT INTO ".DB_PREFIX."file_combine SET type = 'css', file_name = '".$this->db->escape($filename)."', file_path = '".$this->db->escape($file_path)."' ");
        } else {
            // echo "Already added " . $file_path;
        }
    }

    public function minify_js($str) {
        $res = '';
        $maybe_regex = true;
        $i=0;
        $current_char = '';
        while ($i+1<strlen($str)) {
            if ($maybe_regex && $str[$i]=='/' && $str[$i+1]!='/' && $str[$i+1]!='*' && @$str[$i-1]!='*') {//regex detected
                if (strlen($res) && $res[strlen($res)-1] === '/') $res .= ' ';
                do {
                    if ($str[$i] == '\\') {
                        $res .= $str[$i++];
                    } elseif ($str[$i] == '[') {
                        do {
                            if ($str[$i] == '\\') {
                                $res .= $str[$i++];
                            }
                            $res .= $str[$i++];
                        } while ($i<strlen($str) && $str[$i]!=']');
                    }
                    $res .= $str[$i++];
                } while ($i<strlen($str) && $str[$i]!='/');
                $res .= $str[$i++];
                $maybe_regex = false;
                continue;
            } elseif ($str[$i]=='"' || $str[$i]=="'") {//quoted string detected
                $quote = $str[$i];
                do {
                    if ($str[$i] == '\\') {
                        $res .= $str[$i++];
                    }
                    $res .= $str[$i++];
                } while ($i<strlen($str) && $str[$i]!=$quote);
                $res .= $str[$i++];
                continue;
            } elseif ($str[$i].$str[$i+1]=='/*' && @$str[$i+2]!='@') {//multi-line comment detected
                $i+=3;
                while ($i<strlen($str) && $str[$i-1].$str[$i]!='*/') $i++;
                if ($current_char == "\n") $str[$i] = "\n";
                else $str[$i] = ' ';
            } elseif ($str[$i].$str[$i+1]=='//') {//single-line comment detected
                $i+=2;
                while ($i<strlen($str) && $str[$i]!="\n" && $str[$i]!="\r") $i++;
            }

            $LF_needed = false;
            if (preg_match('/[\n\r\t ]/', $str[$i])) {
                if (strlen($res) && preg_match('/[\n ]/', $res[strlen($res)-1])) {
                    if ($res[strlen($res)-1] == "\n") $LF_needed = true;
                    $res = substr($res, 0, -1);
                }
                while ($i+1<strlen($str) && preg_match('/[\n\r\t ]/', $str[$i+1])) {
                    if (!$LF_needed && preg_match('/[\n\r]/', $str[$i])) $LF_needed = true;
                    $i++;
                }
            }

            if (strlen($str) <= $i+1) break;

            $current_char = $str[$i];

            if ($LF_needed) $current_char = "\n";
            elseif ($current_char == "\t") $current_char = " ";
            elseif ($current_char == "\r") $current_char = "\n";

            // detect unnecessary white spaces
            if ($current_char == " ") {
                if (strlen($res) &&
                    (
                    preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res[strlen($res)-1].$str[$i+1]) ||
                    preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1]) // for example i+ ++j;
                    )) $res .= $current_char;
            } elseif ($current_char == "\n") {
                if (strlen($res) &&
                    (
                    preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res[strlen($res)-1].$str[$i+1]) ||
                    (strlen($res)>1 && preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-2].$res[strlen($res)-1])) ||
                    (strlen($str)>$i+2 && preg_match('/^(\+\+)|(--)$/', $str[$i+1].$str[$i+2])) ||
                    preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1])// || // for example i+ ++j;
                    )) $res .= $current_char;
            } else $res .= $current_char;

            // if the next charachter be a slash, detects if it is a divide operator or start of a regex
            if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char)) $maybe_regex = true;
            elseif (!preg_match('/[\n ]/', $current_char)) $maybe_regex = false;

            $i++;
        }
        if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i])) $res .= $str[$i];
        return $res;
    }

    public function convertUrl($url, $count) {
        global $settings, $mimeTypes, $fileDir;

        static $baseUrl = '';

        $url = trim($url);

        if (preg_match('@^[^/]+:@', $url)) return $url;

        $fileType = substr(strrchr($url, '.'), 1);
        if (isset($mimeTypes[$fileType])) $mimeType = $mimeTypes[$fileType];
        elseif (function_exists('mime_content_type')) $mimeType = mime_content_type($url);
        else $mimeType = null;

        if (!$settings['embed'] ||
            !file_exists($fileDir.$url) ||
            ($settings['embedMaxSize'] > 0 && filesize($fileDir.$url) > $settings['embedMaxSize']) ||
            !$fileType ||
            in_array($fileType, $settings['embedExceptions']) ||
            !$mimeType ||
            $count > 1) {
            if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'].'?') === 0 ||
                strpos($_SERVER['REQUEST_URI'], rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/').'/?') === 0) {
                if (!$baseUrl) return $fileDir . $url;
            }
            return $baseUrl . $url;
        }

        $contents = file_get_contents($fileDir.$url);

        if ($fileType == 'css') {
            $oldFileDir = $fileDir;
            $fileDir = rtrim(dirname($fileDir.$url), '\/').'/';
            $oldBaseUrl = $baseUrl;
            $baseUrl = 'http'.(@$_SERVER['HTTPS']?'s':'').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/').'/'.$fileDir;
            $contents = minify_css($contents);
            $fileDir = $oldFileDir;
            $baseUrl = $oldBaseUrl;
        }

        $base64   = base64_encode($contents);
        return 'data:' . $mimeType . ';base64,' . $base64;
    }

    public function minify_css($str) {
        $res = '';
        $i=0;
        $inside_block = false;
        $current_char = '';
        while ($i+1<strlen($str)) {
            if ($str[$i]=='"' || $str[$i]=="'") {//quoted string detected
                $res .= $quote = $str[$i++];
                $url = '';
                while ($i<strlen($str) && $str[$i]!=$quote) {
                    if ($str[$i] == '\\') {
                        $url .= $str[$i++];
                    }
                    $url .= $str[$i++];
                }
                if (strtolower(substr($res, -5, 4))=='url(' || strtolower(substr($res, -9, 8)) == '@import ') {
                    $url = $this->convertUrl($url, substr_count($str, $url));
                }
                $res .= $url;
                $res .= $str[$i++];
                continue;
            } elseif (strtolower(substr($res, -4))=='url(') {//url detected
                $url = '';
                do {
                    if ($str[$i] == '\\') {
                        $url .= $str[$i++];
                    }
                    $url .= $str[$i++];
                } while ($i<strlen($str) && $str[$i]!=')');
                $url = $this->convertUrl($url, substr_count($str, $url));
                $res .= $url;
                $res .= $str[$i++];
                continue;
            } elseif ($str[$i].$str[$i+1]=='/*') {//css comment detected
                $i+=3;
                while ($i<strlen($str) && $str[$i-1].$str[$i]!='*/') $i++;
                if ($current_char == "\n") $str[$i] = "\n";
                else $str[$i] = ' ';
            }

            if (strlen($str) <= $i+1) break;

            $current_char = $str[$i];

            if ($inside_block && $current_char == '}') {
                $inside_block = false;
            }

            if ($current_char == '{') {
                $inside_block = true;
            }

            if (preg_match('/[\n\r\t ]/', $current_char)) $current_char = " ";

            if ($current_char == " ") {
                $pattern = $inside_block?'/^[^{};,:\n\r\t ]{2}$/':'/^[^{};,>+\n\r\t ]{2}$/';
                if (strlen($res) &&	preg_match($pattern, $res[strlen($res)-1].$str[$i+1]))
                    $res .= $current_char;
            } else $res .= $current_char;

            $i++;
        }
        if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i])) $res .= $str[$i];
        return $res;
    }

}


?>
