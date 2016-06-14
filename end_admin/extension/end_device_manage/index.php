<?php
END_MODULE != 'admin' && die('Access Denied');

$page = 1;
$limit = 20;
$current_time = time();
$half_hour_ago = $current_time - 1800;
$parameters = $_GET;
$p = $parameters['p'];
$device_ids = "";
$extension = $parameters['extension'];
if ($p != 'extension' || $extension != 'end_device_manage') {
    die('Access Denied');
}
$user = $_SESSION['login_user'];
$user_name = $user['name'];
$file_allowed = $user['rights']['upload_add'];
$condition = array('where'=>'1=1');
//搜索
if (is_array($_GET['search']))
{
    foreach($_GET['search'] as $key=>$val)
    {
        $val = str_replace('*','%',$val);
        $condition['where'].= " AND `$key` LIKE '%".mysql_real_escape_string($val)."%' ";
    }
}
//排序
if ($_GET['order'] && $_GET['asc'])
    $condition['order'] = $_GET['order'].' asc';
else if ($_GET['order'])
    $condition['order'] = $_GET['order'].' desc';

$page_size = 10;
if (isset($_GET['export'])) $page_size = 100000;

//分页

$items = end_page(model('test_device_manage'), $condition, $page_size);
//$select_data_log = "select * from end_device_data_log where collect_time < '{$half_hour_ago}' order by log_id desc limit 20";
//$items =$db->get_all($select_data_log);

