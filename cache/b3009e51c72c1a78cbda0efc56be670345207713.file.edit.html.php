<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:47:24
         compiled from "application/views\settings/menu/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:113155655678c4173b6-18994614%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3009e51c72c1a78cbda0efc56be670345207713' => 
    array (
      0 => 'application/views\\settings/menu/edit.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113155655678c4173b6-18994614',
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
            <li><a href="#" class="active">Edit Data</a></li>
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminmenu/process_update');?>
" method="post" enctype="multipart/form-data">
    <input type="hidden" name="portal_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('portal')->value['portal_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="nav_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="2">Edit Menu</th>
        </tr>
        <tr>
            <td width="25%">Induk Menu</td>
            <td width="75%">
                <select name="parent_id">
                    <option value="0"></option>
                    <?php echo (($tmp = @$_smarty_tpl->getVariable('list_parent')->value)===null||$tmp==='' ? '' : $tmp);?>

                </select>
            </td>
        </tr>
        <tr>
            <td>Judul Menu</td>
            <td><input type="text" name="nav_title" maxlength="100" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_title'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Deskripsi</td>
            <td><input type="text" name="nav_desc" maxlength="255" size="70" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_desc'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Alamat Menu</td>
            <td><input type="text" name="nav_url" maxlength="255" size="40" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_url'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Urutan</td>
            <td><input type="text" name="nav_no" maxlength="5" size="5" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['nav_no'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Digunakan</td>
            <td>
                <select name="active_st">
                    <option value="1"<?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['active_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=="1"){?>selected="selected"<?php }?>>Ya</option>
                    <option value="0" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['active_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=="0"){?>selected="selected"<?php }?>>Tidak</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Ditampilkan</td>
            <td>
                <select name="display_st">
                    <option value="1"<?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['display_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp3=ob_get_clean();?><?php if ($_tmp3=="1"){?>selected="selected"<?php }?>>Ya</option>
                    <option value="0" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['display_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp4=ob_get_clean();?><?php if ($_tmp4=="0"){?>selected="selected"<?php }?>>Tidak</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Navigation Icon</td>
            <td><input type="file" name="nav_icon" /></td>
        </tr>
        <tr>
            <td></td>
            <td><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
<?php echo $_smarty_tpl->getVariable('nav_icon')->value;?>
" alt="" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="Simpan" class="edit-button" /> </td>
        </tr>
    </table>
</form>