<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:34:40
         compiled from "application/views\task/izin_staff_int/migrasi.html" */ ?>
<?php /*%%SmartyHeaderCode:184935600da60b31068-93098052%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '259232cecb7c7ff19e0c02f1a47a14a4218c32c3' => 
    array (
      0 => 'application/views\\task/izin_staff_int/migrasi.html',
      1 => 1441883427,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '184935600da60b31068-93098052',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // slot attachment
        $(".slot").click(function() {
            $(this).toggleClass('down');
            $('.slot-box').toggleClass('down');
            $('.slot-box').slideToggle(100);
            return false;
        });
        // files attachment
        $(".files").click(function() {
            $(this).toggleClass('down');
            $('.files-box').toggleClass('down');
            $('.files-box').slideToggle(100);
            return false;
        });
        $("#proses-all").change(function() {
            var proses = $("#proses-all").val();
            $(".proses-item").val(proses);
        });
        // editorial attachment
        $(".editorial").click(function() {
            $(this).toggleClass('down');
            $('.editorial-box').toggleClass('down');
            $('.editorial-box').slideToggle(100);
            return false;
        });
        // --
        $(".select_editorial").select2({
            placeholder: "-- Semua --",
            width: 650,
            allowClear: true
        });
    });
</script>
<style type="text/css">
    .select_editorial .select2-choice {
        width: 640px !important;
        font-weight: bold;
    }
    .select_editorial .select2-default {
        width: 640px !important;
        font-weight: bold;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Task Manager Izin Rute</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute');?>
"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('com_user')->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm']));?>
</a><span></span>
        <small><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('task')->value['task_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('task')->value['task_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('task')->value['task_nm']));?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute');?>
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
<table class="table-form" width="100%">
    <tr>
        <td colspan="4" align='center'>
            <b style="font-size: 12px;">
                PERMOHONAN <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm']));?>

            </b>
        </td>
    </tr>
    <tr>
        <td colspan="4" align='center'>
            <b style="font-size: 14px;">
                ( <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
 / <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_iata_cd'];?>
)
            </b>
        </td>
    </tr>
    <tr>
        <td colspan="4" align='center'>&nbsp;</td>
    </tr>
    <tr>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Surat Permohonan</span><br /><i>Request Letter Date</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter_date'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter_date'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter_date'])===null||$tmp==='' ? '' : $tmp))));?>
</b></td>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Kirim</span><br /><i>Request Date</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp))));?>
</b></td>
    </tr>
    <tr>
        <td width='20%'>
            <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i>Request Letter Number</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
        <td width='20%'>
            <span style="text-decoration: underline;">Pengirim</span><br /><i>Person In Charge</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['pengirim'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['pengirim'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['pengirim'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
    </tr>
    <tr>
        <td>
            <span style="text-decoration: underline;">Tipe</span><br /><i>Type</i>
        </td>
        <td colspan="3"><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['pax_cargo'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['pax_cargo'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['pax_cargo'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
    </tr>
</table>
<table class="table-form" width="100%">
    <tr>
        <td colspan="4" align='center'>
            <b style="font-size: 12px;">
                DETAIL PENERBITAN
            </b>
        </td>
    </tr>
    <tr>
        <td colspan="4" align='center'>&nbsp;</td>
    </tr>
    <tr>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Surat Diterbitkan</span><br /><i>Published Letter Date</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_date'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_date'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_date'])===null||$tmp==='' ? '' : $tmp))));?>
</b></td>
        <td width='20%'>
            <span style="text-decoration: underline;">Nomor Surat Terbit</span><br /><i>Published Letter Number</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
    </tr>
    <tr>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Aktif</span><br /><i>Active Date</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_start'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_start'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_start'])===null||$tmp==='' ? '' : $tmp))));?>
</b></td>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Berakhir</span><br /><i>Expired Number</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_end'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_end'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_valid_end'])===null||$tmp==='' ? '' : $tmp))));?>
</b></td>
    </tr>
</table>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/approved_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-view" width="100%">
        <tr>
            <th width="5%">No</th>
            <th width="10%">Rute</th>
            <th width="10%">Tipe Pesawat</th>
            <th width="10%">Nomor Penerbangan</th>
            <th width="10%">ETD <br />(Waktu Lokal)</th>
            <th width="10%">ETA <br />(Waktu Lokal)</th>
            <th width="5%">DOS</th>
            <th width="5%">RON</th>
            <th width="10%">Frekuensi</th>
            <th width="10%">Tanggal Efektif</th>
            <th width="15%">
                <select name="proses" id="proses-all">
                    <option value=""></option>
                    <option value="waiting">WAITING</option>
                    <option value="approved">VALID</option>
                    <option value="rejected">REJECT</option>
                </select>
            </th>
        </tr>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(0, null, null);?>
        <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable('', null, null);?>
        <?php $_smarty_tpl->tpl_vars['frekuensi'] = new Smarty_variable(0, null, null);?>
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>    
        <?php }?>
        <tr <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='rejected'){?>class="red-row"<?php }?>>
            <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
"><?php echo $_smarty_tpl->getVariable('no')->value;?>
.</td>
            <?php }?>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['rute_all'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['tipe'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['flight_no'];?>
</td>
            <td align="center"><?php echo substr($_smarty_tpl->tpl_vars['data']->value['etd'],0,5);?>
</td>
            <td align="center"><?php echo substr($_smarty_tpl->tpl_vars['data']->value['eta'],0,5);?>
</td>   
            <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['roon'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['frekuensi'];?>
X</td>
            <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['start_date'])===null||$tmp==='' ? '' : $tmp);?>
<br /><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['end_date'])===null||$tmp==='' ? '' : $tmp);?>
</td>
            <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
">
                <select name="izin_approval[<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
]"  class="proses-item">
                    <option value="waiting" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='waiting'){?>selected="selected"<?php }?>>WAITING</option>
                    <option value="approved" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='approved'){?>selected="selected"<?php }?>>VALID</option>
                    <option value="rejected" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='rejected'){?>selected="selected"<?php }?>>REJECT</option>
                </select>   
                <textarea placeholder="Isikan catatan anda disini" rows="3" cols="50" name="notes[<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
]" style="width: 128px; height: 30px;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['notes'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
            </td>
            <?php }?>
        </tr>
        <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
        <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['izin_id'], null, null);?>
        <?php }?>
        <?php }} else { ?>
        <tr>
            <td colspan="10">Data rute belum diinputkan!</td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
            <td align="center"><b><?php echo (($tmp = @$_smarty_tpl->getVariable('tot_frek')->value)===null||$tmp==='' ? 0 : $tmp);?>
X</b></td>
            <td align="center" colspan="2">
                <input type="submit" value="Simpan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>
<div class="clear" style="margin-bottom: 5px;"></div>
<div class="navigation" style="background-color: #eee; padding: 10px;">
    <div class="navigation-button" style="width: 850px;">
        <ul>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_revisi']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff_int/pending_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.notes.red.png" alt="" /> Revisi ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_publish']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff_int/finish_migrasi_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan menyetujui Permohonan Diatas?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.ok.png" alt="" /> OK</a></li>
            <?php }?>
        </ul>
    </div>
    <div class="clear"></div>
</div>
