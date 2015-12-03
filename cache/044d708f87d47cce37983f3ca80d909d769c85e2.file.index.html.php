<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 10:00:50
         compiled from "application/views\izin_domestik/baru/index.html" */ ?>
<?php /*%%SmartyHeaderCode:25157565eb342d09ea7-16529801%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '044d708f87d47cce37983f3ca80d909d769c85e2' => 
    array (
      0 => 'application/views\\izin_domestik/baru/index.html',
      1 => 1448875777,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25157565eb342d09ea7-16529801',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function () {
        // date picker
        $(".tanggal").datepicker({
            showOn: 'both',
            changeMonth: true,
            changeYear: true,
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
        });
        // Pairing
        var reverseString = function (string) {
            return string.split('-').reverse().join('-');
        };
        // rute
        $(".rute-start").change(function () {
            $(".rute-end").val('');
            $(".rute-end").val(reverseString($(this).val()));
        });
        /*
         * COMBO BOX
         */
        $(".rute-start").select2({
            placeholder: "Pilih Izin Rute Pada SIUP",
            allowClear: true,
            width: 200
        });
    });
</script>
<style type="text/css">
    .select2-choice {
        width: 190px !important;
    }
    .select2-default {
        width: 190px !important;
    }
</style>
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
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_domestik/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Draft List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_domestik/baru/index/').($_smarty_tpl->getVariable('detail')->value['izin_id']));?>
" class="active"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 2</b><br />Data Rute Penerbangan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 3</b><br />Slot Clearance</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 4</b><br />File Attachment</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 5</b><br />Review Permohonan</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_domestik/baru/edit_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
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
            <td>
                <input type="text" name="izin_request_letter_date" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter_date'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center; font-weight: bold;" />
            </td>
            <td width='20%'>
                <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i>Request Letter Number</i>
            </td>
            <td width='25%'>
                <input type="text" name="izin_request_letter" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td width='20%'>
                <span style="text-decoration: underline;">Rute Pairing</span><br /><i>Pair Routes</i>
            </td>
            <td width='35%'>
                <select name="izin_rute_start" class="rute-start">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_rute')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <?php if ($_smarty_tpl->getVariable('data')->value->jenis_penerbangan=='Domestik'){?>
                    <option value="<?php echo $_smarty_tpl->getVariable('data')->value->rute_asal;?>
-<?php echo $_smarty_tpl->getVariable('data')->value->rute_tujuan;?>
" <?php if (((($_smarty_tpl->getVariable('data')->value->rute_asal).('-')).($_smarty_tpl->getVariable('data')->value->rute_tujuan))==$_smarty_tpl->getVariable('result')->value['izin_rute_start']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->getVariable('data')->value->rute_asal;?>
-<?php echo $_smarty_tpl->getVariable('data')->value->rute_tujuan;?>
</option>
                    <?php }?>
                    <?php }} ?>
                </select>
                &nbsp;
                &nbsp;
                <input type="text" name="izin_rute_end" size="10" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_rute_end'])===null||$tmp==='' ? '' : $tmp)));?>
" style="text-align: center;" readonly="readonly" class="rute-end" placeholder="Automatic" />
            </td>
            <td><span style="text-decoration: underline;">Penunjukkan Oleh</span><br /><i>Appointment By</i></td>
            <td>
                <input type="text" name="penanggungjawab" size="20" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp)));?>
" />
            </td>
        </tr>   
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Penerbangan</span><br /><i>Flight Type</i>
            </td>
            <td>
                <select name="pax_cargo">
                    <?php if ($_smarty_tpl->getVariable('com_user')->value['airlines_type']=='penumpang'){?>
                    <option value="penumpang" <?php if ($_smarty_tpl->getVariable('result')->value['pax_cargo']=='penumpang'){?>selected="selected"<?php }?>>PENUMPANG</option>
                    <?php }?>
                    <?php if ($_smarty_tpl->getVariable('com_user')->value['airlines_type']=='kargo'){?>
                    <option value="cargo" <?php if ($_smarty_tpl->getVariable('result')->value['pax_cargo']=='cargo'){?>selected="selected"<?php }?>>KARGO</option>
                    <?php }?>
                </select>
            </td>
            <td><span style="text-decoration: underline;">Jabatan</span><br /><i>Job Title</i></td>
            <td>
                <input type="text" name="jabatan" size="20" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp)));?>
" />
            </td>
        </tr>  
        <tr>
            <td>
                <span style="text-decoration: underline;">Season Code</span><br /><i>For IASM Airport</i>
            </td>
            <td colspan="3">
                <input type="text" name="izin_season" size="11" maxlength="3" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp)));?>
" style="text-align: center;" placeholder="Wajib diisi" />
            </td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>Semua field wajib diisi! kesalahan pada format input merupakan tanggungjawab pengguna dan akan dikembalikan (Revisi) pada pemohon!</td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan dan Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>