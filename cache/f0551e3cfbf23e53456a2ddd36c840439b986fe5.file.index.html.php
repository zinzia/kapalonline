<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 10:09:00
         compiled from "application/views\dashboard/account_settings/index.html" */ ?>
<?php /*%%SmartyHeaderCode:1081956557aace9ff98-95020967%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0551e3cfbf23e53456a2ddd36c840439b986fe5' => 
    array (
      0 => 'application/views\\dashboard/account_settings/index.html',
      1 => 1441883418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1081956557aace9ff98-95020967',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal").datepicker({
            yearRange: '-90:+0',
            changeMonth: true,
            changeYear: true,
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
<div class="breadcrum">
    <p><a href="#">Account Settings</a><span></span><small>Data Pribadi</small></p>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('dashboard/account_settings/process_data_pribadi');?>
" method="post" enctype="multipart/form-data">
    <table class="table-input" width="100%">
        <tr>
            <th colspan="3">Data Pribadi</th>
        </tr>
        <tr>
            <td width="25%">Nama Lengkap</td>
            <td width="50%">
                <input type="text" name="operator_name" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="40" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
            <td rowspan="6" align='center' width="25%">
                <img src="<?php echo (($tmp = @$_smarty_tpl->getVariable('operator_photo')->value)===null||$tmp==='' ? '' : $tmp);?>
" alt="" style="height: 160px; width: 90%; background-color: #fff; border: 1px solid #ccc; padding: 5px;" />
                <br /><br />
                <input type="file" name="operator_photo" />
            </td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>
                <select name="operator_gender">
                    <option></option>
                    <option value="L" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=="L"){?>selected="selected"<?php }?>>Laki - laki</option>
                    <option value="P" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=="P"){?>selected="selected"<?php }?>>Perempuan</option>
                </select>
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>
                <input type="text" name="operator_birth_place" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_place'])===null||$tmp==='' ? '' : $tmp);?>
" size="40" maxlength="50" />
                , <input type="text" name="operator_birth_day" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_day'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" class="tanggal" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td><input type="text" name="operator_address" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_address'])===null||$tmp==='' ? '' : $tmp);?>
" size="70" maxlength="100" /></td>
        </tr>
        <tr>
            <td>Nomor Telepon</td>
            <td>
                <input type="text" name="operator_phone" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_phone'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <input type="text" name="user_mail" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" size="40" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>
                <input name="jabatan" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Sub Direktorat</td>
            <td colspan="3">
                <select name="sub_direktorat">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_sub')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pref_value'];?>
" <?php if ($_smarty_tpl->tpl_vars['data']->value['pref_value']==$_smarty_tpl->getVariable('result')->value['sub_direktorat']){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['pref_value'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['pref_value'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['pref_value']));?>
</option>
                    <?php }} ?>
                </select>
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr class="submit-box">
            <td colspan="3">
                <input name="save" value="Simpan" class="submit-button" type="submit"/>
                <input name="save" value="Reset" class="reset-button" type="reset"/>
            </td>
        </tr>
    </table>
</form>