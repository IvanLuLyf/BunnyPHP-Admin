<?php

namespace BunnyPHP\Admin\Controller;

use BunnyPHP\Config;
use BunnyPHP\Controller;

define('ADMIN_VIEW_DIR', '@' . __DIR__ . '/../template/');

class IndexController extends Controller
{
    public function ac_index()
    {
        $config = Config::load('bunny_php_admin');
        $navs = $config->get('navs', []);
        $this->assign('navs', $navs);
        $this->assign('mod', '');
        $this->render(ADMIN_VIEW_DIR . 'index.html');
    }
}