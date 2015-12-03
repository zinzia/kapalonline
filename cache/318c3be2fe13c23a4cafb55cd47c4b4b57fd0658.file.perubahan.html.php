<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:20:55
         compiled from "application/views\task/izin_staff_int/perubahan.html" */ ?>
<?php /*%%SmartyHeaderCode:245975600e537573d99-93428202%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '318c3be2fe13c23a4cafb55cd47c4b4b57fd0658' => 
    array (
      0 => 'application/views\\task/izin_staff_int/perubahan.html',
      1 => 1441883427,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '245975600e537573d99-93428202',
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
<div id="table-data" style="margin: 0 0 5px 0; padding: 10px; border: 1px solid #ddd; font-weight: bold; font-size: 12px; font-family: helvetica;">
    <h4>Data Izin Rute Sebelumnya</h4>
    <table class="table-view" width="100%" style="font-weight: normal;">
        <tr>
            <th width="5%">No</th>
            <th width="10%">Rute</th>
            <th width="10%">Tipe Pesawat</th>
            <th width="10%">Nomor Penerbangan</th>
            <th width="10%">ETD <br />(Waktu Lokal)</th>
            <th width="10%">ETA <br />(Waktu Lokal)</th>
            <th width="10%">DOS</th>
            <th width="10%">RON</th>
            <th width="10%">Frekuensi</th>
            <th width="15%">Masa Berlaku</th>
        </tr>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(0, null, null);?>
        <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable('', null, null);?>
        <?php $_smarty_tpl->tpl_vars['frekuensi'] = new Smarty_variable(0, null, null);?>
        <?php  $_smarty_tpl->tpl_vars['data_old'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_old')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data_old']->key => $_smarty_tpl->tpl_vars['data_old']->value){
?>
        <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>    
        <?php }?>
        <tr>
            <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data_old']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
"><?php echo $_smarty_tpl->getVariable('no')->value;?>
.</td>
            <?php }?>

            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data_old']->value['rute_all'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data_old']->value['tipe'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data_old']->value['flight_no'];?>
</td>
            <td align="center"><?php echo substr($_smarty_tpl->tpl_vars['data_old']->value['etd'],0,5);?>
</td>
            <td align="center"><?php echo substr($_smarty_tpl->tpl_vars['data_old']->value['eta'],0,5);?>
</td>   

            <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data_old']->value['doop'])===null||$tmp==='' ? '-' : $tmp);?>
</td>

            <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data_old']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['data_old']->value['roon'];?>
</td>
            <?php }?>

            <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <?php if (!in_array($_smarty_tpl->getVariable('data')->value['flight_no'],$_smarty_tpl->getVariable('flight_no')->value)){?>
                <?php if (!isset($_smarty_tpl->tpl_vars['flight_no']) || !is_array($_smarty_tpl->tpl_vars['flight_no']->value)) $_smarty_tpl->createLocalArrayVariable('flight_no', null, null);
$_smarty_tpl->tpl_vars['flight_no']->value[] = $_smarty_tpl->getVariable('data')->value['flight_no'];?>
                <?php $_smarty_tpl->tpl_vars['frekuensi'] = new Smarty_variable($_smarty_tpl->getVariable('frekuensi')->value+$_smarty_tpl->tpl_vars['data_old']->value['frekuensi'], null, null);?>
            <?php }?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data_old']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['data_old']->value['frekuensi'];?>
X</td>
            <?php }?>  

            <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <td align="center" rowspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data_old']->value['rowspan'])===null||$tmp==='' ? '0' : $tmp);?>
">
                <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['start_date'],'ins')));?>
<br />
                <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data_old']->value['end_date'],'ins')));?>

            </td>
            <?php }?>
        </tr>
        <?php if ($_smarty_tpl->tpl_vars['data_old']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
        <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable($_smarty_tpl->tpl_vars['data_old']->value['izin_id'], null, null);?>
        <?php }?>
        <?php }} ?>
    </table>
</div>
<div id="table-data" style="margin: 0 0 5px 0; padding: 10px; border: 1px solid #ddd; font-weight: bold; font-size: 12px; font-family: helvetica;">
    <h4>Data Izin Rute Yang Diubah</h4>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/approved_process');?>
" method="post">
        <input type="hidden" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
" name="registrasi_id"/>
        <table class="table-view" width="100%" style="font-weight: normal;">
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
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, null);?>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(0, null, null);?>
            <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable('', null, null);?>
            <?php $_smarty_tpl->tpl_vars['frekuensi'] = new Smarty_variable(0, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_new')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>    
            <?php }?>
            <tr <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='rejected'){?>class="red-row"<?php }?>>
                <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
                <td align="center" rowspan="<?php echo $_smarty_tpl->tpl_vars['data']->value['rowspan'];?>
"><?php echo $_smarty_tpl->getVariable('no')->value;?>
.</td>
                <?php }?>

                <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['rute_all'];?>
</td>
                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['tipe']!=$_smarty_tpl->tpl_vars['data']->value['tipe']){?><b style="color: red;"><?php }?><?php echo $_smarty_tpl->tpl_vars['data']->value['tipe'];?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['tipe']!=$_smarty_tpl->tpl_vars['data']->value['tipe']){?></b><?php }?>
                </td>
                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['flight_no']!=$_smarty_tpl->tpl_vars['data']->value['flight_no']){?><b style="color: red;"><?php }?><?php echo $_smarty_tpl->tpl_vars['data']->value['flight_no'];?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['flight_no']!=$_smarty_tpl->tpl_vars['data']->value['flight_no']){?></b><?php }?>
                </td>
                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['etd']!=$_smarty_tpl->tpl_vars['data']->value['etd']){?><b style="color: red;"><?php }?><?php echo substr($_smarty_tpl->tpl_vars['data']->value['etd'],0,5);?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['etd']!=$_smarty_tpl->tpl_vars['data']->value['etd']){?></b><?php }?>
                </td>
                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['eta']!=$_smarty_tpl->tpl_vars['data']->value['eta']){?><b style="color: red;"><?php }?><?php echo substr($_smarty_tpl->tpl_vars['data']->value['eta'],0,5);?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['eta']!=$_smarty_tpl->tpl_vars['data']->value['eta']){?></b><?php }?>
                </td>   

                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['doop']!=$_smarty_tpl->tpl_vars['data']->value['doop']){?><b style="color: red;"><?php }?><?php echo $_smarty_tpl->tpl_vars['data']->value['doop'];?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['doop']!=$_smarty_tpl->tpl_vars['data']->value['doop']){?></b><?php }?>
                </td>

                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['roon']!=$_smarty_tpl->tpl_vars['data']->value['roon']){?><b style="color: red;"><?php }?><?php echo $_smarty_tpl->tpl_vars['data']->value['roon'];?>
<?php if ($_smarty_tpl->getVariable('rs_old')->value[$_smarty_tpl->getVariable('i')->value]['roon']!=$_smarty_tpl->tpl_vars['data']->value['roon']){?></b><?php }?>
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
                    </select><br/><br/>   
                    <textarea placeholder="Isikan catatan anda disini" rows="3" cols="50" name="notes[<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
]" style="width: 128px; height: 30px;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['notes'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
                </td>
                <?php }?>
            </tr>
            <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_id']!=$_smarty_tpl->getVariable('temp')->value){?>
            <?php $_smarty_tpl->tpl_vars['temp'] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['izin_id'], null, null);?>
            <?php }?>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
            <?php }} ?>
            <tr>
                <td colspan="9"></td><td align="center"><input type="submit" value="Simpan" class="submit-button" /></td>
            </tr>
            <tr>
            <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
            <td align="center"><b><?php echo (($tmp = @$_smarty_tpl->getVariable('tot_frek')->value)===null||$tmp==='' ? 0 : $tmp);?>
X</b></td>
            <td colspan="2" align="center">
                <input type="submit" value="Simpan" class="submit-button" />
            </td>
            </tr>
        </table>
    </form>
</div>
<table class="table-view" width="100%">
    <tr>
        <td colspan="6">
            <b>Slot Clearance Attachment :</b>
        </td>
    </tr>
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
<div id="table-data" style="margin: 0 0 5px 0; padding: 10px; border: 1px solid #ddd;font-size: 12px; font-family: helvetica;">
    <h3 style="font-family: helvetica; font-size: 12px;">Codeshare</h3>
    <table class="table-view" width="100%">
        <tr>
            <td width="5%" align="center"><b>No</b></td>
            <td width="20%" align="center"><b>Rute</b></td>
            <td width="15%" align="center"><b>Operating Flight No</b></td>
            <td width="15%" align="center"><b>Marketing Flight No</b></td>
            <td width="15%" align="center"><b>Airlines</b></td>
            <td width="10%" align="center"><b>ETD</b></td>
            <td width="10%" align="center"><b>ETA</b></td>
        </tr> 
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_codeshare')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <tr>
            <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['rute_all'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['ope_cxr'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['mkt_cxr'];?>
</td>
            <td align="left"><?php echo $_smarty_tpl->tpl_vars['data']->value['airlines_nm'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['etd'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['data']->value['eta'];?>
</td>
        </tr>
        <?php }} else { ?>
        <tr>
            <td colspan="7">Data sharecode belum diinputkan!</td>
        </tr>
        <?php } ?>
    </table>
</div>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/file_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-view" width="100%">
        <tr>
            <td colspan="6">
                <b>File Attachment :</b>
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
        <tr>
            <td colspan="6" align='right'>
                <input type="submit" name="save" value="Verifikasi Berkas Permohonan" />
            </td>
        </tr>
    </table>
</form>
<table class="table-input" width="100%">
    <tr style="font-weight: bold;">
        <td width="33%" style="vertical-align: top;">
            <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/airlines_notes_process/');?>
" method="post">
                <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                Catatan ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
 ( <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_iata_cd'];?>
 )
                <br />
                <div style="margin: 5px 0; padding: 5px; font-family: helvetica; font-weight: normal; font-size: 11px; height: 80px; overflow: auto; border: 1px solid #ddd;">
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
                <textarea name="catatan" cols="44" rows="3" placeholder="Isikan catatan anda disini"><?php echo $_smarty_tpl->getVariable('catatan_airlines')->value['catatan'];?>
</textarea>
                <br />
                <br />
                <input type="submit" value="Simpan" class="submit-button" />
            </form>
        </td>
        <td width="33%" style="vertical-align: top;">
            <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/catatan_process/');?>
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
            <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/memo_process/');?>
" method="post">
                <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                <input type="hidden" name="process_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['process_id'])===null||$tmp==='' ? '' : $tmp);?>
">
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
                            <span><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff_int/delete_memo_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['memo_id']));?>
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
                <textarea name="memo" cols="44" rows="3" placeholder="Isikan catatan tambahan anda disini"><?php echo $_smarty_tpl->getVariable('detail')->value['memo'];?>
</textarea>
                <br />
                <br />
                <input type="submit" value="Simpan" class="submit-button" />
            </form>
        </td>
    </tr>
</table>
<div class="clear" style="margin-bottom: 5px;"></div>
<table class="table-input" width="100%">
    <tr style="font-weight: bold;">
        <td width="40%" style="vertical-align: top;">
            <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff_int/telaah_process/');?>
" method="post" enctype="multipart/form-data">
                <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                Hasil Telaah Staff
                <br />
                <br />
                <input type="file" name="telaah_file" />
                <input type="submit" value="Upload" class="submit-button" />
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
            </form>
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
        <table class="table-view" width="100%">
            <tr>
                <td width="75%">
                    <select name="editorial" id="editorial" class="select_editorial">
                        <option value=""></option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_editorial')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['redaksional_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['data']->value['redaksional_nm'];?>
</option>
                        <?php }} ?>
                    </select>
                </td>
                <td width="25%" align='left'>
                    <input type="submit" name="save" value="Simpan" onclick="redaksional(<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
)" />
                </td>
            </tr>
        </table>
        <table class="table-view" width="100%" id="table_redaksional">
            <tbody id="redaksional">
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_redaksional')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                <tr>
                    <td width="90%"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['redaksional_nm'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                    <td width="10%" align="center"><a href="#" class="delete-row btn red btn-xs" onclick="delete_redaksional(<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['tembusan_id'])===null||$tmp==='' ? '' : $tmp);?>
)">Hapus</a></td>
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
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_staff_int/preview_perubahan/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.normal.png" alt="" /> Preview Surat</a></li>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_revisi']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff_int/pending_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.notes.red.png" alt="" /> Revisi ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_send']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff_int/send_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ini ke <?php echo $_smarty_tpl->getVariable('next')->value['role_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/arrow-right.png" alt="" /> Kirim ke <?php echo $_smarty_tpl->getVariable('next')->value['role_nm'];?>
</a></li>
            <?php }?>
        </ul>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $.ajaxSetup({
        cache: false
    });
    // append redaksional
    function redaksional(registrasi_id) {
        var get_redaksional_id = document.getElementById('editorial').value;
        var get_redaksional_index = document.getElementById('editorial').selectedIndex;
        var get_redaksional_text = document.getElementById('editorial').options[get_redaksional_index].text;
        if (get_redaksional_id != "") {
            var sendData = {
                registrasi_id: registrasi_id,
                editorial: get_redaksional_id
            };
            $.ajax({
                type: "POST",
                url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/task/izin_staff_int/redaksional_process/",
                dataType: "json",
                data: sendData,
                success: function (msg) {
                    if (msg) {
                        $( "tr#default_empty" ).remove();
                        $( "tbody#redaksional" ).append( "<tr><td width='90%'><input type='hidden' name='editorial[]' value='" + get_redaksional_text + "' />" + get_redaksional_text + "</td><td width='10%' align='center'><a href='#' class='delete-row btn red btn-xs' onclick='delete_redaksional(" + msg + ")'>Hapus</a></td></tr>" );
                    } else {
                        alert('Data tidak dapat ditambahkan');
                    }
                },
            });
        } else {
            alert("Silahkan pilih opsi yang telah disediakan");
        }
    }

    // delete redaksional
    function delete_redaksional(tembusan_id) {
        var sendData = {
            tembusan_id: tembusan_id,
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/task/izin_staff_int/delete_redaksional_process/",
            dataType: "json",
            data: sendData,
            success: function (msg) {
                if (msg) {
                    jQuery(this).closest('tr').fadeOut(function(){
                        jQuery(this).remove();
                    });
                } else {
                    alert('Data tidak dapat dihapus');
                }
            },
        });
    }

    // delete row
    jQuery('#table_redaksional').on('click', '.delete-row', function() {
        jQuery(this).closest('tr').fadeOut(function(){
            jQuery(this).remove();
        });
        return false;
    })
</script>
