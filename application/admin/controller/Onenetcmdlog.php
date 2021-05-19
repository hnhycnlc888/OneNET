<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use service\OnenetService;
use think\Db;

/**
 * 应用向OneNet平台发送命令日志管理控制器
 * Class Onenetcmdlog
 * @package app\admin\controller
 * @author Liuchao <249757247@qq.com>
 * @date 2021/03/02 16:15
 */
class Onenetcmdlog extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'OnenetCmdLog';
    public $product_table = 'OnenetProduct';

    /**
     * 命令日志列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '命令日志管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        //获取产品id列表
        $ret = OnenetService::getAllProducts();
        $product_ids = [];
        foreach($ret as $k => $v){
            $product_ids[] = $v['product_id'];
        }
        unset($ret);
        //$product_ids = Db::name($this->product_table)->column('product_id');
        // 实例Query对象
        $db = Db::name($this->table);
        // 应用搜索条件
        foreach (['project_id', 'product_id', 'device_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
		//排序
		$db->order('id', 'desc');
        $this->assign('product_ids', $product_ids);
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 删除命令日志
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("命令日志删除成功！", '');
        }
        $this->error("命令日志删除失败，请稍候再试！");
    }
}
