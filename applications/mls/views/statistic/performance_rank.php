<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>无标题文档</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/guest_disk.css " rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/myStyle.css"/>
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body >
<div class="tab_box" id="js_tab_box">
    <a href="#" class="link link_on"><span class="iconfont">&#xe630;</span>我的报表</a>
    <a href="#" class="link"><span class="iconfont">&#xe631;</span>全程统计</a>
</div>
<div id="js_search_box" class="shop_tab_title  scr_clear">
    <a href="/statistic/msg_entering_statistic" class="link">信息录入统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/lease_statistic" class="link">租赁成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/buy_sell_statistic" class="link">买卖成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="#" class="link">佣金参数统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/broker_action" class="link">员工行为统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_rank" class="link link_on">业绩排行统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_detail" class="link">业绩明细统计<span class="iconfont hide">&#xe607;</span></a>
</div>
<div class="search_box clearfix" id="js_search_box">
    <form action="/statistic/performance_rank" method="post" id="search_form">
    <div class="fg_box">
        <p class="fg fg_tex">分店：</p>
        <div class="fg fg-edit">
            <select class="select" id="agency_id" name="agency_id">
                <option value="0">全部</option>
                <?php foreach($agency_conf as $k=>$v) { ?>
                    <option value="<?php echo $v['id']; ?>" <?php if($post_param['agency_id'] == $v['id']){echo "selected";} ?>><?php echo $v['name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">员工：</p>
        <div class="fg fg-edit">
            <select class="select" id="broker_id" name="broker_id">
                <option value="0">全部</option>
                <?php foreach ($brokers as $k=>$v) { ?>
                    <option <?php if($v['broker_id'] == $post_param['broker_id']) { echo "selected";}?>><?php echo $v['truename']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">开始时间：</p>
        <div class="fg fg-edit">
            <input type="text" name="start_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['start_time']; ?>">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">结束时间：</p>
        <div class="fg fg-edit">
            <input type="text" name="end_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['end_time']; ?>">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="search_form.submit();" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset" onclick="search_form.reset();">重置</a> </div>
    </div>
</div>
<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c11">排名</td>
                <td class="c12">所属门店</td>
                <td class="c11">员工</td>
                <!--<td class="c11">总业绩</td>
                <td class="c11">买卖业绩</td>
                <td class="c11">租赁业绩</td>-->
                <td class="c11">买卖提成</td>
                <td class="c11">租赁提成</td>
                <td class="c11">总提成</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php foreach($performances as $k=>$v) { ?>
                <tr>
                    <td class="c11"><?php echo $v['rank'];?></td>
                    <td class="c12"><?php echo $v['name'];?></td>
                    <td class="c11"><?php echo $v['truename'];?></td>
                    <td class="c11"><?php echo $v['sell_price'];?></td>
                    <td class="c11"><?php echo $v['rent_price'];?></td>
                    <td class="c11"><?php echo $v['all_price'];?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn" style="margin-top: 15px;">
    <div class="get_page"> <!--<span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to">跳转</a>
        <div id="js_f_input" class="f_input hide"><span class="tex">跳转到第</span>
            <input class="input" type="text">
            <span  class="tex">页</span><a class="b_link" href="javascript:void(0)">确定</a>
        </div>-->
        <?php echo $page_list; ?>
    </div>
</div>
</form>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,scrollPic.js "></script>
<script>
    $(function(){
        /** 门店---经纪人联动 */
        $("#agency_id").change(function(){
            //先清空经纪人下拉列表
            $("#broker_id").html("<option value='0'>全部</option>");
            //AJAX请求数据并添加到相应位置
            var agency_id = $("select[name='agency_id']").val();
            $.post('/contract/get_broker_by_agency',{agency_id:agency_id},function(data){
                $.each(data,function(i,item){
                    $("#broker_id").append("<option value='"+ item.broker_id +"'>"+ item.truename +"</option>");
                });
            },'json');
        });
    });
</script>
</body>
</html>