$list_fields = array(
    'device_id'=>array(
        'name'=>'设备码',
        'width'=>'auto',
        'type'=>'text',
        'search'=>true
    ),
    'soft_version'=>array(
        'name'=>'软件版本号',
        'width'=>'auto',
        'type'=>'text'
    ),
    'cmd'=>array(
        'name'=>'命令码',
        'width'=>'auto',
        'type'=>'text',
        'filter' => 'show_test_device_manage_cmd'
    ),
    'status'=>array(
        'name'=>'状态码',
        'width'=>'auto',
        'type'=>'text',
        'filter' => 'show_test_device_manage_status'
    ),
    'active_time'=>array(
        'name'=>'在线时间',
        'width'=>'auto',
        'type'=>'text',
        'filter' => 'show_test_device_manage_date'
    )
);
?>


    <div class="row-fluid">
        <!-- block -->
        <legend>设备参数配置</legend>
        <ul class="inline">
            <li><label class="small">甲醛K1:</label></li>
            <li><input type="text" id="hcho_k1" class="input-small" placeholder="甲醛K1" required></li>
            <li><label class="small">甲醛K2:</label></li>
            <li><input type="text" id="hcho_k2" class="input-small" placeholder="甲醛K2" required></li>
            <li><label class="small">甲醛0点:</label></li>
            <li><input type="text" id="hcho_a" class="input-small" placeholder="甲醛0点" required></li>
            <li><label class="small">PM25 K1:</label></li>
            <li><input type="text" id="pm25_k1" class="input-small" placeholder="PM25 K1" required></li>

        </ul>
        <ul class="inline">
            <li><label class="small">PM25 K2:</label></li>
            <li><input type="text" id="pm25_k2" class="input-small" placeholder="PM25 K2" required></li>
            <li><label class="small">PM25 0点:</label></li>
            <li><input type="text" id="pm25_a" class="input-small" placeholder="PM25 0点" required></li>
            <li><label class="small">PM25 基准底噪:</label></li>
            <li><input type="text" id="pm25_ref" placeholder="PM25 基准底噪" required></li>

        </ul>
        <div class="form-actions">
            <button class="btn btn-primary" id="save_device_para" type="button">保存参数</button>
        </div>

        <ul class="inline">
            <li><legend>固件更新配置</legend></li>
            <li class="offset0"></li>
            <li><label class="control-label" for="fileInput">固件更新文件:</label></li>
            <li><input class="input-file uniform_on" id="fileInput" name="file" type="file"></li>
            <li><button class="btn btn-primary" id="save_device_update_file" type="button">上传固件文件</button></li>
            <li><progress id="progressBar" value="0" max="100"></progress><span id="percentage"></span></li>

        </ul>

        <?php echo '<input type="hidden" id="admin_id" name="admin_id" value="'.$user['admin_id'].'">';?>

        <form id="device_manage" method="post" action="">

            <legend>在线设备列表</legend>
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <p class="navbar-text">在线设备列表</p>
                </div>
                <p>
                    <button class="btn" id="select_all" type="button" >全选</button>
                    <button class="btn" id="select_inverse" type="button">反选</button>
                    <button class="btn btn-primary" id="get_device_para" type="button">读取参数</button>
                    <button class="btn btn-primary" id="set_device_para" type="button">设置参数</button>
                    <button class="btn btn-primary" id="call_device" type="button">在线校准</button>
                    <button class="btn btn-primary" id="update_device" type="button">在线更新</button>

                </p>

                <input type="hidden" id="input_device_ids" name="device_ids" value="">
                <input type="hidden" id="input_action" name="action_type" value="">

                <div class="block-content collapse in">
                    <div class="span12">
                        <table class="table table-bordered" cellpadding="0" cellspacing="0" border="0" id="item_table" class="list_table" >
                            <thead>
                            <tr>
                                <th width="30px">选中</th>
                                <th order="device_id" search="device_id" width="auto">设备码</th>
                                <th search="soft_version" width="auto">软件版本号</th>
                                <th width="auto">命令码</th>
                                <th width="auto">状态码</th>
                                <th order="collect_time" search="collect_time" width="auto">在线时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($items as $item)
                            {
                                $item_id = $item['log_id'];
                                echo '<tr>';
                                echo '<td>'."\n".'<div class="table-cell" ';
                                echo ">";
                                echo '<input type="checkbox" value="'.$item['device_id'].'" admin_trigger="click">';

                                echo "</div></td>";
                                foreach($list_fields as $_key=>$field)
                                {
                                    echo '<td>'."\n".'<div class="table-cell" ';

                                    $value = $item[$_key];

                                    echo ">";


                                    //如果有filter函数
                                    if ($field['filter'])
                                    {
                                        if (!function_exists($field['filter']))
                                            $value = '<span style="color:red">filter function <strong style="font-weight:bold;">'.$field['filter'].'</strong> not found</span>';
                                        else
                                        {
                                            //如果是options字段，那么传整个数据行给filter函数
                                            if ($_key == '_options')
                                            {
                                                $value = $field['filter']($item);
                                            }
                                            //如果是select类型的字段，那么多传入options为filter的第二个参数
                                            else if ($field['options'])
                                            {
                                                $value = $field['filter']($value,$field['options']);
                                            }
                                            else
                                            {
                                                $value = $field['filter']($value);
                                            }
                                        }
                                    }
                                    else if ($field['type'] == 'select')
                                    {
                                        $value = '<script>document.write(show_select_value("'.$_key.'","'.$value.'"));</script>';
                                    }

                                    //显示数据
                                    echo $value;

                                    echo "</div></td>";
                                }
                                echo "</tr>\n";
                            }
                            ?>
                            </tbody>

                        </table>
                        <?php
                        echo pager_prev("上一页");
                        echo pager_numbers();
                        echo pager_next("下一页");
                        ?>
                    </div>
                </div>
            </div>
            <!-- /block -->
        </form>
    </div>






