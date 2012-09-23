<?php

/**
 * NG Framework
 * Version 0.1 Beta
 * Copyright (c) 2012, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
require_once (ROOT . DS . 'Library' . DS . 'NG' . DS . 'Bootstrap.php');

use NG\Bootstrap as FrameworkBootstrap;

/**
 * Bootstrap
 * @package NG
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2012, Nick Gejadze
 */
class Bootstrap extends FrameworkBootstrap {

    public function _initConfig() {
        //$config = \NG\Configuration::loadConfigFile(ROOT . DS . APPDIR . DS . 'Config' . DS . 'application.ini');
        //\NG\Registry::set("config", $config);
    }

    public function _initRoute() {
        //$route = \NG\Configuration::loadConfigFile(ROOT . DS . APPDIR . DS . 'Config' . DS . 'route.ini');
        //\NG\Route::addRoute($route['routes']['post']);
    }

    public function _initDB() {
        //$dbconfig = NG\Configuration::loadConfigFile(ROOT . DS . APPDIR . DS . 'Config' . DS . 'database.ini');
        //$db = new \NG\Database($dbconfig["Database"]["conname"]);
        //\NG\Registry::set("db", $db);
    }

}

?>
