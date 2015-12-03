<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 20:19:01
         compiled from "application/views\pendaftaran_kapal/akta/data_kapal.html" */ ?>
<?php /*%%SmartyHeaderCode:13863565f44250bd901-69586711%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3fd88d483e463bf7886b79aaa3beac2cd9f05f25' => 
    array (
      0 => 'application/views\\pendaftaran_kapal/akta/data_kapal.html',
      1 => 1449083861,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13863565f44250bd901-69586711',
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
        $(".jenisdetailkapal").select2({
            placeholder: "Pilih Jenis Kapal",
            allowClear: true,
            width: 270
        });
        $(".benderaasal").select2({
            placeholder: "Pilih Bendera Asal",
            allowClear: true,
            width: 270
        });
    });
</script>
<style type="text/css">
    .select2-choice {
        width: 270px !important;
    }
    .select2-default {
        width: 270px !important;
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pendaftaran_kapal/akta/data_pengajuan/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Langkah 1</b><br />Data Pengajuan</a>
        </li>
        <li>
            <a href="#" class="active"><b>Langkah 2</b><br />Data Kapal</a>
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
            <td colspan="4" align="center">
                <b style="font-size: 16px;">DATA KAPAL</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        <tr>
            <td width='15%'>
                <span style="text-decoration: underline;">Nama Kapal</span><br /><i></i>
            </td>
            <td width='35%'>
                <input type="text" name="no_surat" size="30" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
			<td width='15%'>
                <span style="text-decoration: underline;">Eks Nama Kapal</span><br /><i></i>
            </td>
            <td width='35%'>
                <input type="text" name="no_surat" size="30" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Kapal</span><br /><i></i>
            </td>
            <td>
				<select name="jenisdetail_id" class="jenisdetailkapal">
					<option value=""></option>
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_jeniskapal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['jenisdetail_id'];?>
" <?php if (($_smarty_tpl->tpl_vars['data']->value['jenisdetail_id'])==$_smarty_tpl->getVariable('result')->value['jenisdetail_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['jenis_ket'];?>
 - <?php echo $_smarty_tpl->tpl_vars['data']->value['jenisdetail_ket'];?>
</option>
					<?php }} ?>
				</select>
			</td>
            <td>
                <span style="text-decoration: underline;">Nomor IMO</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="20" maxlength="30" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Call Sign</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="10" maxlength="20" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">No. CSR</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="15" maxlength="20" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Harga Kapal</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="20" maxlength="30" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Bendera Asal</span><br /><i></i>
            </td>
            <td>
				<select name="negara_id" class="benderaasal">
					<option value=""></option>
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_benderaasal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['negara_id'];?>
" <?php if (($_smarty_tpl->tpl_vars['data']->value['negara_id'])==$_smarty_tpl->getVariable('result')->value['negara_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['negara_nm'];?>
 </option>
					<?php }} ?>
				</select>
			</td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        </tr>
	<table class="table-form" width="100%">
    	<tr>
            <td colspan="4" align="center">
                <b style="font-size: 16px;">SURAT UKUR & DATA PEMBANGUNAN KAPAL</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        
        <tr>
            <td>
                <span style="text-decoration: underline;">Tempat Terbit</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Tempat Pembuatan</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            
        </tr>
		<tr>
            <td>
                <span style="text-decoration: underline;">Tanggal Surat Ukur</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="tgl_surat" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['tgl_surat'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center; font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Tanggal Peletakan Lunas</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="tgl_surat" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['tgl_surat'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center; font-weight: bold;" />
            </td>
        </tr>
		<tr>
            <td>
                <span style="text-decoration: underline;">No. Surat Ukur</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        </tr>
	</table>
	<table class="table-form" width="100%">
    	<tr>
            <td colspan="4" align="center">
                <b style="font-size: 16px;">DIMENSI</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Bahan Utama Kapal</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Jumlah Geladak</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Jumlah Cerobong</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Jumlah Baling Baling</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Penggerak Utama</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Mesin Merk</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Putaran Mesin</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <span style="text-decoration: underline;">Type Mesin</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Serial No. Mesin</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
	</table>
	<table class="table-form" width="100%">
    	<tr>
            <td colspan="4" align="center">
                <b style="font-size: 16px;">DAYA MESIN</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align='center'>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Daya Mesin 1</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Daya Mesin 2</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Daya Mesin 3</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Daya Mesin 4</span><br /><i></i>
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
            <td>
                <input type="text" name="no_surat" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['no_surat'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
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