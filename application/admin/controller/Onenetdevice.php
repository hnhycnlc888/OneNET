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
use service\LogService;
use util\Page;
use think\Db;

/**
 * OneNet平台设备管理控制器
 * Class Onenetdevice
 * @package app\admin\controller
 * @author Liuchao <249757247@qq.com>
 * @date 2021/03/03 10:16
 */
class Onenetdevice extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'OnenetDevice';
    public $product_table = 'OnenetProduct';

    /**
     * 来自OneNet平台设备列表调试
     */
    public function index()
    {
        // 获取到所有GET参数
        $get = $this->request->get();
        // 设置页面标题
		$this->assign('title', '设备管理调试');

		//获取产品id列表
		$ret = OnenetService::getAllProducts();
		$product_ids = [];
		foreach($ret as $k => $v){
			$product_ids[] = $v['product_id'];
		}
		unset($ret);
		//本地库读取产品列表
		//$product_ids = Db::name($this->product_table)->column('product_id');
		$this->assign('product_ids', $product_ids);

		//分页数据
		$ret = OnenetService::getPageDevices($get);
		$list = $ret['data']['list'];

		//分页导航
		$page = isset($get['page']) ? $get['page'] : 1;
		$p = new Page($ret['data']['meta']['total'], 5, $page, 20);
		//return view('index', [ 'page' => $p->showPages(1), 'list' => $list ]);
		return $this->fetch('index', [ 'page' => $p->showPages(1), 'list' => $list ]);
	}

    /**
     * 远程重启设备
     */
    public function reboot()
    {
		//详情直接通过post参数带过来
		$project_id = $this->request->post('project_id');
		$product_id = $this->request->post('product_id');
		$device_name = $this->request->post('device_name');
		//从本地库根据记录id取详情
		//$device_info = Db::name($this->table)->where("id", $this->request->post('id'))->find();
		//$project_id = $device_info['project_id'];
		//$product_id = $device_info['product_id'];
		//$device_name = $device_info['device_name'];
        if (OnenetService::sendRebootCmd($project_id, $product_id, $device_name)) {
            LogService::write('OneNET平台管理', '发送重启命令成功');
            $this->success('发送重启命令成功!', '');
        }
        $this->error('发送重启命令失败, 请稍候再!');
    }

    /**
     * 同步OneNet平台设备
     */
    public function sync()
    {
		@ignore_user_abort(true);    //关掉浏览器，PHP脚本也可以继续执行.
		@set_time_limit(0);          // 通过set_time_limit(0)可以让程序无限制的执行下去
		Db::execute('truncate table `onenet_product`');
        //Db::name($this->table)->where('1=1')->delete();
        if (OnenetService::syncAllProducts()) {
			Db::execute('truncate table `onenet_device`');
			//Db::name($this->product_table)->where('1=1')->delete();
			if(OnenetService::syncAllDevices()){
				LogService::write('OneNET平台管理', '同步OneNET平台全部设备成功');
			}else{
				LogService::write('OneNET平台管理', '同步OneNET平台全部设备失败');
			}
            LogService::write('OneNET平台管理', '同步OneNET平台全部产品成功');
            $this->success('同步获取全部产品及设备数据成功!', '');
        }
		LogService::write('OneNET平台管理', '同步OneNET平台全部产品失败');
        $this->error('同步获取产品失败, 请稍候再试!');
    }
}