<script type="text/javascript">
//    $('input:checkbox').each(function() {
//        if ($(this).attr('checked') == true) {
//            alert($(this).val());
//        }
//    });

    $('#save_device_para').click(function () {
        if ($('#hcho_k1').val() == "" || $('#hcho_k2').val() == ""
            ||$('#hcho_a').val() == "" || $('#pm25_k1').val() == ""
            ||$('#pm25_k2').val() == "" || $('#pm25_a').val() == ""
            ||$('#pm25_ref').val() == "" ) {
            alert("参数不完整，请填写完整");
        }
        else {
            $.ajax({
                type: "POST",
                url: "api.php?p=saveDevicePara",
                data: {
                    hcho_k1: $('#hcho_k1').val(),
                    hcho_k2: $('#hcho_k2').val(),
                    hcho_a: $('#hcho_a').val(),
                    pm25_k1: $('#pm25_k1').val(),
                    pm25_k2: $('#pm25_k2').val(),
                    pm25_a: $('#pm25_a').val(),
                    pm25_ref: $('#pm25_ref').val()
                },
                dataType: "json",
                success:function(data) {
                    if (data.ret == 0) {
                        alert("保存成功");
                    }
                    else {
                        alert(data.msg);
                    }
                },
                error :function(data) {
                    alert(data.msg);
                }
            });
        }
    });

    $('#save_device_update_file').click(function () {
        var fileObj = document.getElementById("fileInput").files[0]; // js 获取文件对象
        var count = 0;
        if (typeof (fileObj) != "undefined"  && fileObj != null) {
            //查询是否有设备正在更新，如果返回结果大于0，则不能上传更新文件。
            $.ajax({
                type: "POST",
                url: "api.php?p=isDeviceUpdating",
                data: {
                    admin_id:$('#admin_id').val()
                },
                dataType: "json",
                success:function(data) {
                    if (data.ret == 0) {
                        count = data.msg.device_updating_count;
                        if (count > 0) {
                            alert(data.msg.tip);
                        }
                        else {
                            //没有设备在更新
                            var formData = new FormData();
                            formData.append("admin_id",$('#admin_id').val());
                            formData.append("file",fileObj);

                            // XMLHttpRequest 对象
                            var xhr = new XMLHttpRequest();

                            xhr.open("post", "api.php?p=saveDeviceUpdateFile", true);

                            xhr.onload = function () {
                                var data = eval("(" + xhr.responseText +")");
                                if (data.ret == 0) {
                                    alert("上传成功");
                                }
                                else {
                                    alert(data.msg);
                                }

                            };
                            xhr.upload.addEventListener("progress", progressFunction, false);//
                            xhr.send(formData);
                        }
                    }
                    else {
                        alert(data.msg);
                    }
                },
                error :function(data) {
                    alert(data.msg);
                }
            });




        }
        else {
            alert("请选择设备更新文件");
        }


//        $.ajax({
//            type: "POST",
//            url: "api.php?p=saveDeviceUpdateFile",
//            data: formData,
//            dataType: "json",
//            success:function(data) {
//                if (data.ret == 0) {
//                    alert("上传成功");
//                }
//                else {
//                    alert(data.msg);
//                }
//            },
//            error :function(data) {
//                alert(data.msg);
//            }
//        });


    });

    function progressFunction(evt) {
        var progressBar = document.getElementById("progressBar");
        var percentageDiv = document.getElementById("percentage");
        if (evt.lengthComputable) {
            progressBar.max = evt.total;
            progressBar.value = evt.loaded;
            percentageDiv.innerHTML = Math.round(evt.loaded / evt.total * 100) + "%";
        }
    }

    $('#get_device_para').click(function () {
                var device_str ="";
                $('input:checkbox').each(function() {
                    if ($(this).attr('checked') == true) {
                        device_str += $(this).val() + ",";
                    }
                });
                $('#input_device_ids').attr('value',device_str);
                $('#input_action').attr('value','get_device_para');
                if (device_str == "") {
                    alert("请选择要操作的设备");
                }
                else {
                    $('#device_manage').attr('action',"api.php?p=device_manage");
                    $.ajax({
                        type: "POST",
                        url: "api.php?p=device_manage",
                        data: {
                            admin_id: $('#admin_id').val(),
                            device_ids: device_str,
                            action_type: 'get_device_para'
                        },
                        dataType: "json",
                        success:function(data) {
                            if (data.ret == 0) {
                                alert(data.msg.message);
                            }
                            else {
                                alert(data.msg);
                            }
                        },
                        error :function(data) {
                            alert(data.msg);
                        }
                    });
                }


    });

    $('#set_device_para').click(function () {
        var device_str ="";
        $('input:checkbox').each(function() {
            if ($(this).attr('checked') == true) {
                device_str += $(this).val() + ",";
            }
        });
        $('#input_device_ids').attr('value',device_str);
        $('#input_action').attr('value','set_device_para');
        if (device_str == "") {
            alert("请选择要操作的设备");
        }
        else {
            $('#device_manage').attr('action',"api.php?p=device_manage");
            $.ajax({
                type: "POST",
                url: "api.php?p=device_manage",
                data: {
                    admin_id: $('#admin_id').val(),
                    device_ids: device_str,
                    action_type: 'set_device_para'
                },
                dataType: "json",
                success:function(data) {
                    if (data.ret == 0) {
                        alert(data.msg.message);
                    }
                    else {
                        alert(data.msg);
                    }
                },
                error :function(data) {
                    alert(data.msg);
                }
            });
        }

    });

    $('#call_device').click(function () {
        var device_str ="";
        $('input:checkbox').each(function() {
            if ($(this).attr('checked') == true) {
                device_str += $(this).val() + ",";
            }
        });
        $('#input_device_ids').attr('value',device_str);
        $('#input_action').attr('value','call_device');
        if (device_str == "") {
            alert("请选择要操作的设备");
        }
        else {
            $('#device_manage').attr('action',"api.php?p=device_manage");
            $.ajax({
                type: "POST",
                url: "api.php?p=device_manage",
                data: {
                    admin_id: $('#admin_id').val(),
                    device_ids: device_str,
                    action_type: 'call_device'
                },
                dataType: "json",
                success:function(data) {
                    if (data.ret == 0) {
                        alert(data.msg.message);
                    }
                    else {
                        alert(data.msg);
                    }
                },
                error :function(data) {
                    alert(data.msg);
                }
            });
        }

    });

    $('#update_device').click(function () {
        var device_str ="";
        $('input:checkbox').each(function() {
            if ($(this).attr('checked') == true) {
                device_str += $(this).val() + ",";
            }
        });
        $('#input_device_ids').attr('value',device_str);
        $('#input_action').attr('value','update_device');
        if (device_str == "") {
            alert("请选择要操作的设备");
        }
        else {
            $('#device_manage').attr('action',"api.php?p=device_manage");
            $.ajax({
                type: "POST",
                url: "api.php?p=device_manage",
                data: {
                    admin_id: $('#admin_id').val(),
                    device_ids: device_str,
                    action_type: 'update_device'
                },
                dataType: "json",
                success:function(data) {
                    if (data.ret == 0) {
                        alert(data.msg.message);
                    }
                    else {
                        alert(data.msg);
                    }
                },
                error :function(data) {
                    alert(data.msg);
                }
            });
        }

    });

