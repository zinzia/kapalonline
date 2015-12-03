<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:43:38
         compiled from "application/views\settings/role/list.html" */ ?>
<?php /*%%SmartyHeaderCode:11152565566aac6f0d4-07377899%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '084ae98b3ba9bd4dec28501233b6ecf828dd0009' => 
    array (
      0 => 'application/views\\settings/role/list.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11152565566aac6f0d4-07377899',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Role Management</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminrole/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminrole');?>
" class="active">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width="5%">No</th>
        <th width="20%">Web Portal</th>
        <th width="20%">Role Name</th>
        <th width="20%">Role Description</th>
        <th width="20%">Default Page</th>
        <th width="15%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
 $_smarty_tpl->tpl_vars['no']->value = $_smarty_tpl->tpl_vars['result']->key;
?>
    <tr <?php if (($_smarty_tpl->tpl_vars['no']->value%2)!=0){?>class="blink-row"<?php }?>>
        <td><?php echo $_smarty_tpl->tpl_vars['no']->value+1;?>
.</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['portal_nm'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['role_nm'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['role_desc'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['default_page'];?>
</td>
        <td align="center">
            <a href="<?php ob_start();?><?php echo ('settings/adminrole/edit/').($_smarty_tpl->tpl_vars['result']->value['role_id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getVariable('config')->value->site_url($_tmp1);?>
" class="button-edit">Edit</a>
            <a href="<?php ob_start();?><?php echo ('settings/adminrole/hapus/').($_smarty_tpl->tpl_vars['result']->value['role_id']);?>
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