<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 16:04:50
         compiled from "application/views\member/account_settings/account.html" */ ?>
<?php /*%%SmartyHeaderCode:171005655ce12f039e4-65839855%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '097237e626577ef7a873acf7eefa128671e3924d' => 
    array (
      0 => 'application/views\\member/account_settings/account.html',
      1 => 1441883436,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '171005655ce12f039e4-65839855',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p><a href="#">Account Settings</a><span></span><small>Edit Account</small></p>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/account_settings/process_update_account');?>
" method="post">
    <table class="table-input" width="100%">
        <tr>
            <th colspan="2">Edit Account</th>
        </tr>
        <tr>
            <td width='25%'>Username</td>
            <td width='75%'>
                <input type="hidden" name="user_name_old" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
                <input type="text" name="user_name" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" />
            </td>
        </tr>
        <tr>
            <td>Old Password</td>
            <td><input type="password" name="user_pass_old" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass_old'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" /></td>
        </tr>
        <tr>
            <td>New Password</td>
            <td><input type="password" name="user_pass_new" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass_new'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" /></td>
        </tr>
        <tr>
            <td>Confirm New Password</td>
            <td><input type="password" name="user_pass_confirm" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass_confirm'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" /></td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <input name="save" value="Update" class="submit-button" type="submit"/>
            </td>
        </tr>
    </table>
</form>