//    $('#link_get_device_para').click(function () {
//            var device_str ="";
//            $('input:checkbox').each(function() {
//                if ($(this).attr('checked') == true) {
//                    device_str += $(this).val() + ",";
//                }
//            });
//            $('#link_get_device_para').attr('href',"admin.php?p=device_manage&action=get_para&device_ids="+ device_str);
//    });

    $('#select_all')
        .click(function () {
            $('input:checkbox').each(function() {
                $(this).attr('checked',true);
            });
        });
    $('#select_inverse')
        .click(function () {
            $('input:checkbox').each(function() {
                if ($(this).attr('checked') == true) {
                    $(this).attr('checked',false);
                }
                else {
                    $(this).attr('checked',true);
                }
            });
        });


</script>

<?php

function show_test_device_manage_date($t)
{
    return date('Y-m-d H:i:s',$t);
}

function show_test_device_manage_cmd($cmd)
{
    $result = "";
    switch($cmd) {
       case '110':
           $result = "设备上传数据";
           break;
       case '111':
           $result = "开始校准";
           break;
       case '112':
           $result = "设备重发数据";
           break;
       case '120':
           $result = "获取设备参数";
           break;
       case '121':
           $result = "修改设备参数";
           break;
       case '122':
           $result = "设备固件更新";
           break;
    }
    return $result;
}

function show_test_device_manage_status($status)
{
    $result = "";
    switch($status) {
        case '210':
            $result = "正常测试";
            break;
        case '211':
            $result = "校准中";
            break;
        case '212':
            $result = "校准完成";
            break;
        case '221':
            $result = "参数修改完成";
            break;
        case '222':
            $result = "参数获取完成";
            break;
        case '223':
            $result = "校时请求";
            break;
        case '231':
            $result = "固件更新中";
            break;
        case '232':
            $result = "固件更新完毕";
            break;
    }
    return $result;
}



//function end_show_view_button($id,$s=LANG_VIEW)
//{
//    echo ' <a href="admin.php?p=item&action=view_item&category_id='.END_ADMIN_CATEGORY_ID.'&item_id='.$id.'">'.$s.'</a> ';
//}
//function end_show_edit_button($id,$s=LANG_EDIT)
//{
//    echo ' <a href="admin.php?p=item&action=edit_item&category_id='.END_ADMIN_CATEGORY_ID.'&item_id='.$id.'">'.$s.'</a> ';
//}
//function end_show_delete_button($id,$s = LANG_DELETE)
//{
//    echo ' <a href="javascript:;//'.$id.'" onclick="delete_item(\''.$id.'\',this)">'.$s.'</a> ';
//}

?>
