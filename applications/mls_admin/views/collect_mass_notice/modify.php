<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $title?></h1>
                </div>
            </div>
            <?php if(''==$modifyResult){; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type='hidden' name='submit_flag' value='modify'/>
                                                <label>
                                                    标题:&nbsp&nbsp&nbsp&nbsp<input type="search" name="title" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $notice_msg['title']?>">
                                                </label>
                                                <label>
                                                    弹窗:&nbsp
                                                    有 <input <?php if ($notice_msg['show'] == 1) {echo "checked='checked'";}?> type="radio" name="show" value="1"> 无 <input <?php if ($notice_msg['show'] == 0) {echo "checked='checked'";}?> type="radio" name="show" value="0">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    内容:&nbsp&nbsp&nbsp&nbsp<textarea name="content" class="form-control" aria-controls="dataTables-example" rows="7" cols="52" style='resize:none'><?php echo $notice_msg['content']?></textarea>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    城市:&nbsp&nbsp&nbsp&nbsp<input id="checkAll" type="checkbox"> 全选
                                                </label><br>
                                                <label style="width: 412px;margin-left: 4%;line-height: 20px;">
                                                    <?php foreach($city as $val){?>
                                                    <input <?php if (strstr($notice_msg['city'],$val['cityname'])) {echo "checked='checked'";}?> class="city" type="checkbox" name="city[]" value="<?php echo $val['cityname']?>"> <?php echo $val['cityname']?>
                                                    <?php }?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                            </div>
                                        </div>
                                        <?php if(!empty($notice_error)){?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $notice_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="提交">
                                                <input class="btn btn-primary" type="button" value="取消" onclick="javascript:location.href='/collect_mass_notice/'">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }else if(0===$modifyResult){
                echo "<script>alert('更新失败');</script>";
                echo "<script>location.href='".MLS_ADMIN_URL."/collect_mass_notice/index'</script>";
            }else{
                echo "<script>alert('更新成功')</script>";
                echo "<script>location.href='".MLS_ADMIN_URL."/collect_mass_notice/index'</script>";
            }?>
        </div>
    </div>
<script type="text/javascript">
    $(function() {
        $("#checkAll").click(function() {
             $('.city').attr("checked",this.checked);
         });
         var city = $(".city");
         city.click(function(){
             $(this).attr("checked",this.checked);
             $("#checkAll").attr("checked",city.length == $(".city:checked").length ? true : false);
         });
     });
</script>
<?php require APPPATH.'views/footer.php'; ?>

