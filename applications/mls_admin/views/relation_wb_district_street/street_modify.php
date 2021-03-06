<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<style>
    #input{width:140px}
    #r_s_popUP {position: absolute;top: -40px; left:200px;display: none}
    #r_s_popUP .replace_stores_popUp {position: relative;width: 350px; padding: 10px; border: 1px solid #6aa8e6; background: #fff; }
    .replace_stores_popUp .upgou { display: block; width: 7px;height: 5px; background: url(<?=MLS_SOURCE_URL ?>/mls_admin/images/xiangs.png) no-repeat; position: absolute; top: -5px;left: 25px; }
    .replace_stores_popUp li { padding: 10px 0;border-bottom: 1px dashed #dadada; zoom: 1;}
</style>
<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/wuba_district_index' class="btn btn-primary">58区属列表</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/wuba_street_index' class="btn btn-primary">58板块列表</a></h1>
            </div>
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="" id="per-hover" ><img src="<?=MLS_SOURCE_URL ?>/mls_admin/images/tips.png" width="20px" height="20px" border="0"></a></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php if(''==$modifyResult){; ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">

                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" method="post" action="">
                            <input type='hidden' name='submit_flag' value='modify'/>
                            <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                <div class="row">
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            本地58区属:&nbsp&nbsp&nbsp<input type="search" id ='input' class="form-control input-sm" aria-controls="dataTables-example" value="<?=$dist_mess['district_id']?>&nbsp&nbsp&nbsp&nbsp&nbsp<?=$dist_mess['district_name']?>" readOnly>
                                        </label>&nbsp&nbsp&nbsp&nbsp
                                        <label>
                                            本地58板块:&nbsp&nbsp&nbsp<input type="search" id ='input'class="form-control input-sm" aria-controls="dataTables-example" value="<?=$street_mess['street_id']?>&nbsp&nbsp&nbsp&nbsp&nbsp<?=$street_mess['street_name']?>" readOnly>
                                        </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                        <label>
                                            58区属:&nbsp&nbsp&nbsp&nbsp<input type="search"id ='input'class="form-control input-sm" aria-controls="dataTables-example" value="<?=$dist_mess['district_id']?>&nbsp&nbsp&nbsp&nbsp&nbsp<?=$dist_mess['district_name']?>" readOnly>
                                        </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                        <label>
                                            58板块:&nbsp&nbsp&nbsp
                                            <select id="wb_street" name = 'street' class="form-control input-sm" aria-controls="dataTables-example">
                                                <option value="0">请选择...</option>
                                                 <?php if(!empty($wb_street)&&$wb_street){
                                                 foreach ($wb_street as $value) {?>
                                                    <option value="<?=$value->val;?>&<?=$value->text;?>" <?php if($value->val==$street_mess['street_id']){echo 'selected="selected"';}?> ><?=$value->val;?>&nbsp&nbsp&nbsp&nbsp&nbsp<?=$value->text;?></option>
                                                 <?php }}?>
                                            </select>
                                        </label>
                                    </div>
                                    <div id="r_s_popUP">
                                        <div class="replace_stores_popUp">
                                            <ol>
                                                <li>修改板块前，清先确认区属是否正确
                                                <li>当修改板块id值已存在，请先修改该id值的板块
                                            </ol>
                                            <i class="upgou"> </i>
                                        </div>
                                    </div>
                                </div>

                                <?php if(!empty($mess_error)){?>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <font color='red'><?php echo $mess_error; ?></font>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <input class="btn btn-primary" type="submit" value="提交">
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php }else if(0===$modifyResult){ ?>
            <div>更新失败</div>
        <?php }else{?>
            <div>更新成功</div>
        <?php }?>
    </div>
</div>
<script>
    $("#per-hover").hover(function(){
        $("#r_s_popUP").toggle();
    });
</script>
 <?php require APPPATH.'views/footer.php'; ?>

