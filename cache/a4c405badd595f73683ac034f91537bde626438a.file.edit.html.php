<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:45:05
         compiled from "application/views\settings/user/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:28860565567013fc359-39246536%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a4c405badd595f73683ac034f91537bde626438a' => 
    array (
      0 => 'application/views\\settings/user/edit.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28860565567013fc359-39246536',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Users</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="#" class="active">Edit Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminuser/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminuser');?>
">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminuser/edit_process');?>
" method="post">
    <input type="hidden" name="user_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="user_name_old" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="user_mail_old" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="2">Edit Data</th>
        </tr>
        <tr>
            <th colspan="2">User Info</th>
        </tr>
        <tr>
            <td width="25%">Nama Lengkap *</td>
            <td width="75%"><input type="text" name="full_name" maxlength="50" size="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['full_name'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Jabatan *</td>
            <td><input type="text" name="jabatan" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td>Email *</td>
            <td><input type="text" name="user_mail" maxlength="50" size="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Nomor Telepon *</td>
            <td><input type="text" name="telp" maxlength="50" size="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['telp'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <th colspan="2">User Account</th>
        </tr>
        <tr>
            <td>User Name *</td>
            <td><input type="text" name="user_name" maxlength="50" size="20" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input type="text" name="user_pass" maxlength="50" size="20" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Lock Status *</td>
            <td>
                <select name="lock_st">
                    <option value="0" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['lock_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=="0"){?>selected="selected"<?php }?>>Active</option>
                    <option value="1" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['lock_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=="1"){?>selected="selected"<?php }?>>Blocked</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Hak Akses *</td>
            <td>
                <select name="role_id">
                    <option></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_role')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['role_id'];?>
" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_id'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp3=ob_get_clean();?><?php if ($_tmp3==$_smarty_tpl->tpl_vars['data']->value['role_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['role_nm'];?>
</option>
                    <?php }} ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="Simpan" class="edit-button" /> </td>
        </tr>
    </table>
</form>