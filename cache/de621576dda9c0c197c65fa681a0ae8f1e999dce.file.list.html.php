<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:24:46
         compiled from "application/views\izin_internasional/frekuensi_delete/list.html" */ ?>
<?php /*%%SmartyHeaderCode:176515600e61ee7aac6-38892848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de621576dda9c0c197c65fa681a0ae8f1e999dce' => 
    array (
      0 => 'application/views\\izin_internasional/frekuensi_delete/list.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176515600e61ee7aac6-38892848',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function () {
        // rute existing
        $(".drop-up").click(function () {
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
"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="active"><b>Step 2</b><br />Pengurangan Frekuensi Penerbangan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 3</b><br />File Attachment</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 4</b><br />Review Permohonan</a>
        </li>
    </ul>
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
                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_delete/index/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
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
<h3>
    <a href="#" class="drop-up">
        Rute Penerbangan Existing : 
    </a>
</h3>
<table class="table-view" width="100%">
    <tr class="blink-row">
        <td width="15%" align="center">Jumlah Penerbangan</td>
        <td width="9%" align="center"><b><?php echo $_smarty_tpl->getVariable('total_existing')->value['total_rute'];?>
</b></td>
        <td width="19%" align="center">Jumlah Frekuensi / Minggu</td>
        <td width="10%" align="center" style="color: #0055cc;"><b><?php echo $_smarty_tpl->getVariable('total_existing')->value['frekuensi'];?>
X</b></td>
        <td width="16%" align="center">Tanggal Efektif</td>
        <td width="31%" align="center">
            <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['start_date'],'ins')));?>
</b>
            s/d
            <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_existing')->value['end_date'],'ins')));?>
</b>
        </td>
    </tr>
</table>
<div class="clear"></div>
<div style="display: none; overflow: auto;" class="list-rute-box">
    <table class="table-view" width="100%">
        <tr style="font-weight: bold;">
            <td width="5%" align='center'>No</td>
            <td width="10%" align='center'>Rute</td>
            <td width="9%" align='center'>Tipe<br />Pesawat</td>
            <td width="9%" align='center'>Nomor<br />Penerbangan</td>
            <td width="10%" align='center'>ETD <br />(waktu lokal)</td>
            <td width="10%" align='center'>ETA <br />(waktu lokal)</td>
            <td width="8%" align='center'>Hari <br />Operasi</td>
            <td width="8%" align='center'>RON</td>
            <td width="8%" align='center'>Frekuensi</td>
            <td width="13%" align='center'>Tanggal <br />Efektif</td>
            <td width="10%" align='center'></td>
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
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
            <td align="center">
                <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]!='1'){?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_add_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']));?>
" class="button-edit">Delete</a>
                <?php }else{ ?>
                <b>Selected</b>
                <?php }?>
            </td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
">
                <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]!='1'){?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_add_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']));?>
" class="button-edit">Delete</a>
                <?php }else{ ?>
                <b>Selected</b>
                <?php }?>
            </td>
            <?php }?>
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
                <br />
                <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal_existing')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins')));?>
</b>
            </td>
            <td align="center">
            </td>
        </tr>
        <?php }?>
        <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
        <?php }} else { ?>
        <tr <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]=='1'){?>class="blink-row"<?php }?>>
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
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            <td align="center">
                <?php if ($_smarty_tpl->getVariable('rute_selected')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]!='1'){?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_add_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']));?>
" class="button-edit">Delete</a>
                <?php }else{ ?>
                Selected
                <?php }?>
            </td>
        </tr>
        <?php } ?>
        <?php }} else { ?>
        <tr>
            <td colspan="11">Data rute tidak ditemukan!</td>
        </tr>
        <?php } ?>
    </table>
</div>
<h3 style="font-size: 12px; color: : #4E4E4E;">
    Rute Penerbangan Yang Dicabut : 
</h3>
<table class="table-view" width="100%">
    <tr style="font-weight: bold;">
        <td width="5%" align='center'>No</td>
        <td width="10%" align='center'>Rute</td>
        <td width="9%" align='center'>Tipe<br />Pesawat</td>
        <td width="9%" align='center'>Nomor<br />Penerbangan</td>
        <td width="10%" align='center'>ETD <br />(waktu lokal)</td>
        <td width="10%" align='center'>ETA <br />(waktu lokal)</td>
        <td width="8%" align='center'>Hari <br />Operasi</td>
        <td width="8%" align='center'>RON</td>
        <td width="8%" align='center'>Frekuensi</td>
        <td width="13%" align='center'>Tanggal <br />Efektif</td>
        <td width="10%" align='center'></td>
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
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
        <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_delete/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['izin_id']));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus baris berikut ini!');">Cancel</a>
        </td>
        <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
        <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_delete/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['izin_id']));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus baris berikut ini!');">Cancel</a>
        </td>
        <?php }?>
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
            <br />
            <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('subtotal')->value[$_smarty_tpl->tpl_vars['data']->value['kode_frekuensi']]['end_date'],'ins')));?>
</b>
        </td>
        <td align="center">
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
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/frekuensi_delete/rute_delete/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['izin_id']));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus baris berikut ini!');">Cancel</a>
        </td>
    </tr>
    <?php } ?>
    <?php }} else { ?>
    <tr>
        <td colspan="11">
            <span style="color: red;">Input data rute menggunakan rute penerbangan existing diatas!</span>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="8" align="right">Perhitungan Jumlah Frekuensi / Minggu Yang Di Cabut</td>
        <td align="center"><b><?php echo $_smarty_tpl->getVariable('total')->value['frekuensi'];?>
X</b></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <td colspan="8" align="right">Sisa Jumlah Frekuensi Penerbangan</td>
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/frekuensi_delete/list_rute_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>
                Apakah anda yakin akan mencabut / mengurangi frekuensi penerbangan pada rute diatas?
            </td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan dan Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>