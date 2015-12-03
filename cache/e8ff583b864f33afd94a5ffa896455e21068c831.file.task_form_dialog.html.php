<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 05:30:26
         compiled from "application/views\task/izin_rute/task_form_dialog.html" */ ?>
<?php /*%%SmartyHeaderCode:277455600cb52cfafd1-17406783%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e8ff583b864f33afd94a5ffa896455e21068c831' => 
    array (
      0 => 'application/views\\task/izin_rute/task_form_dialog.html',
      1 => 1442892347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '277455600cb52cfafd1-17406783',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_capitalize')) include 'C:\xampp\htdocs\development\angudonline\system\plugins\smarty\libs\plugins\modifier.capitalize.php';
?><!-- form notes -->
<div id="form-notes" style="display: none;">
    <form id='form-tambah-notes' method='post' action ='#'>
        <input type="hidden" name="izin_id" id="notes_izin_id" value="" />
        <table class='table-input' width='100%'>
            <tr>
                <td align="center"><input type='text' name='notes' value='' size='50' maxlength='100' id="notes_value" style="text-align: center;" /></td>
            </tr>
            <tr>
                <td align="center">
                    <input type='submit' value='Simpan' class='submit-button' style="float: none;" />
                </td>
            </tr>
        </table>
        <div id="ajax-loader-notes" style="display: none; margin: 0 auto;">
            <br />
            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/ajax-loader-star.gif" alt="" style="height: 18px; vertical-align: middle;" />
            <small>Please Wait</small>
        </div>
    </form>
</div>
<!-- form catatan permohonan -->
<div id="form-catatan" style="display: none;">
    <div style="height: 170px; overflow: auto; margin-bottom: 10px;" id="form-catatan-list">
        <table class='table-input' width='100%' style="font-family: tahoma; font-size: 11px;">
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_process')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr>
                <td width='5%' align='center'><?php echo $_smarty_tpl->getVariable('no')->value++;?>
</td>
                <td width='40%'><span style="text-decoration: underline;"><?php echo smarty_modifier_capitalize(((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->tpl_vars['data']->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->tpl_vars['data']->value['operator_name'])));?>
</span>,<br /><?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['mdd_finish'],'ins');?>
</td>
                <td width='55%' style="color: #0055cc;"><?php echo $_smarty_tpl->tpl_vars['data']->value['catatan'];?>
</td>
            </tr>
            <?php }} else { ?>
            <tr>
                <td>
                    Belum ada catatan yang diinputkan!
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <form id='form-tambah-catatan' method='post' action ='#'>
        <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
        <input type="hidden" name="process_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['process_id'])===null||$tmp==='' ? '' : $tmp);?>
">
        <table class='table-input' width='100%'>
            <tr>
                <td align="center">
                    <p style="margin: 0 0 5px 0; padding: 0; font-family: tahoma; font-size: 11px;">Catatan saya :</p>
                    <textarea name="catatan" cols="70" rows="2" placeholder="Isikan catatan anda disini" id="catatan_value" style="font-family: tahoma; font-size: 11px;"><?php echo $_smarty_tpl->getVariable('detail')->value['catatan'];?>
</textarea>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input type='submit' value='Simpan' class='submit-button' style="float: none;" />
                </td>
            </tr>
        </table>
        <div id="ajax-loader-catatan" style="display: none; margin: 0 auto;">
            <br />
            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/ajax-loader-star.gif" alt="" style="height: 18px; vertical-align: middle;" />
            <small>Please Wait</small>
        </div>
    </form>
</div>
<!-- form telaah staff -->
<div id="form-telaah" style="display: none;">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute/telaah_process/');?>
" method="post" enctype="multipart/form-data">
        <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
        <input type="hidden" name="url_path" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('url_path')->value)===null||$tmp==='' ? '' : $tmp);?>
">
        <?php if ($_smarty_tpl->getVariable('com_user')->value['role_id']!='45'&&$_smarty_tpl->getVariable('com_user')->value['role_id']!='63'&&$_smarty_tpl->getVariable('com_user')->value['role_id']!='44'){?>
        <table class='table-input' width='100%' style="font-family: tahoma; font-size: 11px;">
            <tr>
                <td width='100%'>
                    <input type="file" name="telaah_file" />
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input type='submit' value='Simpan' class='submit-button' style="float: none;" />
                </td>
            </tr>
        </table>
        <?php }?>
        <table class='table-input' width='100%' style="font-family: tahoma; font-size: 11px;">
            <tr>
                <td>
                    Telah diupload : <br />
                    <?php if ($_smarty_tpl->getVariable('telaah')->value!=''){?>
                    <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_rute/telaah_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" style="color: #0074cc;"><?php echo $_smarty_tpl->getVariable('telaah')->value['telaah_file'];?>
</a>
                    <?php }else{ ?>
                    -
                    <?php }?>
                </td>
            </tr>
            <tr>
                <td>
                    Oleh : <br /><b><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('telaah')->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('telaah')->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('telaah')->value['operator_name'])))===null||$tmp==='' ? '-' : $tmp);?>
</b>
                </td>
            </tr>
            <tr>
                <td>
                    Tanggal : <br /><b><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('telaah')->value['telaah_date']))===null||$tmp==='' ? '-' : $tmp)));?>
</b>                    
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- form slot -->
<div id="form-slot" style="display: none;">
    <div style="height: 345px; overflow: auto;">
        <p style="margin: 0; padding: 5px; font-size: 11px; font-weight: bold;">
            Slot Attachment ( Non IASM )
        </p>
        <table class="table-view" width="100%">
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_slot')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr>
                <td align="center" width='8%'><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
                <td width='37%'>
                    <span style="text-decoration: underline;"><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_subject'];?>
</span>
                    <br />
                    <?php echo $_smarty_tpl->tpl_vars['data']->value['slot_number'];?>

                </td>
                <td align="center" width='20%'>
                    <?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['slot_date']);?>

                </td>
                <td align="center" width='35%'>
                    <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('task/izin_rute/slot_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['slot_id']));?>
" style="color: #0074cc;"><?php echo $_smarty_tpl->tpl_vars['data']->value['slot_file_name'];?>
</a>
                </td>
            </tr>
            <?php }} else { ?>
            <tr>
                <td colspan="4">-</td>
            </tr>
            <?php } ?>
        </table>
        <p style="margin: 0; padding: 5px; font-size: 11px; font-weight: bold;">
            Slot Clearance ( IASM )
        </p>
        <table class="table-view" width="100%">
            <tr style="font-weight: bold; font-size: 10px;">
                <td align="center" width='5%'>No</td>
                <td align="center" width='11%'>Rute</td>
                <td align="center" width='8%'>Tipe</td>
                <td align="center" width='8%'>Flight No</td>
                <td align="center" width='8%'>ETD <br />( UTC )</td>
                <td align="center" width='8%'>ETA <br />( UTC )</td>
                <td align="center" width='10%'>DOS</td>
                <td align="center" width='8%'>RON</td>
                <td align="center" width='15%'>Start</td>
                <td align="center" width='15%'>End</td>
            </tr>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_slot_iasm')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <tr>
                <td align="center" width='5%'><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
                <td align="center" width='11%'><?php echo $_smarty_tpl->tpl_vars['data']->value['rute_all'];?>
</td>
                <td align="center" width='8%'><?php echo $_smarty_tpl->tpl_vars['data']->value['tipe'];?>
</td>
                <td align="center" width='8%'><?php echo $_smarty_tpl->tpl_vars['data']->value['flight_no'];?>
</td>
                <td align="center" width='8%'><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['etd'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center" width='8%'><?php echo substr((($tmp = @$_smarty_tpl->tpl_vars['data']->value['eta'])===null||$tmp==='' ? '-' : $tmp),0,5);?>
</td>
                <td align="center" width='10%'><?php echo $_smarty_tpl->tpl_vars['data']->value['doop'];?>
</td>
                <td align="center" width='8%'><?php echo $_smarty_tpl->tpl_vars['data']->value['roon'];?>
</td>
                <td align="center" width='15%'><?php echo $_smarty_tpl->tpl_vars['data']->value['start_date'];?>
</td>
                <td align="center" width='15%'><?php echo $_smarty_tpl->tpl_vars['data']->value['end_date'];?>
</td>
            </tr>
            <?php }} else { ?>
            <tr>
                <td colspan="10">-</td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<!-- form files -->
<div id="form-files" style="display: none;">
    <div style="height: 395px; overflow: auto;">
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute/file_process');?>
" method="post">
            <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
            <input type="hidden" name="url_path" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('url_path')->value)===null||$tmp==='' ? '' : $tmp);?>
">
            <table class="table-input" width="100%" style="font-family: tahoma; font-size: 11px;">
                <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
                <?php $_smarty_tpl->tpl_vars['verified_by'] = new Smarty_variable('', null, null);?>
                <?php $_smarty_tpl->tpl_vars['verified_date'] = new Smarty_variable('', null, null);?>
                <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                <?php if ($_smarty_tpl->getVariable('verified_by')->value==''){?>
                <?php $_smarty_tpl->tpl_vars['verified_by'] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['check_by'], null, null);?>
                <?php $_smarty_tpl->tpl_vars['verified_date'] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['check_date'], null, null);?>
                <?php }?>
                <tr>
                    <td width='5%' align="center">
                        <?php echo $_smarty_tpl->getVariable('no')->value++;?>
.
                    </td>
                    <td width='45%'>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['ref_name'];?>

                    </td>
                    <td width="40%">
                        <?php if ($_smarty_tpl->tpl_vars['data']->value['file_id']!=''){?>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('task/izin_rute/files_download/').($_smarty_tpl->tpl_vars['data']->value['file_id']));?>
" style="color: #0074cc;"><?php echo $_smarty_tpl->tpl_vars['data']->value['file_name'];?>
</a>
                        <?php }else{ ?>
                        Belum ada file yang di upload!
                        <?php }?>
                    </td>
                    <td width="10%" align='center'>
                        <input type="checkbox" name="izin_files[<?php echo $_smarty_tpl->tpl_vars['data']->value['file_id'];?>
]" <?php if ($_smarty_tpl->tpl_vars['data']->value['file_check']=='1'){?>checked="checked"<?php }?> value="1" />
                    </td>
                </tr>
                <?php }} ?>
                <tr>
                    <td colspan="2">
                        Verified at <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('verified_date')->value))===null||$tmp==='' ? '-' : $tmp);?>
, <br /> By <?php echo (($tmp = @$_smarty_tpl->getVariable('verified_by')->value)===null||$tmp==='' ? 'Belum di verifikasi!' : $tmp);?>

                    </td>
                    <td colspan="2" align='right'>
                        <?php if ($_smarty_tpl->getVariable('com_user')->value['role_id']!='45'&&$_smarty_tpl->getVariable('com_user')->value['role_id']!='63'&&$_smarty_tpl->getVariable('com_user')->value['role_id']!='44'){?>
                        <input type="submit" name="save" value="Verifikasi Berkas Permohonan" class="submit-button" />
                        <?php }?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>