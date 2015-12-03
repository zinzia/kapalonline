<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 16:10:48
         compiled from "application/views\izin_pending_domestik/migrasi/review.html" */ ?>
<?php /*%%SmartyHeaderCode:17458565720f82f4097-12035348%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0f00a83e7202a4d1bb2184ddc98218f356703386' => 
    array (
      0 => 'application/views\\izin_pending_domestik/migrasi/review.html',
      1 => 1443841850,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17458565720f82f4097-12035348',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Permohonan Direvisi</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/pending_izin');?>
">Izin Rute Penerbangan</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/pending_izin');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Waiting List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_pending_domestik/migrasi/index/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_pending_domestik/migrasi/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><b>Step 2</b><br />Data Rute Penerbangan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_pending_domestik/migrasi/review/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="active"><b>Step 3</b><br />Review Permohonan</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<div class="dashboard">
    <table class="table-form" width="100%">
        <tr>
            <td><h3>Catatan Perbaikan</h3></td>
        </tr>
        <tr>
            <td>
                <?php if ($_smarty_tpl->getVariable('catatan')->value!=''){?>
                <p style="color: #B72C0F; font-size: 12px;"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('catatan')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('catatan')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('catatan')->value));?>
</p>
                <?php }else{ ?>
                Tidak ada catatan yang diinputkan.
                <?php }?>
            </td>
        </tr>
    </table>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul> 
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_pending_domestik/migrasi/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Previous Step</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_pending_domestik/migrasi/send_process');?>
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
            <td width='15%'>
                <span style="text-decoration: underline;">Rute Pairing</span><br /><i>Pair Routes</i>
            </td>
            <td width='35%'>
                <b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_start'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_start'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_start'])===null||$tmp==='' ? '' : $tmp)));?>
</b> / <b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp)));?>
</b>
            </td>
            <td width='20%'>
                <span style="text-decoration: underline;">Tanggal Surat Diterbitkan</span><br /><i>Published Letter Date</i>
            </td>
            <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_published_date']))===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_published_date']))===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_published_date']))===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Penerbangan</span><br /><i>Flight Type</i>
            </td>
            <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['pax_cargo'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['pax_cargo'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['pax_cargo']));?>
</b></td>
            <td>
                <span style="text-decoration: underline;">Nomor Surat Terbit</span><br /><i>Published Letter Number</i>
            </td>
            <td><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        </tr>
    </table>
    <table class="table-view" width="100%">
        <tr style="font-weight: bold;">
            <td width="4%" align='center'>No</td>
            <td width="9%" align='center'>Rute</td>
            <td width="9%" align='center'>Tipe<br />Pesawat</td>
            <td width="9%" align='center'>Nomor<br />Penerbangan</td>
            <td width="9%" align='center'>ETD <br /><small>Waktu Lokal</small></td>
            <td width="9%" align='center'>ETA <br /><small>Waktu Lokal</small></td>
            <td width="9%" align='center'>Hari <br />Operasi</td>
            <td width="9%" align='center'>Frekuensi</td>
            <td width="9%" align='center'>Total <br />Frekuensi</td>
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
        <tr <?php echo $_smarty_tpl->getVariable('row_style')->value[$_smarty_tpl->tpl_vars['data']->value['rute_all']];?>
>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
            <td align="center" style="color: black;"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
" style="color: black;"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
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
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
            <td align="center" style="color: blue;">
                <?php echo $_smarty_tpl->getVariable('pairing')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
 / 
                <?php echo $_smarty_tpl->getVariable('frekuensi')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
X
            </td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
" style="color: blue;">
                <?php echo $_smarty_tpl->getVariable('pairing')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
 / 
                <?php echo $_smarty_tpl->getVariable('frekuensi')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
X
            </td>
            <?php }?>
            <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
</td>
            <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
        </tr>        
        <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>        
        <?php }} ?>
        <?php }} else { ?>
        <tr>
            <td colspan="11">Data rute belum diinputkan!</td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="8" align="right">Perhitungan Total Jumlah Frekuensi / Minggu</td>
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