<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:03:32
         compiled from "application/views\pengaturan/operator/account.html" */ ?>
<?php /*%%SmartyHeaderCode:592256554f34d0bbc0-02802645%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '70464a2fa895611387564fb67b489cd46fb313d0' => 
    array (
      0 => 'application/views\\pengaturan/operator/account.html',
      1 => 1444880369,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '592256554f34d0bbc0-02802645',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator');?>
">Operator</a><span></span>
        <small><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('operator')->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('operator')->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('operator')->value['operator_name'])))===null||$tmp==='' ? '' : $tmp);?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/operator/edit/').($_smarty_tpl->getVariable('operator')->value['user_id']));?>
">Data Pribadi</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/operator/account/').($_smarty_tpl->getVariable('operator')->value['user_id']));?>
" class="active">User Account</a></li>
    </ul>
    <div class="clear"></div>
    <div class="sub-content">
        <!-- notification template -->
        <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
        <!-- end of notification template-->
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/process_update_account');?>
" method="post">
            <input type="hidden" name="user_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
            <table class="table-input" width="100%">
                <tr>
                    <th colspan="2">Edit Account</th>
                </tr>
                <tr>
                    <td width='15%'>Username</td>
                    <td width='85%'>
                        <input type="hidden" name="user_name_old" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
                        <input type="text" name="user_name" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td>
                        <input type="password" name="user_pass" value="" size="20" maxlength="30" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td>Permissions</td>
                    <td colspan="3">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_roles')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <label <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['role_id'],$_smarty_tpl->getVariable('roles_selected')->value)){?>style="color: red;"<?php }?>>
                            <input type="checkbox" name="roles[]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['role_id'];?>
" <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['role_id'],$_smarty_tpl->getVariable('roles_selected')->value)){?>checked="checked"<?php }?> /> <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['role_nm']));?>

                        </label>
                        <?php }} ?>
                    </td>
                </tr> 
                <tr>
                    <td>Lock Status</td>
                    <td colspan="3">
                        <select name="lock_st">
                            <option value="0" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['lock_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=="0"){?>selected="selected"<?php }?>>OPEN</option>
                            <option value="1" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['lock_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=="1"){?>selected="selected"<?php }?>>LOCKED</option>
                        </select>
                    </td>
                </tr>
                <tr class="submit-box">
                    <td colspan="2">
                        <input name="save" value="Simpan" class="submit-button" type="submit"/>
                        <input name="save" value="Reset" class="reset-button" type="reset"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>