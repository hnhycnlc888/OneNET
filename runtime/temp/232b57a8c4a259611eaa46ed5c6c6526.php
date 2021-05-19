<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:88:"/www/admin/onenet.zhinengyingjian.work_80/wwwroot/application/admin/view/index.main.html";i:1614845180;s:91:"/www/admin/onenet.zhinengyingjian.work_80/wwwroot/application/extra/view/admin.content.html";i:1615190744;}*/ ?>
<div class="ibox">
    
    <?php if(isset($title)): ?>
    <div class="ibox-title">
        <h5><?php echo $title; ?></h5>
        
    </div>
    <?php endif; ?>
    <div class="ibox-content fadeInUp animated">
        <?php if(isset($alert)): ?>
        <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible" role="alert" style="border-radius:0">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?php if(isset($alert['title'])): ?><p style="font-size:18px;padding-bottom:10px"><?php echo $alert['title']; ?></p><?php endif; if(isset($alert['content'])): ?><p style="font-size:14px"><?php echo $alert['content']; ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        
<div class="row">
    <div class="col-lg-6">
        <table class="layui-box layui-table" lay-even lay-skin="line">
            <colgroup>
                <col width="40%">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center" colspan="2">系统信息</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>产品版本</td>
                    <td><?php echo sysconf('app_version'); ?></td>
                </tr>
                <tr>
                    <td>ThinkPHP 版本</td>
                    <td><?php echo THINK_VERSION; ?></td>
                </tr>
                <tr>
                    <td>服务器操作系统</td>
                    <td><?php echo php_uname('s'); ?></td>
                </tr>
                <tr>
                    <td>WEB运行环境</td>
                    <td><?php echo php_sapi_name(); ?></td>
                </tr>
                <!--<tr>
                    <td>MySQL数据库版本</td>
                    <td><?php echo $mysql_ver; ?></td>
                </tr>
                <tr>
                    <td>运行PHP版本</td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td>上传大小限制</td>
                    <td><?php echo ini_get('upload_max_filesize'); ?></td>
                </tr>
                <tr>
                    <td>POST大小限制</td>
                    <td><?php echo ini_get('post_max_size'); ?></td>
                </tr>-->
            </tbody>
        </table>
    </div>
    <div class="col-lg-6">
        <table class="layui-box layui-table" lay-even lay-skin="line">
            <colgroup>
                <col width="40%">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center" colspan="2">产品团队</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>产品名称</td>
                    <td>Telpo后台管理框架</td>
                </tr>
                <tr>
                    <td>产品研发团队</td>
                    <td>广东天波信息技术股份有限公司 -> 智能硬件事业部 -> 软件室</td>
                </tr>
                <!--<tr>
                    <td>产品体验</td>
                    <td>
                        <a target="_blank" href="http://onenet.zhinengyingjian.work/index.php">
                            http://onenet.zhinengyingjian.work/index.php
                        </a>
                    </td>
                </tr>-->
                <!--<tr>
                    <td>官方QQ群</td>
                    <td>
                        <a target="_blank" href="http://shang.qq.com/wpa/qunwpa?idkey=ae25cf789dafbef62e50a980ffc31242f150bc61a61164458216dd98c411832a">
                            <img src="//pub.idqqimg.com/wpa/images/group.png" style="height:18px;width:auto" alt="PHP微信开发群 (SDK)">
                        </a>
                    </td>
                </tr>-->
                <!--<tr>
                    <td>BUG反馈</td>
                    <td>
						BUG反馈
                    </td>
                </tr>-->
                <tr>
                    <td>项目地址</td>
                    <td>
                        <a target="_blank" href="https://onenet.zhinengyingjian.work/index.php">
                            https://onenet.zhinengyingjian.work/index.php
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>公司官网</td>
                    <td>
                        <a target="_blank" href="http://www.telpo.com/">
                            http://www.telpo.com/
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>公司地址</td>
                    <td>
                        广东省佛山市南海区桂城街道深海路17号瀚天科技城A区5号楼五楼
                    </td>
                </tr>
                <tr>
                    <td>联系电话</td>
                    <td>
                        0757-86337898
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    </div>
    
</div>
