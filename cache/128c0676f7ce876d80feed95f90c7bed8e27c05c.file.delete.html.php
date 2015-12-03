<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 14:30:38
         compiled from "application/views\pengaturan/operator/delete.html" */ ?>
<?php /*%%SmartyHeaderCode:28168565c4f7e7e1e86-56671154%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '128c0676f7ce876d80feed95f90c7bed8e27c05c' => 
    array (
      0 => 'application/views\\pengaturan/operator/delete.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28168565c4f7e7e1e86-56671154',
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
        <small><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
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
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/delete_process');?>
" method="post" onsubmit="return confirm('Apakah anda yakin akan menghapus data berikut ini?')">
    <input name="user_id" type="hidden"  value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="4">Apakah anda yakin akan menghapus data dibawah ini?</th>
        </tr>
        <tr>
            <td width="15%">Nama Lengkap</td>
            <td width="35%"><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
</td>
            <td width="15%">Alamat</td>
            <td width="35%"><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_address'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>
                <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp)=='L'){?>Laki - Laki<?php }?>
                <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='P'){?>Perempuan<?php }?>
            </td>
            <td>Email</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Tempat Lahir</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_place'])===null||$tmp==='' ? '' : $tmp);?>
</td>
            <td>Nomor Telepon</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_phone'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_day'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        </tr>
        <tr class="headrow">
            <th colspan="4">Attachment</th>
        </tr>
        <tr>
            <td>Foto</td>
            <td colspan="3">
                <img src="<?php echo (($tmp = @$_smarty_tpl->getVariable('operator_photo')->value)===null||$tmp==='' ? '' : $tmp);?>
" alt="" style="height: 160px; background-color: #fff; border: 1px solid #ccc; padding: 5px;" />
            </td>
        </tr>
        <tr class="submit-box">
            <td colspan="4">
                <input type="submit" name="save" value="Hapus" class="reset-button" />
            </td>
        </tr>
    </table>
</form>  