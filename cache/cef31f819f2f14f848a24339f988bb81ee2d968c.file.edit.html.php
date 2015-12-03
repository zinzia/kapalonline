<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 04:22:15
         compiled from "application/views\pengaturan/operator/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:1990565d1267f295c8-62036350%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cef31f819f2f14f848a24339f988bb81ee2d968c' => 
    array (
      0 => 'application/views\\pengaturan/operator/edit.html',
      1 => 1448894357,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1990565d1267f295c8-62036350',
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
        /*
         * COMBO BOX
         */
        $(".pelabuhan").select2({
            placeholder: "Pilih Pelabuhan",
            allowClear: true,
            width: 300
        });
		
    });
	
</script>
<style type="text/css">
    .select2-choice {
        width: 280px !important;
    }
    .select2-default {
        width: 280px !important;
    }
</style>

<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator');?>
">Operator</a><span></span>
        <small><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('result')->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('result')->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('result')->value['operator_name'])))===null||$tmp==='' ? '' : $tmp);?>
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
<div class="sub-nav-content">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/operator/edit/').($_smarty_tpl->getVariable('result')->value['user_id']));?>
" class="active">Data Pribadi</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/operator/account/').($_smarty_tpl->getVariable('result')->value['user_id']));?>
">User Account</a></li>
    </ul>
    <div class="clear"></div>
    <div class="sub-content">
        <!-- notification template -->
        <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
        <!-- end of notification template-->
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/edit_process');?>
" method="post" enctype="multipart/form-data">
            <input name="user_id" type="hidden"  value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
            <input name="operator_email_old" type="hidden"  value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" />
            <input name="operator_photo" type="hidden"  value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_photo'])===null||$tmp==='' ? '' : $tmp);?>
" />
            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="4">Edit Data</th>
                </tr>
                <tr>
                    <td width="15%">Nama Lengkap</td>
                    <td width="35%">
                        <input name="operator_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" />
                        <em>* wajib diisi</em>
                    </td>
                    <td width="15%">Alamat</td>
                    <td width="35%">
                        <input name="operator_address" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_address'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="100" />
                    </td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>
                        <input name="operator_birth_place" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_place'])===null||$tmp==='' ? '' : $tmp);?>
" size="25" maxlength="50" />
                    </td>
                    <td>Email</td>
                    <td>
                        <input name="user_mail" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="50" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>
                        <input name="operator_birth_day" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_birth_day'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" class="tanggal" style="text-align: center;" readonly='readonly' />
                    </td>
                    <td>Nomor Telepon</td>
                    <td>
                        <input name="operator_phone" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_phone'])===null||$tmp==='' ? '' : $tmp);?>
" size="15" maxlength="30" />
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
                    <td>Jenis Kelamin</td>
                    <td>
                        <label><input type="radio" name="operator_gender" value="L" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='L'){?>checked="checked"<?php }?> />Laki-laki</label>
                        <label><input type="radio" name="operator_gender" value="P" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['operator_gender'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=='P'){?>checked="checked"<?php }?> />Perempuan</label>
                    </td>
                </tr>
                <tr>
				<td>Pelabuhan </td>
					<td colspan="3">
						<select name="pelabuhan_id" class="pelabuhan">
							<option value=""></option>
							<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_sub')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'];?>
" <?php if (($_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'])==$_smarty_tpl->getVariable('result')->value['pelabuhan_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_nm'];?>
 - <?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_kd'];?>
</option>
							<?php }} ?>
						</select>
						&nbsp;
						<em>* wajib diisi</em>
					</td>            
				</tr>
                <tr class="headrow">
                    <th colspan="4">Attachment</th>
                </tr>
                <tr>
                    <td colspan="4" align="center">
                        <img src="<?php echo (($tmp = @$_smarty_tpl->getVariable('operator_photo')->value)===null||$tmp==='' ? '' : $tmp);?>
" alt="" style="height: 160px; background-color: #fff; border: 1px solid #ccc; padding: 5px;" />
                        <br /><br />
                        <input type="file" name="operator_photo" size="30" />
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
    </div>
</div>