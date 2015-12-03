<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:59:24
         compiled from "application/views\stakeholder/welcome/role.html" */ ?>
<?php /*%%SmartyHeaderCode:21465565691aca9de24-43911500%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a0bfc6a9486687313682d2df0e32f095d9f4b700' => 
    array (
      0 => 'application/views\\stakeholder/welcome/role.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21465565691aca9de24-43911500',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p><a href="#">Account Settings</a><span></span><small>Change Role</small></p>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/account_settings/process_update_role');?>
" method="post" onsubmit="return confirm('Apakah anda yakin akan merubah hak akses anda sekarang?');">
    <table class="table-input" width="100%">
        <tr>
            <th colspan="2">Change Role</th>
        </tr>
        <tr>
            <td width='15%'>Active Role</td>
            <td width='85%'><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('com_user')->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm'])))===null||$tmp==='' ? '' : $tmp);?>
</b></td>
        </tr>
        <tr>
            <td>Change To</td>
            <td>
                <select name="role_id">
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_roles')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['role_id'];?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('com_user')->value['role_id'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['data']->value['role_id']){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm']));?>
</option>
                    <?php }} ?>
                </select>
            </td>
        </tr>
        <tr class="submit-box">
            <td colspan="2">
                <input name="save" value="Change" class="submit-button" type="submit"/>
            </td>
        </tr>
    </table>
</form>