<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 09:10:23
         compiled from "application/views\pengaturan/aircraft/add.html" */ ?>
<?php /*%%SmartyHeaderCode:193885656be6f82a7b9-85278520%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5a98aabae45db9b03b5a21cb4a744d821a08e6d4' => 
    array (
      0 => 'application/views\\pengaturan/aircraft/add.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '193885656be6f82a7b9-85278520',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/aircraft');?>
">Tipe Pesawat</a><span></span>
        <small>Add Data</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/aircraft');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/aircraft/add_process');?>
" method="post">
    <table class="table-input" width="100%" border='0'>
        <tr class="headrow">
            <th colspan="4">Tambah Data Tipe Pesawat</th>
        </tr>
        <tr>
            <td width="15%">Pembuat</td>
            <td width="35%">
                <input name="aircraft_manufacture" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_manufacture'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="40" />
                <em>* wajib diisi</em>
            </td>
            <td width="15%">Tipe</td>
            <td width="35%">
                <input name="aircraft_model" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_model'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="25" />
                <em>* wajib diisi</em>
            </td>
        </tr>  
        <tr>
            <td>Tahun Pembuatan</td>
            <td>
                <input name="aircraft_product_year" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_product_year'])===null||$tmp==='' ? '' : $tmp);?>
" size="4" maxlength="4" />
            </td>

            <td>Standard Capacity</td>
            <td>
                <input name="aircraft_std_capacity" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_std_capacity'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td width="15%">Deskripsi</td>
            <td width="35%" colspan="3">
                <input name="aircraft_desc" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_desc'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="255" />
            </td>
        </tr>
        <tr class="submit-box">
            <td colspan="4">
                <input type="submit" name="save" value="Simpan" class="submit-button" />
                <input type="reset" name="save" value="Reset" class="reset-button" />
            </td>
        </tr>
    </table>
</form>