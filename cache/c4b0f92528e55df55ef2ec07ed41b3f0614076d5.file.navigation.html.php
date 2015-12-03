<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:45:36
         compiled from "application/views\settings/menu/navigation.html" */ ?>
<?php /*%%SmartyHeaderCode:307895655672075b402-99600270%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c4b0f92528e55df55ef2ec07ed41b3f0614076d5' => 
    array (
      0 => 'application/views\\settings/menu/navigation.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '307895655672075b402-99600270',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Navigation - <?php echo (($tmp = @$_smarty_tpl->getVariable('portal')->value['portal_nm'])===null||$tmp==='' ? '' : $tmp);?>
</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('settings/adminmenu/add/').($_smarty_tpl->getVariable('portal')->value['portal_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Menu</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('settings/adminmenu/navigation/').($_smarty_tpl->getVariable('portal')->value['portal_id']));?>
" class="active">List Menu</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminmenu');?>
">Web Portal</a></li>
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
        <th width="5%"></th>
        <th width="46%">Judul Menu</th>
        <th width="20%">Alamat</th>
        <th width="7%">Uses</th>
        <th width="7%">Show</th>
        <th width="15%"></th>
    </tr>
    <?php echo (($tmp = @$_smarty_tpl->getVariable('rs_id')->value)===null||$tmp==='' ? '' : $tmp);?>

</table>