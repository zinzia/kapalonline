<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:50:51
         compiled from "application/views\settings/role/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:30565655685bf19ef2-29327020%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '451eaf3d540549bb18e5199eda699bf37d4a573b' => 
    array (
      0 => 'application/views\\settings/role/edit.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30565655685bf19ef2-29327020',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Role Management</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="#" class="active">Edit Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminrole/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminrole');?>
">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminrole/process_update');?>
" method="post">
    <input type="hidden" name="role_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="2">Edit Role</th>
        </tr>
        <tr>
            <td width="25%">Web Portal *</td>
            <td width="75%">
                <select name="portal_id">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['portal_id'];?>
" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['portal_id'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==$_smarty_tpl->tpl_vars['data']->value['portal_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['portal_nm'];?>
</option>
                    <?php }} ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Role Name *</td>
            <td><input type="text" name="role_nm" maxlength="30" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_nm'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td>Role Description *</td>
            <td><input type="text" name="role_desc" maxlength="70" size="100" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_desc'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td>Default Page *</td>
            <td><input type="text" name="default_page" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['default_page'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="Simpan" class="edit-button" /> </td>
        </tr>
    </table>
</form>
