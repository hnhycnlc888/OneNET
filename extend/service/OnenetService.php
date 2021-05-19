<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace service;

use think\Db;
use think\Log;
use util\onenet\SafetyAuth;

/**
 * 中移OneNet服务
 * Class OnenetService
 * @package service
 * @author liuchao <249757247@qq.com>
 * @date 2021/03/03 15:39
 */
class OnenetService
{

	//OneNet平台工程id
	private static $PROJECT_ID = 'VZ8n3z';
	//OneNet平台工程id
	private static $PROJECT_NAME = 'bbb';
	//OneNet平台用户Accesskey
	private static $ACCESS_KEY = 'v45HFHcouipOLWrT+MdkWR7pfV1fZOs8SdA0crP3Te3UsHWXHr30pTDqAOnJ7yGZmTt2A5j3la6Um/lsqhyXRA==';
	//OneNet平台用户id
	private static $USER_ID = 'userid/223931';
	//OneNet平台接口验证authorization有效时间
	private static $EXPIRE_TIME = 1800;
	//OneNet平台接口请求超时时间
	private static $TIMEOUT = 30;

    /**
     * 发送设备重启命令
     * @param string $project_id OneNet平台项目id
     * @param string $product_id OneNet平台产品id
     * @param string $device_name OneNet平台设备名称
     * @return boolean
     */
    public static function sendRebootCmd( $project_id, $product_id, $device_name )
    {
        $obj = new SafetyAuth(self::$ACCESS_KEY, strtotime('now') + self::$EXPIRE_TIME, self::$USER_ID);
        $authorization = $obj->makeToken();
		$url = 'http://openapi.heclouds.com/application?action=CallService&version=1';
        $header = ['authorization: ' . $authorization];
		$params = [ 'project_id' => $project_id, 'product_id' => $product_id, 'device_name' => $device_name, 'identifier' => 'cmd', 'params' => [ 'InData' => 'reboot' ] ];
        $ret = HttpService::post($url, http_build_query($params), self::$TIMEOUT, $header);
		//记录接口请求日志
		$sql = "insert into onenet_cmd_log (`project_id`, `product_id`, `device_name`, `request_url`, `request_param`, `request_result`) values (:project_id, :product_id, :device_name, :request_url, :request_param, :request_result)";
		Db::execute($sql, ['project_id' => $project_id, 'product_id' => $product_id, 'device_name' => $device_name, 'request_url' => $url, 'request_param' => json_encode($params, 320), 'request_result' => $ret]);

		$ret = json_decode($ret, true);
		if($ret['success'] && $ret['data']['OutData']){
			return true;
		}
		return false;
    }

    /**
     * 获取OneNet平台分页设备列表
	 * condition 查询条件
     * @return array
     */
    public static function getPageDevices($condition)
    {
		$product_id = isset($condition['product_id']) ? $condition['product_id'] : null;
		$device_name = isset($condition['device_name']) ? $condition['device_name'] : null;
		$page = isset($condition['page']) ? $condition['page'] : 1;
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$project_id = self::$PROJECT_ID;
        $obj = new SafetyAuth(self::$ACCESS_KEY, strtotime('now') + self::$EXPIRE_TIME, self::$USER_ID);
        $authorization = $obj->makeToken();
		$url = 'http://openapi.heclouds.com/application';
        $header = ['authorization: ' . $authorization];
		$params = ['action' => 'QueryDeviceList', 'version' => 1, 'project_id' => $project_id, 'offset' => $offset, 'limit' => $limit];
		if($product_id){
			$params['product_id'] = $product_id;
		}
		if($device_name){
			$params['device_name'] = $device_name;
		}
        $ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
		$ret = json_decode($ret, true);
		foreach($ret['data']['list'] as $k => $v){
			$ret['data']['list'][$k]['project_id'] = self::$PROJECT_ID;
		}
		return $ret;
	}

