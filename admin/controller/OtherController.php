<?php


namespace BunnyPHP\Admin\Controller;


use BunnyPHP\BunnyPHP;
use BunnyPHP\Config;
use BunnyPHP\Controller;
use BunnyPHP\Model;

define('ADMIN_VIEW_DIR', '@' . __DIR__ . '/../template/');

class OtherController extends Controller
{
    /**
     * @param int $id path(0)
     */
    public function ac_edit_get($id)
    {
        $mod = strtolower($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $navs = $config->get('navs', []);
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = $modelConf['column'];
        $column = array_keys($columnConf);

        /**
         * @var $model Model
         */
        $model = new $modelClass();

        $item = $model->where($primaryKey . ' =:pk ', ['pk' => $id])->fetch($column);
        $this->assign('primaryKey', $primaryKey);
        $this->assign('columns', $column);
        $this->assign('navs', $navs);
        $this->assign('conf', $columnConf);
        $this->assign('title', $modelConf['title']);
        $this->assign('mod', $mod);
        $this->assign('item', $item);
        $this->render(ADMIN_VIEW_DIR . 'edit.html');
    }

    /**
     * @filter auth
     * @param int $id path(0)
     */
    public function ac_edit_post($id = 0)
    {
        $mod = strtolower($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = $modelConf['column'];
        $column = array_keys($columnConf);

        /**
         * @var $model Model
         */
        $model = new $modelClass();
        $updateData = [];
        foreach ($column as $c) {
            if (isset($_REQUEST[$c])) {
                $updateData[$c] = $_REQUEST[$c];
            }
        }
        $ret = $model->where($primaryKey . ' =:pk ', ['pk' => $id])->update($updateData);
        $this->redirect('/admin/' . $mod . '/edit/' . $id);
    }

    public function ac_add_get()
    {
        $mod = strtolower($this->getController());

        $config = Config::load('bunny_php_admin');
        $navs = $config->get('navs', []);
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = $modelConf['column'];
        $column = array_keys($columnConf);

        $this->assign('primaryKey', $primaryKey);
        $this->assign('columns', $column);
        $this->assign('navs', $navs);
        $this->assign('conf', $columnConf);
        $this->assign('title', $modelConf['title']);
        $this->assign('mod', $mod);
        $this->render(ADMIN_VIEW_DIR . 'add.html');
    }

    public function ac_add_post()
    {
        $mod = strtolower($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = $modelConf['column'];
        $column = array_keys($columnConf);

        /**
         * @var $model Model
         */
        $model = new $modelClass();
        $newData = [];
        foreach ($column as $c) {
            if ($c == $primaryKey && empty($_REQUEST[$primaryKey])) {
                continue;
            }
            if (isset($_REQUEST[$c])) {
                $newData[$c] = $_REQUEST[$c];
            }
        }
        $ret = $model->add($newData);
        $this->redirect('/admin/' . $mod . '/manage');
    }

    public function ac_manage($page = 1, $limit = 10)
    {
        $mod = strtolower($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');
        $config = Config::load('bunny_php_admin');
        $navs = $config->get('navs', []);
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = $modelConf['column'];
        $column = array_keys($columnConf);
        /**
         * @var $model Model
         */
        $model = new $modelClass();
        $items = $model->limit($limit, ($page - 1) * $limit)->fetchAll($column);
        $this->assign('primaryKey', $primaryKey);
        $this->assign('columns', $column);
        $this->assign('navs', $navs);
        $this->assign('conf', $columnConf);
        $this->assign('title', $modelConf['title']);
        $this->assign('mod', $mod);
        $this->assign('items', $items);
        $this->assign('page', $page);
        $this->render(ADMIN_VIEW_DIR . 'manage.html');
    }
}