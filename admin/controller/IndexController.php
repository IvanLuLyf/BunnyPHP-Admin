<?php

namespace Bunny\Admin\Controller;

use BunnyPHP\Config;
use BunnyPHP\Controller;

define('ADMIN_VIEW_DIR', __DIR__ . '/../template/');

class IndexController extends Controller
{
    /**
     * @filter bunny.admin.auth
     */
    public function ac_index()
    {
        $config = Config::load('bunny_php_admin');
        $navs = $config->get('navs', []);
        $this->assign('navs', $navs);
        $this->assign('mod', '');
        $this->assign('admin_version', '1.0.0');
        $this->render(['index.html', ADMIN_VIEW_DIR]);
    }
}