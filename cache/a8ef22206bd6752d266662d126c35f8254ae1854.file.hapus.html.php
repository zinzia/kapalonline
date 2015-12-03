<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 19:17:04
         compiled from "application/views\settings/menu/hapus.html" */ ?>
<?php /*%%SmartyHeaderCode:26816565de420cfb633-73861615%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8ef22206bd6752d266662d126c35f8254ae1854' => 
    array (
      0 => 'application/views\\settings/menu/hapus.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26816565de420cfb633-73861615',
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
            <li><a href="#" class="active">Hapus Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('settings/adminmenu/add/').($_smarty_tpl->getVariable('portal')->value['portal_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Menu</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('settings/adminmenu/navigation/').($_smarty_tpl->getVariable('portal')->value['portal_id']));?>
">List Menu</a></li>
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminmenu/process_delete');?>
" method="post">
    <input type="hidden" name="portal_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('portal')->value['portal_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="nav_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="nav_icon" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_icon'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="2">Hapus Menu</th>
        </tr>
        <tr>
            <th colspan="2">Apakah anda yakin akan menghapus data dibawah ini?</th>
        </tr>
        <tr>
            <td width="25%">Judul Menu</td>
            <td width="75%"><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_title'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Deskripsi</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_desc'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Alamat Menu</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_url'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Urutan</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_no'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Digunakan</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['active_st'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Ditampilkan</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['display_st'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Navigation Icon</td>
            <td colspan="3"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
<?php echo $_smarty_tpl->getVariable('nav_icon')->value;?>
" alt="" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="Hapus" class="edit-button" /> </td>
        </tr>
    </table>
</form>