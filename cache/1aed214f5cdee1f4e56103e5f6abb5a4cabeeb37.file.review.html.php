<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:25:17
         compiled from "application/views\izin_internasional/frekuensi_delete/review.html" */ ?>
<?php /*%%SmartyHeaderCode:59855600e63d3ff4c7-90660013%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1aed214f5cdee1f4e56103e5f6abb5a4cabeeb37' => 
    array (
      0 => 'application/views\\izin_internasional/frekuensi_delete/review.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '59855600e63d3ff4c7-90660013',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran Izin Rute</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_internasional/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Draft List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/index/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Step 2</b><br />Pengurangan Frekuensi Penerbangan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/list_files/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><b>Step 3</b><br />File Attachment</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/review/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="active"><b>Step 4</b><br />Review Permohonan</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/list_files/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Previous Step</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/frekuensi_delete/send_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
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
            <td>
                <span style="text-decoration: underline;">Tanggal Surat Permohonan</span><br /><i>Request Letter Date</i>
            </td>
            <td><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
            <td width='20%'>
                <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i>Request Letter Number</i>
            </td>
            <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>
        <tr>
            <td width='20%'>
                <span style="text-decoration: underline;">Rute Pairing</span><br /><i>Pair Routes</i>
            </td>
            <td width='30%'><b><?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
 / <?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
</b></td>
            <td><span style="text-decoration: underline;">Penunjukkan Oleh</span><br /><i>Appointment By</i></td>
            <td><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>   
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Penerbangan</span><br /><i>Flight Type</i>
            </td>
            <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['pax_cargo'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['pax_cargo'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['pax_cargo']));?>
</b></td>
            <td><span style="text-decoration: underline;">Jabatan</span><br /><i>Job Title</i></td>
            <td><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['jabatan'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['jabatan'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['jabatan'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>  
        <tr>
            <td>
                <span style="text-decoration: underline;">Season Code</span><br /><i>For IASM Airport</i>
            </td>
            <td colspan="3"><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>
    </table>
    <div style="margin: 0 0 5px 0; padding: 10px; border: 1px solid #ddd; font-size: 11px; font-family: helvetica;">
        <h3 style="font-family: helvetica; font-size: 12px;">Data Rute Penerbangan Existing :</h3>
        <table class="table-view" width="100%">
            <tr style="font-weight: bold;">
                <td width="4%" align='center'>No</td>
                <td width="10%" align='center'>Rute</td>
                <td width="9%" align='center'>Tipe<br />Pesawat</td>
                <td width="9%" align='center'>Nomor<br />Penerbangan</td>
                <td width="10%" align='center'>ETD <br />(waktu lokal)</td>
                <td width="10%" align='center'>ETA <br />(waktu lokal)</td>
                <td width="8%" align='center'>Hari <br />Operasi</td>
                <td width="8%" align='center'>RON</td>
                <td width="8%" align='center'>Frekuensi</td>
                <td width="12%" align='center'>Tanggal <br />Mulai</td>
                <td width="12%" align='center'>Tanggal <br />Berakhir</td>
            </tr>
            <?php  $_smarty_tpl->tpl_vars['rute'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_existing')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['rute']->key => $_smarty_tpl->tpl_vars['rute']->value){
 $_smarty_tpl->tpl_vars['no']->value = $_smarty_tpl->tpl_vars['rute']->key;
?>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(1, null, null);?>
            <?php $_smarty_tpl->tpl_vars['rowspan'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['rute']->value), null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['rute']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]=='1'){?>class="red-row"<?php }?>>
                <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
                <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <?php }?>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['flight_no'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['etd'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['eta'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>   
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['roon'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            </tr>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value==$_smarty_tpl->getVariable('i')->value){?>
            <tr>
                <td colspan="8" align="right">Perhitungan Jumlah Frekuensi / Minggu</td>
                <td align="center">
                    <b><?php echo $_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['frekuensi'];?>
X</b>
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins')));?>
</b>
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins')));?>
</b>                    
                </td>
            </tr>
            <?php }?>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
            <?php }} else { ?>
            <tr <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]=='1'){?>class="red-row"<?php }?>>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['flight_no'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['etd'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['eta'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>  
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['roon'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            </tr>
            <?php } ?>
            <?php }} else { ?>
            <tr>
                <td colspan="11">Data rute tidak ditemukan!</td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="8" align="right">Perhitungan Total Jumlah Frekuensi / Minggu</td>
                <td align="center">
                    <b><?php echo $_smarty_tpl->getVariable('total_existing')->value['frekuensi'];?>
X</b>
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins')));?>
</b>
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins')));?>
</b>
                </td>
            </tr>
        </table>
    </div>
    <div style="margin: 0 0 5px 0; padding: 10px; border: 1px solid #ddd; font-size: 11px; font-family: helvetica;">
        <h3 style="font-family: helvetica; font-size: 12px;">Data Rute Penerbangan Yang Akan Dicabut</h3>
        <table class="table-view" width="100%">
            <tr style="font-weight: bold;">
                <td width="4%" align='center'>No</td>
                <td width="10%" align='center'>Rute</td>
                <td width="9%" align='center'>Tipe<br />Pesawat</td>
                <td width="9%" align='center'>Nomor<br />Penerbangan</td>
                <td width="10%" align='center'>ETD <br />(waktu lokal)</td>
                <td width="10%" align='center'>ETA <br />(waktu lokal)</td>
                <td width="8%" align='center'>Hari <br />Operasi</td>
                <td width="8%" align='center'>RON</td>
                <td width="8%" align='center'>Frekuensi</td>
                <td width="12%" align='center'>Tanggal <br />Mulai</td>
                <td width="12%" align='center'>Tanggal <br />Berakhir</td>
            </tr>
            <?php  $_smarty_tpl->tpl_vars['rute'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['rute']->key => $_smarty_tpl->tpl_vars['rute']->value){
 $_smarty_tpl->tpl_vars['no']->value = $_smarty_tpl->tpl_vars['rute']->key;
?>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(1, null, null);?>
            <?php $_smarty_tpl->tpl_vars['rowspan'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['rute']->value), null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['rute']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr>
                <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
                <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <?php }?>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['flight_no'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['etd'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['eta'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>   
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['roon'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            </tr>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value==$_smarty_tpl->getVariable('i')->value){?>
            <tr>
                <td colspan="8" align="right">Perhitungan Jumlah Frekuensi / Minggu</td>
                <td align="center">
                    <b><?php echo $_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['frekuensi'];?>
X</b>
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['start_date'],'ins')));?>
</b>                    
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins')));?>
</b>
                </td>
            </tr>
            <?php }?>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
            <?php }} else { ?>
            <tr>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['flight_no'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['etd'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center"><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['eta'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>  
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['roon'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            </tr>
            <?php } ?>
            <?php }} else { ?>
            <tr>
                <td colspan="11">Data rute tidak ditemukan!</td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="8" align="right">Perhitungan Jumlah Frekuensi / Minggu Yang Di Cabut</td>
                <td align="center"><b><?php echo $_smarty_tpl->getVariable('total')->value['frekuensi'];?>
X</b></td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins')));?>
</b>                    
                </td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins')));?>
</b>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="right">Total Jumlah Frekuensi Keseluruhan</td>
                <td align="center">
                    <b style="color: red;"><?php echo $_smarty_tpl->getVariable('total_existing')->value['frekuensi']-$_smarty_tpl->getVariable('total')->value['frekuensi'];?>
X</b>
                </td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
            <tr>
                <td colspan="8" align="right">Selisih Frekuensi / Minggu</td>
                <td align="center">
                    <b style="color: red;">-<?php ob_start();?><?php echo $_smarty_tpl->getVariable('total')->value['frekuensi'];?>
<?php $_tmp1=ob_get_clean();?><?php echo $_tmp1;?>
X</b>
                </td>
                <td align="center">
                </td>
                <td align="center">
                </td>
            </tr>
        </table>
    </div>
    <table class="table-form" width="100%">
        <tr>
            <td colspan="3">
                <b>File persyaratan yang wajib di sertakan adalah sebagai berikut :</b>
            </td>
        </tr>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <tr>
            <td width='5%' align="center">
                <?php echo $_smarty_tpl->getVariable('no')->value++;?>
.
            </td>
            <td width='35%'>
                <?php echo $_smarty_tpl->tpl_vars['data']->value['ref_name'];?>

            </td>
            <td width="60%">
                <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['ref_id'],$_smarty_tpl->getVariable('file_uploaded')->value)){?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('member/monitoring_izin/files_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['ref_id']));?>
"><?php echo $_smarty_tpl->getVariable('name_uploaded')->value[$_smarty_tpl->tpl_vars['data']->value['ref_id']];?>
</a>
                <?php }else{ ?>
                -
                <?php }?>
            </td>
        </tr>
        <?php }} ?>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td colspan="2">
                Apakah anda yakin dengan data permohonan diatas?
            </td>
            <td colspan="2" align='right'>
                <input type="submit" name="save" value="Kirim Permohonan" class="reset-button" onclick="return confirm('Apakah anda yakin akan mengirim data permohonan diatas?')" />
            </td>
        </tr>
    </table>
</form>