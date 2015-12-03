<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:20:26
         compiled from "application/views\izin_internasional/perubahan/files.html" */ ?>
<?php /*%%SmartyHeaderCode:314955600e51a89d044-83263291%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b9484380791a01cff12a34aa41ed2962d8225be' => 
    array (
      0 => 'application/views\\izin_internasional/perubahan/files.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '314955600e51a89d044-83263291',
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perubahan/index/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perubahan/list_rute/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="normal"><b>Step 2</b><br />Data Rute Penerbangan</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perubahan/list_slot/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><b>Step 3</b><br />Slot Clearance</a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perubahan/list_files/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
" class="active"><b>Step 4</b><br />File Attachment</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 5</b><br />Review Permohonan</a>
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
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perubahan/list_slot/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perubahan/files_process');?>
" method="post" enctype="multipart/form-data">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width="100%">
        <tr>
            <th colspan="5">File Attachment</th>
        </tr>
        <tr>
            <td colspan="5">
                File persyaratan yang wajib di sertakan adalah sebagai berikut :
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
            <td width='30%'>
                <?php echo $_smarty_tpl->tpl_vars['data']->value['ref_name'];?>

            </td>
            <td width='20%'>
                <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['data']->value['ref_field'];?>
" />
            </td>
            <td width='15%'>
                <small style="color: #999;">Maksimal <?php echo $_smarty_tpl->tpl_vars['data']->value['ref_size'];?>
KB (<?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['ref_allowed'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['ref_allowed'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['ref_allowed']));?>
)</small>
            </td>
            <td width="30%">
                <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['ref_id'],$_smarty_tpl->getVariable('file_uploaded')->value)){?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('izin_internasional/perubahan/files_download/').($_smarty_tpl->getVariable('detail')->value['registrasi_id'])).('/')).($_smarty_tpl->tpl_vars['data']->value['ref_id']));?>
" style="font-size: 9px;"><?php echo $_smarty_tpl->getVariable('name_uploaded')->value[$_smarty_tpl->tpl_vars['data']->value['ref_id']];?>
</a>
                <?php }else{ ?>
                <?php if ($_smarty_tpl->tpl_vars['data']->value['ref_required']=='1'){?>
                <span style="color: red">Belum ada file yang di upload!</span>
                <?php }else{ ?>
                <span style="color: blue;">* Optional atau sesuai arahan dari regulator</span>
                <?php }?>
                <?php }?>
            </td>
        </tr>
        <?php }} ?>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5" align="center">
                <input type="submit" name="save" value="Upload Berkas" class='button-upload' />
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
    </table>
</form>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perubahan/files_next');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>
                Semua file dengan tanda khusus wajib di upload!
            </td>
            <td align='right'>
                <input type="submit" name="save" value="Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>