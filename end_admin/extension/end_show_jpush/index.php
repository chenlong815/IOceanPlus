<?php
END_MODULE != 'admin' && die('Access Denied');
$do = $_GET['do'];
require_once(END_ROOT.'end_admin/library/vendor/autoload.php');

use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;


?>

<h3>极光推送控制台</h3>
<form method="post" name="form1" action="<?php echo $url;?>&do=push">
    <table cellspacing="8">
        <tr>
            <td>推送标题：</td>
            <td><input type="text" name="title" value="<?php echo $_POST['title'];?>" required="true" style="height:20px;width:450px"></td>
        </tr>
        <tr>
            <td>推送对象：</td>
            <td><input type='text' name='platform_text' value="<?php echo $_POST['platform_text'];?>" style="display: none"">
                <input type='checkbox' name='Platform' style="height:15px;width:15px" value="1" onclick="setPlatform(1)">IOS开发环境 &nbsp;&nbsp;
                <input type='checkbox' name='Platform' style="height:15px;width:15px" value="2" onclick="setPlatform(2)">IOS生产环境 &nbsp;&nbsp;&nbsp;&nbsp;
                <input type='checkbox' style="height:15px;width:15px" name='Platform' value="4" onclick="setPlatform(4)">Android
            </td>
        </tr>
        <tr>
            <td></td>
            <td><input type='radio' name='Audience' checked="true" style="height:15px;width:15px" value="1" onclick="showText(1)">广播(所有人) &nbsp;&nbsp;
                <input type='radio' name='Audience' style="height:15px;width:15px" value="2" onclick="showText(2)">设备标签(Tag) &nbsp;&nbsp;
                <input type='radio' name='Audience' style="height:15px;width:15px" value="3" onclick="showText(3)">设备别名(Alias) &nbsp;&nbsp;
                <input type='radio' name='Audience' style="height:15px;width:15px" value="4" onclick="showText(4)">Registration ID &nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td></td>
            <td><input type="text" name="audience_text" value="<?php echo $_POST['audience_text'];?>" style="margin-top:5px;height:20px;width:450px;display:none"></td>
        </tr>
        <tr>
            <td>内&nbsp;&nbsp;&nbsp;&nbsp;容：</td>
            <td><textarea name="content" style="margin-top:5px;height:100px;width:450px"><?php echo $_POST['content'];?></textarea></td>
        </tr>
    </table>

    <div id="btns">
        <table>
            <tr>
                <td>
                    <button class="btn" style="width:200px;height:50px;font-size:24px">发&nbsp;&nbsp;&nbsp;&nbsp;送</button>
                </td>
            </tr>
        </table>
    </div>
</form>
<script>

    function setPlatform(value) {
        if (value == 1) {
            document.form1.platform_text.value += 1;
        }
        else if (value == 2) {
            document.form1.platform_text.value += 2;
        }
        else if (value == 4) {
            document.form1.platform_text.value += 4;
        }
    }

    function showText(value) {
        if (value == 1) {
            document.form1.audience_text.style.display = "none";
            document.form1.audience_text.style.placeholder = "";
            document.form1.audience_text.required = "false";
        }
        else if (value == 2) {
            document.form1.audience_text.style.display = "block";
            document.form1.audience_text.placeholder = "请输入Tag值";
            document.form1.audience_text.required = "true";
        }
        else if (value == 3) {
            document.form1.audience_text.style.display = "block";
            document.form1.audience_text.placeholder = "请输入设备别名";
            document.form1.audience_text.required = "true";
        }
        else if (value == 4) {
            document.form1.audience_text.style.display = "block";
            document.form1.audience_text.placeholder = "请输入Registration ID";
            document.form1.audience_text.required = "true";
        }
    }
</script>

<?php
if (!$do) //首页
{

}
elseif ($do == 'push')
{
    $data = $_POST;
    $title = $data['title'];
    $content = $data['content'];
    $platform_text = $data['platform_text'];
    $platform = "";
    if ((strpos($platform_text,"1") > 0 || strpos($platform_text,"2") > 0) && strpos($platform_text,"4") > 0) {
        $platform = M\all;
    }
    else if (strpos($platform_text,"4") > 0) {
        $platform = M\platform('android');
    }
    else if (strpos($platform_text,"1") > 0 || strpos($platform_text,"2") > 0){
        $platform = M\platform('ios');
    }
    else {
        $platform = M\all;
    }

    $audience_value = $data['Audience'];
    $audience_text = $data['audience_text'];

    $audience = M\all;

    if ($audience_value = "1") {
        $audience = M\all;
    }
    else if ($audience_value = "2"){
        $audience = M\audience(M\tag(array($audience_text)));
    }
    else if ($audience_value = "3"){
        $audience = M\audience(M\alias(array($audience_text)));
    }
    else if ($audience_value = "4"){
        $audience = M\audience(M\registration_id(array($audience_text)));
    }

    $client = new JPushClient(JPUSH_APP_KEY, JPUSH_MASTER_SECRET);
    try {
        $result = $client->push()
            ->setPlatform($platform)
            ->setAudience($audience)
            //->setNotification(M\notification($data['title']))
            ->setMessage(M\message($content, $data['title'], null, array('key'=>'value')))
            ->setOptions(M\options(123456, null, null, false, 0))
            ->printJSON()
            ->send();
        echo 'Push Success.' . $br;
        echo 'sendno : ' . $result->sendno . $br;
        echo 'msg_id : ' .$result->msg_id . $br;
        echo 'Response JSON : ' . $result->json . $br;
    }
    catch (APIRequestException $e) {
        echo 'Push Fail.' . $br;
        echo 'Http Code : ' . $e->httpCode . $br;
        echo 'code : ' . $e->code . $br;
        echo 'Error Message : ' . $e->message . $br;
        echo 'Response JSON : ' . $e->json . $br;
        echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
        echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
        echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
    } catch (APIConnectionException $e) {
        echo 'Push Fail: ' . $br;
        echo 'Error Message: ' . $e->getMessage() . $br;
        //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
        echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
    }

}?>