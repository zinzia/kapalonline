<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 05:30:26
         compiled from "application/views\task/izin_staff/perubahan.html" */ ?>
<?php /*%%SmartyHeaderCode:277555600cb5271aff4-38589575%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab8c58d2dfcf0d26a9f9f3503e81f24ebe46be91' => 
    array (
      0 => 'application/views\\task/izin_staff/perubahan.html',
      1 => 1442892347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '277555600cb5271aff4-38589575',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!-- include javascript -->
<?php $_template = new Smarty_Internal_Template("task/izin_rute/task_javascript.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of include javascript-->
<script type="text/javascript">
    $(document).ready(function () {
        // select all
        $("#proses-all").change(function () {
            var proses = $("#proses-all").val();
            $(".proses-item").val(proses);
        });
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Task Manager Izin Rute</a><span></span>
        <a href="#"><?php echo $_smarty_tpl->getVariable('task')->value['task_nm'];?>
</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['group_nm']));?>
</b> : <?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
 / <?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
 ( <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['izin_season'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['izin_season'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['izin_season']));?>
 )
                    <br />
                    Surat Permohonan : <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('detail')->value['izin_request_letter'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('detail')->value['izin_request_letter'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('detail')->value['izin_request_letter'])))===null||$tmp==='' ? '-' : $tmp);?>
, <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '-' : $tmp);?>

                    <br />
                    Pengirim : <?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['pengirim'])===null||$tmp==='' ? '-' : $tmp);?>
, <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_date']))===null||$tmp==='' ? '-' : $tmp);?>

                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back to Waiting List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="navigation" style="background-color: #eee; padding: 10px;">
    <div class="navigation-button">
        <ul>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_revisi']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff/pending_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.notes.red.png" alt="" /> Revisi ke <?php echo $_smarty_tpl->getVariable('detail')->value['airlines_nm'];?>
</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('action')->value['action_send']=='1'){?>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_staff/send_process/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->getVariable('detail')->value['process_id']));?>
" onclick="return confirm('Apakah anda yakin akan mengirimkan data berikut ini ke <?php echo $_smarty_tpl->getVariable('next')->value['role_nm'];?>
?')"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/arrow-right.png" alt="" /> Kirim ke <?php echo $_smarty_tpl->getVariable('next')->value['role_nm'];?>
</a></li>
            <?php }?>  
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="rute-box">
    <h5>
        <a href="#" class="drop-up">
            Rute Penerbangan Sebelumnya :
        </a>
    </h5>
    <table class="table-view" width="100%">
        <tr style="font-weight: bold;">
            <td width="4%" align='center'>No</td>
            <td width="8%" align='center'>Rute</td>
            <td width="7%" align='center'>Tipe<br />Pesawat</td>
            <td width="8%" align='center'>Nomor<br />Penerbangan</td>
            <td width="7%" align='center'>ETD <br />( LT )</td>
            <td width="7%" align='center'>ETA <br />( LT )</td>
            <td width="8%" align='center'>Hari <br />Operasi</td>
            <td width="8%" align='center'>Frekuensi</td>
            <td width="8%" align='center'>Total <br />Frekuensi</td>
            <td width="18%" align='center'>Tanggal <br />Efektif</td>
            <td width="12%" align='center'></td>
            <td width="5%"></td>
        </tr>
        <?php  $_smarty_tpl->tpl_vars['rute'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_old')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
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
                <?php echo $_smarty_tpl->getVariable('pairing_old')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
 / 
                <?php echo $_smarty_tpl->getVariable('frekuensi_old')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
X
            </td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
" style="color: blue;">
                <?php echo $_smarty_tpl->getVariable('pairing_old')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
 / 
                <?php echo $_smarty_tpl->getVariable('frekuensi_old')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']];?>
X
            </td>
            <?php }?>
            <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['start_date'],'ins')));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
            <td align="center"></td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
"></td>
            <?php }?>
            <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
            <td align="center"></td>
            <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
            <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
"></td>
            <?php }?>
        </tr>
        <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
        <?php }} else { ?>
        <tr>
            <td align="center" style="color: black;"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
            <td colspan="11">Data rute tidak ditemukan!</td>
        </tr>
        <?php } ?>
        <?php }} else { ?>
        <tr>
            <td colspan="12">Data rute tidak ditemukan!</td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
            <td align="center"><b><?php echo $_smarty_tpl->getVariable('total_old')->value;?>
X</b></td>
            <td align="center"></td>
            <td align="center"></td>
            <td></td>
        </tr>
    </table>
</div>
<div class="rute-box">
    <h5>
        <a href="#" class="drop-up">
            Perubahan Rute Penerbangan Yang Diajukan :
        </a>
    </h5>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_staff/approved_process');?>
" method="post">
        <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
        <table class="table-view" width="100%">
            <tr style="font-weight: bold;">
                <td width="4%" align='center'>No</td>
                <td width="8%" align='center'>Rute</td>
                <td width="7%" align='center'>Tipe<br />Pesawat</td>
                <td width="8%" align='center'>Nomor<br />Penerbangan</td>
                <td width="7%" align='center'>ETD <br />( LT )</td>
                <td width="7%" align='center'>ETA <br />( LT )</td>
                <td width="8%" align='center'>Hari <br />Operasi</td>
                <td width="8%" align='center'>Frekuensi</td>
                <td width="8%" align='center'>Total <br />Frekuensi</td>
                <td width="18%" align='center'>Tanggal <br />Efektif</td>
                <td width="12%" align='center'>
                    <select name="proses" id="proses-all">
                        <option value=""></option>
                        <option value="waiting">Waiting</option>
                        <option value="approved">Valid</option>
                        <option value="rejected">Reject</option>
                    </select>
                </td>
                <td width="5%"></td>
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
 / <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['end_date'],'ins')));?>
</td>
                <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
                <td align="center">
                    <select name="izin_approval[<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
]" class="proses-item">
                        <option value="waiting" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='waiting'){?>selected="selected"<?php }?>>Waiting</option>
                        <option value="approved" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='approved'){?>selected="selected"<?php }?>>Valid</option>
                        <option value="rejected" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='rejected'){?>selected="selected"<?php }?>>Reject</option>
                    </select>   
                </td>
                <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
                <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
">
                    <select name="izin_approval[<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
]" class="proses-item">
                        <option value="waiting" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='waiting'){?>selected="selected"<?php }?>>Waiting</option>
                        <option value="approved" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='approved'){?>selected="selected"<?php }?>>Valid</option>
                        <option value="rejected" <?php if ($_smarty_tpl->tpl_vars['data']->value['izin_approval']=='rejected'){?>selected="selected"<?php }?>>Reject</option>
                    </select>   
                </td>
                <?php }?>
                <?php if ($_smarty_tpl->getVariable('rowspan')->value<=1){?>
                <td align="center">
                    <a href="#" id="notes_<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
" <?php if ($_smarty_tpl->getVariable('notes')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']]!=''){?>class="button-notes-red"<?php }else{ ?>class="button-notes"<?php }?>></a>
                </td>
                <?php }elseif($_smarty_tpl->getVariable('i')->value==1){?>
                <td align="center" rowspan="<?php echo $_smarty_tpl->getVariable('rowspan')->value;?>
">
                    <a href="#" id="notes_<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['data']->value['izin_id'];?>
" <?php if ($_smarty_tpl->getVariable('notes')->value[$_smarty_tpl->tpl_vars['data']->value['izin_id']]!=''){?>class="button-notes-red"<?php }else{ ?>class="button-notes"<?php }?>></a>
                </td>
                <?php }?>
            </tr>
            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('i')->value+1, null, null);?>
            <?php }} else { ?>
            <tr>
                <td align="center" style="color: black;"><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
.</td>
                <td colspan="11">Data rute belum diinputkan!</td>
            </tr>
            <?php } ?>
            <?php }} else { ?>
            <tr>
                <td colspan="12">Data rute belum diinputkan!</td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                <td align="center"><b><?php echo $_smarty_tpl->getVariable('total')->value['frekuensi'];?>
X</b></td>
                <td align="center">
                    <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['start_date'],'ins')));?>
</b> / <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total')->value['end_date'],'ins')));?>
</b>
                </td>
                <td align="center">
                    <input type="submit" value="Simpan" class="submit-button" />
                </td>
                <td></td>
            </tr>
            <tr style="color: blue; background-color: #FFEFEF;">
                <td colspan="8" align="right">Perhitungan Jumlah Frekuensi / Minggu Yang Disetujui</td>
                <td align="center"><b><?php echo $_smarty_tpl->getVariable('total_approved')->value['frekuensi'];?>
X</b></td>
                <td align="center">
                    <?php if ($_smarty_tpl->getVariable('total_approved')->value['start_date']!=$_smarty_tpl->getVariable('total_approved')->value['valid_start_date']){?>
                    <b style="color: red;">
                        <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['valid_start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['valid_start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['valid_start_date'],'ins')));?>

                    </b>
                    <?php }else{ ?>
                    <b>
                        <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['start_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['start_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['start_date'],'ins')));?>

                    </b>
                    <?php }?>
                    / 
                    <b>
                        <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['end_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['end_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('total_approved')->value['end_date'],'ins')));?>

                    </b>
                </td>
                <td></td>
                <td></td>
            </tr>
            <?php if ($_smarty_tpl->getVariable('total_approved')->value['frekuensi']!=''){?>
            <?php if ($_smarty_tpl->getVariable('total_old')->value!=$_smarty_tpl->getVariable('total_approved')->value['frekuensi']){?>
            <tr>
                <td colspan="8" align="right">Terdapat Perbedaan Jumlah Frekuensi Dari Rute Sebelumnya!</td>
                <td align="center">
                    <b style="color: red;">
                        <?php $_smarty_tpl->tpl_vars['selisih'] = new Smarty_variable(($_smarty_tpl->getVariable('total')->value['frekuensi']-$_smarty_tpl->getVariable('total_old')->value), null, null);?>
                        <?php if ($_smarty_tpl->getVariable('selisih')->value<0){?>
                        ( <?php echo $_smarty_tpl->getVariable('selisih')->value;?>
 )
                        <?php }else{ ?>
                        +<?php echo $_smarty_tpl->getVariable('selisih')->value;?>

                        <?php }?>
                    </b>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php if ($_smarty_tpl->getVariable('selisih')->value>0){?>
            <tr>
                <td colspan="8" align="right">Perhitungan Biaya Yang Harus Dibayarkan</td>
                <td align="right" colspan="2" style="background-color: #FFFDD0;">
                    <b style="float: left; margin-left: 25px;">Rp. </b>
                    <b><?php echo number_format(($_smarty_tpl->getVariable('selisih')->value*$_smarty_tpl->getVariable('tarif')->value),'0',',','.');?>
</b>
                </td>
                <td></td>
                <td></td>
            </tr>
            <?php }?>
            <?php }?>
            <?php }?>
        </table>
    </form>
</div>
<div class="action">
    <div class="action-button">
        <ul>
            <li><a href="#" title="<?php echo $_smarty_tpl->getVariable('detail')->value['registrasi_id'];?>
" id="button-catatan"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/document_edit.png" alt="" />( <?php echo $_smarty_tpl->getVariable('total_catatan')->value;?>
 ) Catatan Proses Permohonan</a></li>
            <li><a href="#" title="<?php echo $_smarty_tpl->getVariable('detail')->value['registrasi_id'];?>
" id="button-telaah"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/finished_work.png" alt="" /> ( <?php if ($_smarty_tpl->getVariable('telaah')->value['telaah_file']!=''){?> telah di-upload <?php }else{ ?> belum di-upload <?php }?> ) Telaah Staff</a></li>
            <li><a href="#" title="<?php echo $_smarty_tpl->getVariable('detail')->value['registrasi_id'];?>
" id="button-slot"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.permit.png" alt="" /> Slot Clearance</a></li>
            <li><a href="#" title="<?php echo $_smarty_tpl->getVariable('detail')->value['registrasi_id'];?>
" id="button-files"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/todolist.png" alt="" />  Files Attachment</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_rute/preview_perubahan/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.failed.png" /> Draft Surat Penerbitan</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- include html -->
<?php $_template = new Smarty_Internal_Template("task/izin_rute/task_form_dialog.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of include html -->