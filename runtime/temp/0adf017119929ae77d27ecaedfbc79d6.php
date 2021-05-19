<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:93:"/www/admin/onenet.zhinengyingjian.work_80/wwwroot/application/admin/view/onenetmsg.index.html";i:1615168485;s:91:"/www/admin/onenet.zhinengyingjian.work_80/wwwroot/application/extra/view/admin.content.html";i:1615190744;}*/ ?>
<div class="ibox">
    
    <?php if(isset($title)): ?>
    <div class="ibox-title">
        <h5><?php echo $title; ?></h5>
        
<div class="nowrap pull-right" style="margin-top:10px">
    <button data-update data-field='delete' data-action='<?php echo url("$classuri/del"); ?>'
            class='layui-btn layui-btn-small layui-btn-danger'><i class='fa fa-remove'></i> 删除消息
    </button>
</div>

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
        

<!-- 表单搜索 开始 -->
<form class="animated form-search" action="__SELF__" onsubmit="return false" method="get">

    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                <input type="text" name="project_id" value="<?php echo (\think\Request::instance()->get('project_id') ?: ''); ?>" placeholder="项目id" class="input-sm form-control">
            </div>
        </div>

        <div class="col-xs-3">
            <div class="form-group">
                <!--<input type="text" name="product_id" value="<?php echo (\think\Request::instance()->get('product_id') ?: ''); ?>" placeholder="产品id" class="input-sm form-control">-->
                <select name='product_id' class='input-sm form-control'>
                    <option value=''> - 产品id - </option>
                    <?php foreach($product_ids as $product_id): if($product_id===\think\Request::instance()->get('product_id')): ?>
                    <option selected="selected" value='<?php echo $product_id; ?>'><?php echo $product_id; ?></option>
                    <?php else: ?>
                    <option value='<?php echo $product_id; ?>'><?php echo $product_id; ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-xs-3">
            <div class="form-group">
                <input type="text" name="device_name" value="<?php echo (\think\Request::instance()->get('device_name') ?: ''); ?>" placeholder="设备名称" class="input-sm form-control">
            </div>
        </div>

        <div class="col-xs-1">
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-white"><i class="fa fa-search"></i> 搜索</button>
            </div>
        </div>
    </div>
</form>
<!-- 表单搜索 结束 -->

<form onsubmit="return false;" data-auto="" method="POST">
    <input type="hidden" value="resort" name="action"/>
    <table class="table table-hover">
        <thead>
            <tr>
                <th class='list-table-check-td'>
                    <input data-none-auto="" data-check-target='.list-check-box' type='checkbox'/>
                </th>
                <th class='text-left'>项目id</th>
                <th class='text-left'>产品id</th>
                <th class='text-left'>设备名称</th>
                <!--<th class='text-left'>消息类型</th>-->
                <!--<th class='text-left'>通知类型</th>-->
                <th class='text-left'>到达时间</th>
                <th class='text-left'>消息</th>
                <th class='text-right'>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list as $key=>$vo): ?>
            <tr>
                <td class='list-table-check-td'>
                    <input class="list-check-box" value='<?php echo $vo['id']; ?>' type='checkbox'/>
                </td>
                <td class='text-left'><?php echo (isset($vo['project_id']) && ($vo['project_id'] !== '')?$vo['project_id']:"<span style='color:#ccc'>无</span>"); ?></td>
                <td class='text-left'><?php echo (isset($vo['product_id']) && ($vo['product_id'] !== '')?$vo['product_id']:"<span style='color:#ccc'>无</span>"); ?></td>
                <td class='text-left'><?php echo (isset($vo['device_name']) && ($vo['device_name'] !== '')?$vo['device_name']:"<span style='color:#ccc'>无</span>"); ?></td>
                <!--<td class='text-left'><?php echo (isset($vo['message_type']) && ($vo['message_type'] !== '')?$vo['message_type']:"<span style='color:#ccc'>无</span>"); ?></td>-->
                <!--<td class='text-left'><?php echo (isset($vo['notify_type']) && ($vo['notify_type'] !== '')?$vo['notify_type']:"<span style='color:#ccc'>无</span>"); ?></td>-->
                <td class='text-left nowrap'><?php echo (isset($vo['create_at']) && ($vo['create_at'] !== '')?$vo['create_at']:"<span style='color:#ccc'>无</span>"); ?></td>
                <td class='text-left nowrap'><?php echo (isset($vo['data']) && ($vo['data'] !== '')?$vo['data']:"<span style='color:#ccc'>无</span>"); ?></td>
                <td class='text-right nowrap'>
                    <span class="text-explode">|</span>
                    <a data-update="<?php echo $vo['id']; ?>" data-field='delete' data-action='<?php echo url("$classuri/del"); ?>'
                       href="javascript:void(0)">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(isset($page)): ?><p><?php echo $page; ?></p><?php endif; ?>
</form>

    </div>
    
</div>
