<?php
namespace nest;

define('DOCROOT',     str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']));
define('METHOD',      strtolower($_SERVER['REQUEST_METHOD']));
define('ISXHR',       isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
define('DEBUG',       isset($_SERVER['_nest']['debug']) && !empty($_SERVER['_nest']['debug']));
define('BEFORE_FILE', isset($_SERVER['_nest']['before']) ? $_SERVER['_nest']['before']: 'before.php');
define('AFTER_FILE',  isset($_SERVER['_nest']['after'])  ? $_SERVER['_nest']['after']: 'after.php');

function run() {
    $nest = array(
        'before'        => isset($_SERVER['_nest']['before']) ? $_SERVER['_nest']['before']: 'before.php',
        'after'         => isset($_SERVER['_nest']['after'])  ? $_SERVER['_nest']['after']: 'after.php',
        'script_dir'    => dirname($_SERVER['SCRIPT_FILENAME']),
        'script'        => $_SERVER['SCRIPT_NAME'],
        'filter_files'  => array(), // array of scripts that contain filters (before's and after's)
        'filters'       => array()  // array of closures that will be called in the order they were defined
    );

    $GLOBALS['_nest'] = &$nest;

    /**
     * given a request for this script:
     *
     *  /admin/users/list.php
     *
     * we need to build an array of the following strings (which we'll then try to include())
     *
     *  /before.php
     *  /admin/before.php
     *  /admin/users/before.php
     *  /admin/users/list.php
     *  /admin/users/after.php
     *  /admin/after.php
     *  /after.php
     */

    $last_dir     = '';
    $before_files = array();
    $after_files  = array();
    foreach(array_slice(explode('/', $nest['script']), 0, -1) as $dir) {
        $abs_dir         = realpath(DOCROOT . "/$last_dir/$dir/");
        $before_abs_path = $abs_dir . '/' . BEFORE_FILE;
        $after_abs_path  = $abs_dir . '/' . AFTER_FILE;
        
        if (file_exists($before_abs_path)) {
            $before_files[] = $before_abs_path;
        }

        if ($nest['script_dir'] == $abs_dir) {
            $before_files[] = DOCROOT . $nest['script'];
        }

        if (file_exists($after_abs_path)) {
            array_unshift($after_files, $after_abs_path);
        }
        
        $last_dir .= '/' . $dir;
    }

    // glue all the filter files together
    $nest['filter_files'] = array_merge($before_files, $after_files);
    
    // include the existing filter files, where the filters (closures) are defined
    foreach ($nest['filter_files'] as $file) {
        if (DEBUG) echo "including $file<br>";
        include $file;
    }

    foreach ($nest['filters'] as $func) {
        // stop running the filters if a closure returns FALSE
        if ($func() === false) break;
    }

    if (DEBUG) var_dump($GLOBALS['_nest']);
}

function add($method, $closure) {
    if (   ($method == 'any')            // ALWAYS add 'any' filters
        || ($method == METHOD && !ISXHR) // only add GET filter if this request is a non-XHR GET (or POST, etc)
        || ($method == 'xhr'  &&  ISXHR) // only add in XHR filter if this request is an XHR
    )
    {
        $GLOBALS['_nest']['filters'][] = $closure;
    }
}

function any($closure)    { add('any',    $closure); }
function get($closure)    { add('get',    $closure); }
function post($closure)   { add('post',   $closure); }
function xhr($closure)    { add('xhr',    $closure); }
function put($closure)    { add('put',    $closure); }
function delete($closure) { add('delete', $closure); }

\nest\run();
exit;
