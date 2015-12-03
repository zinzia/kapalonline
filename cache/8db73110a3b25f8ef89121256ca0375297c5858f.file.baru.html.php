<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:28:44
         compiled from "application/views\task/izin_dirjen_int/baru.html" */ ?>
<?php /*%%SmartyHeaderCode:73475600e70c7e74f5-82081358%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8db73110a3b25f8ef89121256ca0375297c5858f' => 
    array (
      0 => 'application/views\\task/izin_dirjen_int/baru.html',
      1 => 1441883423,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '73475600e70c7e74f5-82081358',
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
            <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i>Request Letter Number</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp)));?>
</b></td>
    </tr>
    <tr>
        <td width='20%'>
            <span style="text-decoration: underline;">Tanggal Kirim</span><br /><i>Request Date</i>
        </td>
        <td width='30%'><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp)), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp)),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_date'])===null||$tmp==='' ? '' : $tmp))));?>
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
        <th width="15%">Status Persetujuan</th>
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
    <tr>
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
            <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='approved'){?>VALID<?php }else{ ?><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_approval'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_approval'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_approval']));?>
<?php }?>
        </td>
        <?php }?>
    </tr>
    <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
        <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['izin_id'], null, null);?>
    <?php }?>
    <?php }} else { ?>
    <tr>
        <td colspan="11">Data rute belum diinputkan!</td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
        <td align="center"><b><?php echo (($tmp = @$_smarty_tpl->getVariable('tot_frek')->value)===null||$tmp==='' ? 0 : $tmp);?>
X</b></td>
        <td colspan="2"></td>
    </tr>
</table>
<?php if ($_smarty_tpl->getVariable('is_used_score')->value!="2"){?>
<div class="content-dashboard">
    <h4>
        <a href="#" class="slot">
            Slot Clearance Attachment - ( <?php echo $_smarty_tpl->getVariable('total_slot')->value;?>
 ) Files
        </a>
    </h4>
    <div style="display: none;" class="slot-box">
        <table class="table-view" width="100%">
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_slot')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr>
                <td align="center" width='5%'><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
                <td width='25%'><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_subject'];?>
</td>
                <td width='20%'><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_number'];?>
</td>
                <td align="center" width='10%'><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['slot_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['slot_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['slot_date'],'ins')));?>
</td>
                <td width='20%'><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_desc'];?>
</td>
                <td align="center" width='20%'>
                    <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_rute/slot_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['slot_id']));?>
"><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_file_name'];?>
</a>
                </td>
            </tr>
            <?php }} else { ?>
            <tr>
                <td colspan="6" style="color: red;">Data slot clearance belum diinputkan!</td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<?php }?>
<div class="content-dashboard">
    <h4>
        <a href="#" class="files">
            File Attachment - ( <?php echo $_smarty_tpl->getVariable('total_files')->value;?>
 ) Files
        </a>
    </h4>
    <div style="display: none;" class="files-box">
            <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
            <table class="table-view" width="100%">
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
                    <td width='25%'>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['ref_name'];?>

                    </td>
                    <td width="35%">
                        <?php if ($_smarty_tpl->tpl_vars['data']->value['file_id']!=''){?>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_rute/files_download/').($_smarty_tpl->tpl_vars['data']->value['file_id']));?>
"><?php echo $_smarty_tpl->tpl_vars['data']->value['file_name'];?>
</a>
                        <?php }else{ ?>
                        Belum ada file yang di upload!
                        <?php }?>
                    </td>
                    <td width="5%" align='center'>
                        <input type="checkbox" name="izin_files[<?php echo $_smarty_tpl->tpl_vars['data']->value['file_id'];?>
]" <?php if ($_smarty_tpl->tpl_vars['data']->value['file_check']=='1'){?>checked="checked"<?php }?> value="1" />
                    </td>
                    <td width="15%" align='center'>
                        <?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['check_date'])===null||$tmp==='' ? '-' : $tmp);?>

                    </td>
                    <td width="15%" align='center'>
                        <?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['check_by'])===null||$tmp==='' ? '-' : $tmp);?>

                    </td>
                </tr>
                <?php }} ?>
            </table>
    </div>
</div>
<table class="table-input" width="100%">
    <tr style="font-weight: bold;">
        <td width="33%" style="vertical-align: top;">
            Catatan ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
 ( <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_iata_cd'];?>
 )
            <br />
            <div style="margin: 5px 0; padding: 5px; font-family: helvetica; font-weight: normal; font-size: 11px; height: 180px; overflow: auto; border: 1px solid #ddd;">
                <ul style="list-style: none;">
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_notes')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <li style="border-bottom: 1px solid #ddd;">
                        <span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['data']->value['catatan'];?>
</span>
                        <br />
                        <em>
                            <?php echo $_smarty_tpl->tpl_vars['data']->value['operator_name'];?>
 / <?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['note_date']);?>

                        </em>
                    </li>
                    <?php }} ?>
                </ul>
            </div>
        </td>
        <td width="33%" style="vertical-align: top;">
            <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_dirjen_int/catatan_process/');?>
" method="post">
                <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                <input type="hidden" name="process_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['process_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                Catatan Proses Permohonan
                <br />
                <div style="margin: 5px 0; padding: 5px; font-family: helvetica; font-weight: normal; font-size: 11px; height: 80px; overflow: auto; border: 1px solid #ddd;">
                    <ul style="list-style: none;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_process')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <li style="border-bottom: 1px solid #ddd;">
                            <span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['data']->value['catatan'];?>
</span>
                            <br />
                            <em>
                                <?php echo $_smarty_tpl->tpl_vars['data']->value['operator_name'];?>
 / <?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['mdd_finish']);?>

                            </em>
                        </li>
                        <?php }} ?>
                    </ul>
                </div>
                <textarea name="catatan" cols="44" rows="3" placeholder="Isikan catatan anda disini"><?php echo $_smarty_tpl->getVariable('detail')->value['catatan'];?>
</textarea>
                <br />
                <br />
                <input type="submit" value="Simpan" class="submit-button" />
            </form>
        </td>
        <td width="33%" style="vertical-align: top;">
            Catatan Tambahan Pada Surat
            <br />
            <div style="margin: 5px 0; padding: 5px; font-family: helvetica; font-weight: normal; font-size: 11px; height: 80px; overflow: auto; border: 1px solid #ddd;">
                <ul style="list-style: none;">
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_memos')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <li style="border-bottom: 1px solid #ddd;">
                        <span><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_dirjen_int/delete_memo_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['memo_id']));?>
" onclick="return confirm('Apakah anda yakin akan menghapus catatan tambahan tersebut?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.delete.png" alt="" /></a></span>
                        <span style="color: blue;"><?php echo $_smarty_tpl->tpl_vars['data']->value['memo'];?>
</span>
                        <br />
                        <em>
                            <?php echo $_smarty_tpl->tpl_vars['data']->value['operator_name'];?>
 / <?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['memo_date']);?>

                        </em>
                    </li>
                    <?php }} ?>
                </ul>
            </div>
        </td>
    </tr>
</table>
<div class="clear" style="margin-bottom: 5px;"></div>
<table class="table-input" width="100%">
    <tr style="font-weight: bold;">
        <td width="40%" style="vertical-align: top;">
            Hasil Telaah
            <br />
            <br />
            <p style="font-weight: normal;">
                Telah diupload : <br /><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_rute/telaah_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="button"><?php echo $_smarty_tpl->getVariable('telaah')->value['telaah_file'];?>
</a>
            </p>
            <p style="font-weight: normal;">
                Oleh : <br /><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('telaah')->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('telaah')->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('telaah')->value['operator_name'])))===null||$tmp==='' ? '-' : $tmp);?>
</b>
            </p>
            <p style="font-weight: normal;">
                Tanggal : <br /><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp)));?>
</b>
            </p>
        </td>
    </tr>
</table>
<div class="clear" style="margin-bottom: 5px;"></div>
<div class="content-dashboard">
    <h4>
        <a href="#" class="editorial">
            ( <?php echo (($tmp = @$_smarty_tpl->getVariable('total_redaksional')->value)===null||$tmp==='' ? '' : $tmp);?>
 ) Tembusan dan Kepada
        </a>
    </h4>
    <div style="display: none; overflow: auto;" class="editorial-box">
        <table class="table-view" width="100%" id="table_redaksional">
            <tbody id="redaksional">
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_redaksional')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                <tr>
                    <td width="100%"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['redaksional_nm'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                </tr>
            <?php }} else { ?>
                <tr id="default_empty">
                    <td>Tidak Ada Data Tembusan dan Kepada</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clear" style="margin-bottom: 5px;"></div>
<div class="navigation" style="background-color: #eee; padding: 10px;">
    <div class="navigation-button" style="width: 850px;">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_dirjen_int/preview_baru/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.normal.png" alt="" /> Preview Surat</a></li>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_rollback']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_dirjen_int/back_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ini ke <?php echo $_smarty_tpl->getVariable('prev')->value['role_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/arrow-left.png" alt="" /> Kirim ke <?php echo $_smarty_tpl->getVariable('prev')->value['role_nm'];?>
</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_reject']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_dirjen_int/reject_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan menolak semua data permohonan diatas?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.failed.png" alt="" /> Tolak Semua Permohonan</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_publish']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_dirjen_int/finish_baru_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan menyetujui Permohonan Diatas?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.ok.png" alt="" /> Terbitkan Izin Rute</a></li>
            <?php }?>
        </ul>
    </div>
    <div class="clear"></div>
</div>