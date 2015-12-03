<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:43:35
         compiled from "application/views\settings/user/list.html" */ ?>
<?php /*%%SmartyHeaderCode:1619565566a7f0a7a8-12118262%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd04c7353b49851fef059b45894067163717067a2' => 
    array (
      0 => 'application/views\\settings/user/list.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1619565566a7f0a7a8-12118262',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Users</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminuser/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminuser');?>
" class="active">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="pageRow">
    <div class="pageNav">
        <ul>
            <li class="info"><strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['start'])===null||$tmp==='' ? '0' : $tmp);?>
 - <?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['end'])===null||$tmp==='' ? '0' : $tmp);?>
</strong> dari <strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['total'])===null||$tmp==='' ? '0' : $tmp);?>
</strong> Data</li>
            <?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['data'])===null||$tmp==='' ? '' : $tmp);?>

        </ul>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr>
        <th width="5%">No</th>
        <th width="35%">Role</th>
        <th width="35%">Email</th>
        <th width="10%">Lock Status</th>
        <th width="15%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)==0){?>class="blink-row"<?php }?>>
        <td><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['role_nm'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['user_mail'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['lock_st'];?>
</td>
        <td align="center">
            <a href="<?php ob_start();?><?php echo ('settings/adminuser/edit/').($_smarty_tpl->tpl_vars['result']->value['user_id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getVariable('config')->value->site_url($_tmp1);?>
" class="button-edit">Edit</a>
            <a href="<?php ob_start();?><?php echo ('settings/adminuser/hapus/').($_smarty_tpl->tpl_vars['result']->value['user_id']);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->getVariable('config')->value->site_url($_tmp2);?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>