    /**
     * 同步OneNet平台设备到本地库
     * @return boolean
     */
    public static function syncAllDevices()
    {
		$project_id = self::$PROJECT_ID;
        $obj = new SafetyAuth(self::$ACCESS_KEY, strtotime('now') + self::$EXPIRE_TIME, self::$USER_ID);
        $authorization = $obj->makeToken();
		$url = 'http://openapi.heclouds.com/application';
        $header = ['authorization: ' . $authorization];
		$params = ['action' => 'QueryDeviceList', 'version' => 1, 'project_id' => $project_id];
        $ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
		$ret = json_decode($ret, true);
		$devices_list = [];
		if($ret['success']){
			if($ret['data']['meta']['total'] == 0){
				return false;
			}else{
				$devices_list = $ret['data']['list'];
				$total = $ret['data']['meta']['total'];
				$limit = $ret['data']['meta']['limit'];
				$offset = $ret['data']['meta']['offset'];
				$pages = ceil($total / $limit);
				//多于1页则分页获取数据
				if($pages > 1){
					foreach (range(2, $pages) as $curr_page) {
						$offset = ($curr_page - 1) * $limit;
						$params = ['action' => 'QueryDeviceList', 'version' => 1, 'project_id' => $project_id, 'offset' => $offset];
						$ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
						$ret = json_decode($ret, true);
						if($ret['success']){
							$devices_list = array_merge($devices_list, $ret['data']['list']);
						}
					}
				}
				//设备插入本地库
				$sql = "INSERT INTO `onenet_device` (`project_id`, `project_name`, `product_id`, `product_name`, `device_name`, `created_time`, `status`, `lat`, `lon`) VALUES ";
				foreach($devices_list as $k => $item){
					$itemStr = '( ';
					$itemStr .= sprintf("'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'", $project_id, self::$PROJECT_NAME, $item['product_id'], $item['product_name'], $item['device_name'], $item['created_time'], $item['status'], $item['lat'], $item['lon']);
					$itemStr .= '),';
					$sql .= $itemStr;
				}
				$sql = rtrim($sql, ',');
				$sql .= ';';
				$affect_rows = Db::execute($sql);
				if($affect_rows){
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

    /**
     * 获取OneNet平台指定项目下全部产品列表
     * @return array
     */
    public static function getAllProducts()
    {
		$project_id = self::$PROJECT_ID;
        $obj = new SafetyAuth(self::$ACCESS_KEY, strtotime('now') + self::$EXPIRE_TIME, self::$USER_ID);
        $authorization = $obj->makeToken();
		$url = 'http://openapi.heclouds.com/application';
        $header = ['authorization: ' . $authorization];
		$params = ['action' => 'QueryProductList', 'version' => 1, 'project_id' => $project_id];
        $ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
		$ret = json_decode($ret, true);
		$products_list = [];
		if($ret['success']){
			if($ret['data']['meta']['total'] == 0){
				return [];
			}else{
				$products_list = $ret['data']['list'];
				$total = $ret['data']['meta']['total'];
				$limit = $ret['data']['meta']['limit'];
				$offset = $ret['data']['meta']['offset'];
				$pages = ceil($total / $limit);
				//多于1页则分页获取数据
				if($pages > 1){
					foreach (range(2, $pages) as $curr_page) {
						$offset = ($curr_page - 1) * $limit;
						$params = ['action' => 'QueryProductList', 'version' => 1, 'project_id' => $project_id, 'offset' => $offset];
						$ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
						$ret = json_decode($ret, true);
						if($ret['success']){
							$products_list = array_merge($products_list, $ret['data']['list']);
						}
					}
				}
				return $products_list;
			}
		}else{
			return [];
		}
	}

    /**
     * 同步OneNet平台产品到本地库
     * @return boolean
     */
    public static function syncAllProducts()
    {
		$project_id = self::$PROJECT_ID;
        $obj = new SafetyAuth(self::$ACCESS_KEY, strtotime('now') + self::$EXPIRE_TIME, self::$USER_ID);
        $authorization = $obj->makeToken();
		$url = 'http://openapi.heclouds.com/application';
        $header = ['authorization: ' . $authorization];
		$params = ['action' => 'QueryProductList', 'version' => 1, 'project_id' => $project_id];
        $ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
		$ret = json_decode($ret, true);
		$products_list = [];
		if($ret['success']){
			if($ret['data']['meta']['total'] == 0){
				return false;
			}else{
				$products_list = $ret['data']['list'];
				$total = $ret['data']['meta']['total'];
				$limit = $ret['data']['meta']['limit'];
				$offset = $ret['data']['meta']['offset'];
				$pages = ceil($total / $limit);
				//多于1页则分页获取数据
				if($pages > 1){
					foreach (range(2, $pages) as $curr_page) {
						$offset = ($curr_page - 1) * $limit;
						$params = ['action' => 'QueryProductList', 'version' => 1, 'project_id' => $project_id, 'offset' => $offset];
						$ret = HttpService::get($url, $params, self::$TIMEOUT, $header);
						$ret = json_decode($ret, true);
						if($ret['success']){
							$products_list = array_merge($products_list, $ret['data']['list']);
						}
					}
				}
				//设备插入本地库
				$sql = "INSERT INTO `onenet_product` (`project_id`, `product_id`, `name`, `desc`, `device_number`) VALUES ";
				foreach($products_list as $k => $item){
					$itemStr = '( ';
					$itemStr .= sprintf("'%s', '%s', '%s', '%s', '%s'", $project_id, $item['product_id'], $item['name'], $item['desc'], $item['device_number']);
					$itemStr .= '),';
					$sql .= $itemStr;
				}
				$sql = rtrim($sql, ',');
				$sql .= ';';
				$affect_rows = Db::execute($sql);
				if($affect_rows){
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
}
