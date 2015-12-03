<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:43:45
         compiled from "application/views\settings/role/access.html" */ ?>
<?php /*%%SmartyHeaderCode:5262565566b1435619-28918246%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd79fff501a2004c3f213c882a5bce0e4b2813c87' => 
    array (
      0 => 'application/views\\settings/role/access.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5262565566b1435619-28918246',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Role Permissions</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminpermissions');?>
" class="active">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr>
        <th width="5%">No</th>
        <th width="20%">Web Portal</th>
        <th width="25%">Nama</th>
        <th width="35%">Deskripsi</th>
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
        <td align="center">
            <a href="<?php ob_start();?><?php echo ('settings/adminpermissions/access_update/').($_smarty_tpl->tpl_vars['result']->value['role_id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getVariable('config')->value->site_url($_tmp1);?>
" class="button-edit">Permissions</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="5">Empty</td>
    </tr>
    <?php } ?>
</table>
