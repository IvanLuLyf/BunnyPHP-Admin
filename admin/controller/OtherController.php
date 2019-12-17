<?php


namespace Bunny\Admin\Controller;


use BunnyPHP\BunnyPHP;
use BunnyPHP\Config;
use BunnyPHP\Controller;
use BunnyPHP\Model;

define('ADMIN_VIEW_DIR', __DIR__ . '/../template/');

class OtherController extends Controller
{
    /**
     * @filter bunny.admin.auth
     * @param int $id path(0)
     */
    public function ac_edit_get($id)
    {
        $mod = lcfirst($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = isset($modelConf['edit']) ? $modelConf['edit'] : $modelConf['column'];
        $column = array_keys($columnConf);

        $navs = $config->get('navs', []);
        $appPath = $config->get('path', 'admin');

        /**
         * @var $model Model
         */
        $model = new $modelClass();
        $item = $model->where($primaryKey . ' =:pk ', ['pk' => $id])->fetch($column);

        $this->assignAll([
            'mod' => $mod,
            'title' => $modelConf['title'],
            'navs' => $navs,
            'primaryKey' => $primaryKey,
            'columns' => $column,
            'conf' => $columnConf,
            'appPath' => $appPath,
            'item' => $item,
        ])->render(['edit.html', ADMIN_VIEW_DIR]);
    }

    /**
     * @filter bunny.admin.auth
     * @param int $id path(0)
     */
    public function ac_edit_post($id = 0)
    {
        $mod = lcfirst($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = isset($modelConf['edit']) ? $modelConf['edit'] : $modelConf['column'];
        $column = array_keys($columnConf);

        $appPath = $config->get('path', 'admin');

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

        if ($ret > 0) {
            $this->redirect("/{$appPath}/{$mod}/edit/{$id}");
        } else {
            $navs = $config->get('navs', []);
            $this->assignAll([
                'mod' => $mod,
                'title' => $modelConf['title'],
                'navs' => $navs,
                'primaryKey' => $primaryKey,
                'columns' => $column,
                'conf' => $columnConf,
                'appPath' => $appPath,
                'tp_error_msg' => '保存失败',
            ])->render(['error.html', ADMIN_VIEW_DIR]);
        }
    }

    /**
     * @filter bunny.admin.auth
     */
    public function ac_add_get()
    {
        $mod = lcfirst($this->getController());

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = isset($modelConf['add']) ? $modelConf['add'] : $modelConf['column'];
        $column = array_keys($columnConf);

        $navs = $config->get('navs', []);
        $appPath = $config->get('path', 'admin');

        $this->assignAll([
            'mod' => $mod,
            'title' => $modelConf['title'],
            'navs' => $navs,
            'primaryKey' => $primaryKey,
            'columns' => $column,
            'conf' => $columnConf,
            'appPath' => $appPath,
        ])->render(['add.html', ADMIN_VIEW_DIR]);
    }

    /**
     * @filter bunny.admin.auth
     */
    public function ac_add_post()
    {
        $mod = lcfirst($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = isset($modelConf['add']) ? $modelConf['add'] : $modelConf['column'];
        $column = array_keys($columnConf);

        $appPath = $config->get('path', 'admin');

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
        if ($ret > 0) {
            $this->redirect("/{$appPath}/{$mod}/manage");
        } else {
            $navs = $config->get('navs', []);
            $this->assignAll([
                'mod' => $mod,
                'title' => $modelConf['title'],
                'navs' => $navs,
                'primaryKey' => $primaryKey,
                'columns' => $column,
                'conf' => $columnConf,
                'appPath' => $appPath,
                'tp_error_msg' => '添加失败',
            ])->render(['error.html', ADMIN_VIEW_DIR]);
        }
    }

    /**
     * @filter bunny.admin.auth
     * @param int $page
     * @param int $limit
     */
    public function ac_manage($page = 1, $limit = 10)
    {
        $mod = lcfirst($this->getController());
        $modelClass = BunnyPHP::getClassName($mod, 'model');

        $config = Config::load('bunny_php_admin');
        $modelConf = $config->get('models')[$mod];
        $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
        $columnConf = isset($modelConf['view']) ? $modelConf['view'] : $modelConf['column'];
        $column = array_keys($columnConf);

        $navs = $config->get('navs', []);
        $appPath = $config->get('path', 'admin');

        /**
         * @var $model Model
         */
        $model = new $modelClass();
        $items = $model->limit($limit, ($page - 1) * $limit)->fetchAll($column);
        $this->assignAll([
            'mod' => $mod,
            'title' => $modelConf['title'],
            'navs' => $navs,
            'primaryKey' => $primaryKey,
            'columns' => $column,
            'conf' => $columnConf,
            'appPath' => $appPath,
            'items' => $items,
            'page' => $page,
        ])->render(['manage.html', ADMIN_VIEW_DIR]);
    }

    /**
     * @filter bunny.admin.auth
     * @param array $path
     * @param int $page
     * @param int $limit
     */
    public function other(array $path = [], $page = 1, $limit = 10)
    {
        $mod = strtolower($this->getController());
        $ac = $this->getAction();
        if (is_numeric($ac)) {
            $modelClass = BunnyPHP::getClassName($mod, 'model');
            $config = Config::load('bunny_php_admin');
            $navs = $config->get('navs', []);
            $modelConf = $config->get('models')[$mod];
            $primaryKey = isset($modelConf['pk']) ? $modelConf['pk'] : 'id';
            $relations = isset($modelConf['relation']) ? $modelConf['relation'] : [];
            $modAssoc = strtolower($path[0]);
            if (isset($relations[$modAssoc])) {
                $relation = $relations[$modAssoc];

                $columnConf = $modelConf['column'];
                $column = array_keys($columnConf);

                /**
                 * @var $model Model
                 */
                $model = new $modelClass();

                $item = $model->where($primaryKey . ' =:pk ', ['pk' => $ac])->fetch($column);

                $assocKey = $item[$relation['key']];

                $modelAssocConf = $config->get('models')[$modAssoc];
                $assocModelClass = BunnyPHP::getClassName($modAssoc, 'model');
                /**
                 * @var $assocModel Model
                 */
                $assocModel = new $assocModelClass();
                $assocColumnConf = $modelAssocConf['column'];
                $assocColumn = array_keys($assocColumnConf);
                $items = $assocModel->where($relation['assoc'] . ' =:ak ', ['ak' => $assocKey])->limit($limit, ($page - 1) * $limit)->fetchAll($assocColumn);
                print_r($items);
            }
        }
    }
}