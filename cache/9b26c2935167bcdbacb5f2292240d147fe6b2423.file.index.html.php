<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 18:35:19
         compiled from "application/views\pendaftaran_kapal/akta/index.html" */ ?>
<?php /*%%SmartyHeaderCode:4862565f2bd72bab48-99299981%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b26c2935167bcdbacb5f2292240d147fe6b2423' => 
    array (
      0 => 'application/views\\pendaftaran_kapal/akta/index.html',
      1 => 1449077718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4862565f2bd72bab48-99299981',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function () {
        // date picker
        $(".tanggal").datepicker({
            showOn: 'both',
            changeMonth: true,
            changeYear: true,
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
        });
		
        /*
         * COMBO BOX
         */
        $(".pelabuhan").select2({
            placeholder: "Pilih Pelabuhan",
            allowClear: true,
            width: 270
        });
    });
</script>
<style type="text/css">
    .select2-choice {
        width: 190px !important;
    }
    .select2-default {
        width: 190px !important;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Pengajuan Akta Pendaftaran</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/akta_pendaftaran/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Draft List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="#" class="active"><b>Langkah 1</b><br />Data Pengajuan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Langkah 2</b><br />Data Kapal</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Langkah 3</b><br />Data Pemilik</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Langkah 4</b><br />Upload File</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Langkah 5</b><br />Review Pengajuan</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pendaftaran_kapal/akta/add_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width="100%">
        <tr>
            <td colspan="4" align='center'>
                <b style="font-size: 16px;">PERMOHONAN <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm']));?>
</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        <tr>
            <td width='19%'>
                <span style="text-decoration: underline;">Tanggal Surat Permohonan</span><br /><i></i>
            </td>
            <td width='26%'>
                <input type="text" name="tgl_surat" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['tgl_surat'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center; font-weight: bold;" />
            </td>
            <td width='19%'>
                <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i></i>
            </td>
            <td width='36%'>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td><span style="text-decoration: underline;">Nama Pemohon</span><br /><i></i></td>
            <td>
                <input type="text" name="nama_pemohon" size="20" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['nama_pemohon'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['nama_pemohon'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['nama_pemohon'])===null||$tmp==='' ? '' : $tmp)));?>
" />
            </td>
			<td>
                <span style="text-decoration: underline;">Pemohon merupakan </span><br /><i>Pilih salah satu</i>
            </td>
            <td>
            </br>
            	<input type="radio" name="pemilik_st" value="1"  <?php if ($_smarty_tpl->getVariable('result')->value['pemilik_st']==1){?>checked="checked"<?php }?> /> Pemilik Kapal
				</br>
				</br>
            	<input type="radio" name="pemilik_st" value="0"  <?php if ($_smarty_tpl->getVariable('result')->value['pemilik_st']==0&&$_smarty_tpl->getVariable('result')->value['pemilik_st']!=null){?>checked="checked"<?php }?> /> Bukan Pemilik Kapal
			</td>
        </tr> <tr>
            <td><span style="text-decoration: underline;">Tempat Pendaftaran</span><br /><i></i></td>
            <td>
                <select name="pelabuhan_id" class="pelabuhan">
					<option value=""></option>
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_pelabuhan')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'];?>
" <?php if (($_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'])==$_smarty_tpl->getVariable('result')->value['pelabuhan_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_nm'];?>
 - <?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_kd'];?>
</option>
					<?php }} ?>
				</select>
            </td>
			<td>
                <span style="text-decoration: underline;">Bukti Hak Milik Atas Kapal </span><br /><i>Pilih salah satu</i>
            </td>
            <td></br></br>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_subgroup')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
				<input type="radio" name="bukti_hakmilik" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['subgroup_id'];?>
" <?php if ($_smarty_tpl->getVariable('result')->value['bukti_hakmilik']==$_smarty_tpl->tpl_vars['data']->value['subgroup_id']){?>checked="checked"<?php }?>/> <?php echo $_smarty_tpl->tpl_vars['data']->value['subgroup_nm'];?>
 </br></br>
			<?php }} ?>
			</td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td></td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan dan Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>