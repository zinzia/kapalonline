<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 16:04:36
         compiled from "application/views\member/account_settings/index.html" */ ?>
<?php /*%%SmartyHeaderCode:176635655ce04b81a11-80963763%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0943d0555cfc3f9d68f363d9fafa60eebf7fb975' => 
    array (
      0 => 'application/views\\member/account_settings/index.html',
      1 => 1443500820,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176635655ce04b81a11-80963763',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal").datepicker({
            yearRange: '-50:+0',
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/account_settings/process_data_pribadi');?>
" method="post" enctype="multipart/form-data">
    <table class="table-input" width="100%">
        <tr>
            <th colspan="3">Data Pribadi</th>
        </tr>
        <tr>
            <td width="20%">Nama Lengkap</td>
            <td width="55%">
                <input type="text" name="operator_name" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
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
                <label><input type="radio" name="operator_gender" value="L" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='L'){?>checked="checked"<?php }?> />LAKI - LAKI</label>
                <label><input type="radio" name="operator_gender" value="P" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=='P'){?>checked="checked"<?php }?> />PEREMPUAN</label>
            </td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>
                <input name="operator_birth_place" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_place'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="50" />
                <input name="operator_birth_day" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_day'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" class="tanggal" style="text-align: center;" readonly='readonly' />
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>
                <input name="operator_address" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_address'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="100" />
            </td>
        </tr>
        <tr>
            <td>Nomor Telepon</td>
            <td>
                <input name="operator_phone" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_phone'])===null||$tmp==='' ? '' : $tmp);?>
" size="15" maxlength="30" />
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
            <th colspan="3">
                <input type="hidden" name="member_status" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['member_status'])===null||$tmp==='' ? '' : $tmp);?>
" />
                <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('result')->value['member_status'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('result')->value['member_status'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('result')->value['member_status']));?>
 UNTUK :
            </th>
        </tr>
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <tr>
            <td colspan="3">
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/account_settings/process_change_airlines/').($_smarty_tpl->tpl_vars['data']->value['airlines_id']));?>
" <?php if ($_smarty_tpl->getVariable('com_user')->value['airlines_id']==$_smarty_tpl->tpl_vars['data']->value['airlines_id']){?>style="color: red; font-weight: bold;"<?php }?>> <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
 ( <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_brand'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_brand'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_brand']));?>
 )</a>
            </td>
        </tr> 
        <?php }} ?>
        <tr class="submit-box">
            <td colspan="3" align="right">
                <input name="save" value="Update" class="submit-button" type="submit"/>
            </td>
        </tr>
    </table>
</form>