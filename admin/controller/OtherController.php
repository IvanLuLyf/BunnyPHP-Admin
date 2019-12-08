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
        $this->render(['edit.html', ADMIN_VIEW_DIR]);
    }

    /**
     * @filter bunny.admin.auth
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

    /**
     * @filter bunny.admin.auth
     */
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
        $this->render(['add.html', ADMIN_VIEW_DIR]);
    }

    /**
     * @filter bunny.admin.auth
     */
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

    /**
     * @filter bunny.admin.auth
     * @param int $page
     * @param int $limit
     */
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
        $this->render(['manage.html', ADMIN_VIEW_DIR]);
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