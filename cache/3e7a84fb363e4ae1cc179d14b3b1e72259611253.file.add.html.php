<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 13:56:54
         compiled from "application/views\pengaturan/member/add.html" */ ?>
<?php /*%%SmartyHeaderCode:27935655b01666d7f3-00295660%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e7a84fb363e4ae1cc179d14b3b1e72259611253' => 
    array (
      0 => 'application/views\\pengaturan/member/add.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27935655b01666d7f3-00295660',
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
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/member');?>
">Member</a><span></span>
        <small>Add Data</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/member');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back</a></li>
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
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/member/add_process');?>
" method="post" enctype="multipart/form-data">
            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="4">Data Member</th>
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
                    <td>Member Sebagai</td>
                    <td>
                        <select name="member_status" id="member_status">
                            <option value=""></option>
                            <option value="operator" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['member_status'])===null||$tmp==='' ? '' : $tmp)=='operator'){?>selected="selected"<?php }?>>OPERATOR</option>
                            <option value="agent" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['member_status'])===null||$tmp==='' ? '' : $tmp)=='agent'){?>selected="selected"<?php }?>>AGENT</option>
                        </select>
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td rowspan="4">Operator / Airlines yang Ditangani</td>
                    <th colspan="3">OPERATOR NASIONAL</th>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="nasional">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines_nasional')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <label>
                            <input type="checkbox" name="airlines[]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['airlines_id'];?>
" <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['airlines_id'],$_smarty_tpl->getVariable('airlines_selected')->value)){?>checked="checked"<?php }?> /> <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>

                        </label>
                        <?php }} ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th colspan="3">OPERATOR ASING</th>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="asing">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines_asing')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <label>
                            <input type="checkbox" name="airlines[]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['airlines_id'];?>
" <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['airlines_id'],$_smarty_tpl->getVariable('airlines_selected')->value)){?>checked="checked"<?php }?> /> <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>

                        </label>
                        <?php }} ?>
                        </div>
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
                <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
                <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_roles')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                <tr>
                    <td><?php if ($_smarty_tpl->getVariable('no')->value==1){?>Permissions<?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?><?php }?></td>
                    <td colspan="3">
                        <label>
                            <input type="checkbox" name="roles[]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['role_id'];?>
" <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['role_id'],$_smarty_tpl->getVariable('roles_selected')->value)){?>checked="checked"<?php }?> /> <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm']));?>

                        </label>
                    </td>
                </tr> 
                <?php }} ?>
                <tr class="submit-box">
                    <td colspan="4">
                        <input type="submit" name="save" value="Simpan" class="submit-button" />
                        <input type="reset" name="save" value="Reset" class="reset-button" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery('#member_status').on('change', function(member_status) {
        var status = member_status.currentTarget.value;
        if (status == "agent") {
            document.getElementById('nasional').style.display = "none";
            document.getElementById('asing').style.display = "table";
        } else {
            document.getElementById('nasional').style.display = "table";
            document.getElementById('asing').style.display = "table";
        }
    })
</script>