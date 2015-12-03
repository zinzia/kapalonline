<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 07:04:40
         compiled from "application/views\member/registration/add.html" */ ?>
<?php /*%%SmartyHeaderCode:3929565d387866d9a2-39344997%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d8acc258b3555698846faf5424f74b975e8ac5a' => 
    array (
      0 => 'application/views\\member/registration/add.html',
      1 => 1448949594,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3929565d387866d9a2-39344997',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal").datepicker({
            yearRange: '-50:+0',
            changeMonth: true,
            changeYear: true,
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd'
        });
		
    });
	
</script>
<div class="breadcrum">
    <p>
        <a href="#">Register</a><span></span>
        <small>Registration User</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back to Login</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
    <div class="sub-content">
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration/add_process');?>
" method="post" enctype="multipart/form-data">
            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="4">Data Pribadi</th>
                </tr>
                <tr>
                    <td width="15%">Nama Lengkap</td>
                    <td width="35%">
                        <input name="operator_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" />
                        <em>* wajib diisi</em>
                    </td>
                    <td width="15%">Alamat</td>
                    <td width="35%">
                        <input name="operator_address" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_address'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="100" />
                    </td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>
                        <input name="operator_birth_place" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_place'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="50" />
                    </td>
                    <td>Nomor Telepon</td>
                    <td>
                        <input name="operator_phone" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_phone'])===null||$tmp==='' ? '' : $tmp);?>
" size="15" maxlength="30" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>
                        <input name="operator_birth_day" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_day'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" class="tanggal" style="text-align: center;" readonly='readonly' />
                    </td>
                    <td>Email</td>
                    <td>
                        <input name="user_mail" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="50" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
				<tr>
					<td>Jenis Kelamin</td>
                    <td>
                        <label><input type="radio" name="operator_gender" value="L" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='L'){?>checked="checked"<?php }?> />LAKI - LAKI</label>
                        <label><input type="radio" name="operator_gender" value="P" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=='P'){?>checked="checked"<?php }?> />PEREMPUAN</label>
                    </td>
				</tr>	
                <tr class="headrow">
                    <th colspan="4">User Account</th>
                </tr>
                <tr>
                    <td>Username </td>
                    <td>
                        <input name="user_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" />
                        <em>* wajib diisi</em>
                    </td>
                    <td>Foto</td>
                    <td><input type="file" name="operator_photo" size="30" /></td>
                </tr>
				<tr>
                    <td>Password</td>
                    <td colspan="3">
                        <input name="user_pass" type="password" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                    </td>
					<td>
                        <b>Masukkan kode </b>
                        <br>
                        <input type="text" name="captcha" size="20" maxlength="10" style="background-color:#CDD0D9" />
                    </td>
				</tr>
                <tr class="submit-box">
                    <td colspan="4">
						</br>
                        <input type="submit" name="save" value="Simpan" class="submit-button" />
                        <input type="reset" name="save" value="Reset" class="reset-button" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
