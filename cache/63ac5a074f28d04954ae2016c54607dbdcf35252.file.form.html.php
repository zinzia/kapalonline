<?php /* Smarty version Smarty-3.0.7, created on 2015-09-29 10:21:07
         compiled from "application/views\information/member/form.html" */ ?>
<?php /*%%SmartyHeaderCode:26416560a49f3289d43-78761149%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63ac5a074f28d04954ae2016c54607dbdcf35252' => 
    array (
      0 => 'application/views\\information/member/form.html',
      1 => 1441883418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26416560a49f3289d43-78761149',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(function() {
        $(".login").jCryption({
            getKeysURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/crypt?getPublicKey=true');?>
",
            handshakeURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/crypt?handshake=true');?>
"
        });
    });
</script>
<h3><?php echo $_smarty_tpl->getVariable('member_title')->value;?>
</h3>
<?php if (!empty($_smarty_tpl->getVariable('com_user',null,true,false)->value)){?>
<div class="content">
    <img src="<?php echo $_smarty_tpl->getVariable('com_user')->value['operator_photo'];?>
" style="height: 128px; width: auto;" alt="" />
    <ul style="list-style: none;">
        <li><b><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_name'];?>
, (<?php echo $_smarty_tpl->getVariable('com_user')->value['airlines_nm'];?>
)</b></li>
        <li><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_phone'];?>
</li>
        <li><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_address'];?>
</li>
        <li><?php echo $_smarty_tpl->getVariable('com_user')->value['user_mail'];?>
</li>
    </ul>
    <p>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/welcome');?>
" style="text-decoration: none; color: #FF00BA;">Back to your private menu? Click Here!</a>
    </p>
    <div class="clear"></div>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)==''){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <table class="table-input" width="100%">
            <tbody>
                <tr>
                    <td colspan="5">
                        Gunakan fasilitas pelayanan Flight Approval Online di Direktorat Angkutan Udara untuk mempermudah dan mempercepat proses permohonan Flight Approval anda disini!
                    </td>
                </tr>
                <tr>
                    <td width="10%">User Name</td>
                    <td width="20%">
                        <input name="username" maxlength="30" type="text" autocomplete="off"/>
                    </td>
                    <td width="10%">Password</td>
                    <td width="20%">
                        <input name="pass" maxlength="30" type="password" autocomplete="off"/>
                    </td>
                    <td width="40%">
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                        <input type="text" name="captcha" size="10" maxlength="10" style="background-color:#f7ecbc;color:#000" />
                        <button class="submit-button">Login</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/forget_password');?>
"><?php echo $_smarty_tpl->getVariable('member_forget')->value;?>
</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error'){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <table class="table-input" width="100%">
            <tbody>
                <tr>
                    <td colspan="5">
                        Gunakan fasilitas pelayanan Flight Approval Online di Direktorat Angkutan Udara untuk mempermudah dan mempercepat proses permohonan Flight Approval anda disini!
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="blink-row">
                        Username dan Password anda tidak ditemukan! Hubungi administrator Direktorat Angkutan Udara jika mengalami kesulitan.
                    </td>
                </tr>
                <tr>
                    <td width="10%">User Name</td>
                    <td width="20%">
                        <input name="username" maxlength="30" type="text" autocomplete="off"/>
                    </td>
                    <td width="10%">Password</td>
                    <td width="20%">
                        <input name="pass" maxlength="30" type="password" autocomplete="off"/>
                    </td>
                    <td width="40%">
                       <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                        <input type="text" name="captcha" size="10" maxlength="10" style="background-color:#f7ecbc;color:#000" />
                        <button class="submit-button">Login</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/forget_password');?>
">Lupa password? Klik Disini!</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='locked'){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <table class="table-input" width="100%">
            <tbody>
                <tr>
                    <td colspan="5">
                        Gunakan fasilitas pelayanan Flight Approval Online di Direktorat Angkutan Udara untuk mempermudah dan mempercepat proses permohonan Flight Approval anda disini!
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="blink-row">
                        Account anda tidak bisa digunakan! Hubungi administrator Direktorat Angkutan Udara jika mengalami kesulitan.
                    </td>
                </tr>
                <tr>
                    <td width="10%">User Name</td>
                    <td width="20%">
                        <input name="username" maxlength="30" type="text" autocomplete="off"/>
                    </td>
                    <td width="10%">Password</td>
                    <td width="20%">
                        <input name="pass" maxlength="30" type="password" autocomplete="off"/>
                    </td>
                    <td width="40%">
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                        <input type="text" name="captcha" size="10" maxlength="10" style="background-color:#f7ecbc;color:#000" />
                        <button class="submit-button">Login</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/forget_password');?>
">Lupa password? Klik Disini!</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php }else{ ?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <table class="table-input" width="100%">
            <tbody>
                <tr>
                    <td colspan="5">
                        Gunakan fasilitas pelayanan Flight Approval Online di Direktorat Angkutan Udara untuk mempermudah dan mempercepat proses permohonan Flight Approval anda disini!
                    </td>
                </tr>
                <tr>
                    <td width="10%">User Name</td>
                    <td width="20%">
                        <input name="username" maxlength="30" type="text" autocomplete="off"/>
                    </td>
                    <td width="10%">Password</td>
                    <td width="20%">
                        <input name="pass" maxlength="30" type="password" autocomplete="off"/>
                    </td>
                    <td width="40%">
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                        <input type="text" name="captcha" size="10" maxlength="10" style="background-color:#f7ecbc;color:#000" />
                        <button class="submit-button">Login</button> 
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/forget_password');?>
"><?php echo $_smarty_tpl->getVariable('member_forget')->value;?>
</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php }?>
<br />
<br />
<br />
<br />