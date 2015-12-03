<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 10:07:06
         compiled from "application/views\izin_domestik/perubahan/list_izin_data.html" */ ?>
<?php /*%%SmartyHeaderCode:2165256010c2acf1db0-95497093%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27f82de6049748deef32c65d9a8c09fefa27a63f' => 
    array (
      0 => 'application/views\\izin_domestik/perubahan/list_izin_data.html',
      1 => 1441883441,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2165256010c2acf1db0-95497093',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // timepicker
        $('.waktu').timepicker({
            showPeriodLabels: false
        });
        // date picker
        $(".tanggal").datepicker({
            showOn: 'focus',
            changeMonth: true,
            changeYear: true,
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: false,
            dateFormat: 'yy-mm-dd',
        });
        // rute existing
        $(".drop-up").click(function() {
            $(this).toggleClass('drop-down');
            $('.list-rute-box').toggleClass('down');
            $('.list-rute-box').slideToggle(100);
            return false;
        });
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran Izin Rute</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">
                    Surat Permohonan : <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '-' : $tmp);?>
</span>, 
                    <br />
                    Tanggal : <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '-' : $tmp);?>
</span>, ( For <?php echo $_smarty_tpl->getVariable('detail')->value['izin_season'];?>
 )
                    <br />
                    Rute : <b><?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
 / <?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
</b>
                    <br />
                    <?php if ($_smarty_tpl->getVariable('rute')->value['pairing']=='VV'){?>
                    Jenis Layanan : <b>Vice Versa ( VV )</b>
                    <?php }elseif($_smarty_tpl->getVariable('rute')->value['pairing']=='OW'){?>
                    Jenis Layanan : <b>One Way Services (OW)</b>
                    <?php }else{ ?>
                    Jenis layanan penerbangan otomatis diupdate berdasarkan data pairing dibawah ini!
                    <?php }?>
                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_domestik/perubahan/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Step 2 : List Data Penerbangan</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<h3 style="color: #0055cc; font-size: 12px;">Rute Penerbangan Existing : </h3>
<table class="table-view" width="100%">
    <tr style="font-weight: bold;">
        <td width="4%" align='center'>No</td>
        <td width="8%" align='center'>Rute</td>
        <td width="8%" align='center'>Tipe Pesawat</td>
        <td width="8%" align='center'>Kapasitas Pesawat</td>
        <td width="8%" align='center'>Nomor Penerbangan</td>
        <td width="8%" align='center'>ETD <br />(waktu lokal)</td>
        <td width="8%" align='center'>ETA <br />(waktu lokal)</td>
        <td width="8%" align='center'>Hari Operasi</td>
        <td width="6%" align='center'>RON</td>
        <td width="8%" align='center'>Frekuensi</td>
        <td width="9%" align='center'>Start</td>
        <td width="9%" align='center'>End</td>
        <td width="9%" align='center'></td>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_existing')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
    <tr>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['capacity'])===null||$tmp==='' ? '-' : $tmp);?>
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
        <td align="center"></td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="13">Data rute belum diinputkan!</td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="9" align="right">Perhitungan Jumlah Frekuensi / Minggu</td>
        <td align="center"><b><?php echo $_smarty_tpl->getVariable('total_existing')->value['frekuensi'];?>
X</b></td>
        <td align="center"><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'))))===null||$tmp==='' ? '-' : $tmp);?>
</b></td>
        <td align="center"><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'))))===null||$tmp==='' ? '-' : $tmp);?>
</b></td>
        <td align="center"></td>
    </tr>
</table>
<h3 style="color: #0055cc; font-size: 12px;">Rute Penerbangan Yang Diajukan : </h3>
<table class="table-view" width="100%">
    <tr style="font-weight: bold;">
        <td width="4%" align='center'>No</td>
        <td width="8%" align='center'>Rute</td>
        <td width="8%" align='center'>Tipe Pesawat</td>
        <td width="8%" align='center'>Kapasitas Pesawat</td>
        <td width="8%" align='center'>Nomor Penerbangan</td>
        <td width="8%" align='center'>ETD <br />(waktu lokal)</td>
        <td width="8%" align='center'>ETA <br />(waktu lokal)</td>
        <td width="8%" align='center'>Hari Operasi</td>
        <td width="6%" align='center'>RON</td>
        <td width="8%" align='center'>Frekuensi</td>
        <td width="9%" align='center'>Start</td>
        <td width="9%" align='center'>End</td>
        <td width="9%" align='center'></td>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
    <tr>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rute_all'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tipe'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['capacity'])===null||$tmp==='' ? '-' : $tmp);?>
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
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((((('izin_domestik/perubahan/izin_data_delete_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['izin_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['rute_id']));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus baris berikut ini!');">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="13">Data rute belum diinputkan!</td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="9" align="right">Perhitungan Jumlah Frekuensi / Minggu</td>
        <td align="center"><b><?php echo $_smarty_tpl->getVariable('total')->value['frekuensi'];?>
X</b></td>
        <td align="center"><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'))))===null||$tmp==='' ? '-' : $tmp);?>
</b></td>
        <td align="center"><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'))))===null||$tmp==='' ? '-' : $tmp);?>
</b></td>
        <td align="center"></td>
    </tr>
</table>
<?php if ($_smarty_tpl->getVariable('rute')->value['is_used_score']==2){?>
<!-- include html -->
<?php $_template = new Smarty_Internal_Template("izin_domestik/perubahan/form_iasm.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of include html -->
<?php }?>
<?php if ($_smarty_tpl->getVariable('rute')->value['is_used_score']==1){?>
<!-- include html -->
<?php $_template = new Smarty_Internal_Template("izin_domestik/perubahan/form_mixed.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of include html -->
<?php }?>
<?php if ($_smarty_tpl->getVariable('rute')->value['is_used_score']==0){?>
<h4>Gunakan form dibawah ini untuk memasukkan data rute penerbangan</h4>
<!-- include html -->
<?php $_template = new Smarty_Internal_Template("izin_domestik/perubahan/form_non_iasm.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of include html -->
<?php }?>