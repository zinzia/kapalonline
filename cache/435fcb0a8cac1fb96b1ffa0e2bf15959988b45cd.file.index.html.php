<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:21:57
         compiled from "application/views\izin_internasional/frekuensi_add/index.html" */ ?>
<?php /*%%SmartyHeaderCode:138655600e5756e4415-28452944%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '435fcb0a8cac1fb96b1ffa0e2bf15959988b45cd' => 
    array (
      0 => 'application/views\\izin_internasional/frekuensi_add/index.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138655600e5756e4415-28452944',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        /*
         * COMBO BOX
         */
        $("#combobox").select2({
            placeholder: "Pilih Nomor Izin Rute Yang Terdaftar",
            allowClear: true,
        });
        // rute
        $('#combobox').change(function() {
            var kode_izin = $(this).val();
            // ajax loader
            $("#loader-validate").show();
            // ajax
            $.ajax({
                type: 'POST',
                data: 'kode_izin=' + kode_izin,
                url: '<?php echo $_smarty_tpl->getVariable('config')->value->site_url("izin_internasional/frekuensi_add/get_rute_by_kode_izin");?>
',
                complete: function() {
                    $("#loader-validate").hide();
                },
                success: function(result) {
                    data = JSON.parse(result);
                    $('#input_pax_cargo').val(data.pax_cargo);
                    $('#input_masa_berlaku').val(data.masa_berlaku);
                    $('#str_pax_cargo').html(data.pax_cargo);
                    $('#str_masa_berlaku').html(data.masa_berlaku);
                    $('#str_season_cd').val(data.izin_season);
                }
            });
        });
        // date picker
        $(".tanggal").datepicker({
            showOn: 'both',
            changeMonth: true,
            changeYear: true,
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '0'
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/frekuensi_add/index/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
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
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/frekuensi_add/edit_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <input type="hidden" name="input_pax_cargo" value="<?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('result')->value['input_pax_cargo'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('result')->value['input_pax_cargo'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('result')->value['input_pax_cargo'])))===null||$tmp==='' ? '' : $tmp);?>
" id="input_pax_cargo" />
    <input type="hidden" name="input_masa_berlaku" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['input_masa_berlaku'])===null||$tmp==='' ? '' : $tmp);?>
" id="input_masa_berlaku" />
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
            <td width='20%'>
                <span style="text-decoration: underline;">Rute Penerbangan</span><br /><i>Routes</i>
            </td>
            <td width='30%'>
                <select name="kode_izin" id="combobox">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['kode_izin'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['kode_izin'])===null||$tmp==='' ? '' : $tmp)==(($tmp = @$_smarty_tpl->tpl_vars['data']->value['kode_izin'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end']));?>
 ( <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_season'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_season'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_season']));?>
 )</option>
                    <?php }} ?>
                </select>    
            </td>
            <td>
                <span style="text-decoration: underline;">Tanggal Surat Permohonan</span><br /><i>Request Letter Date</i>
            </td>
            <td>
                <input type="text" name="izin_request_letter_date" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter_date'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center; font-weight: bold;" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Penerbangan</span><br /><i>Flight Type</i>
            </td>
            <td>
                <label id="str_pax_cargo"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('result')->value['input_pax_cargo'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('result')->value['input_pax_cargo'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('result')->value['input_pax_cargo']));?>
</label>
            </td>
            <td width='20%'>
                <span style="text-decoration: underline;">Nomor Surat Permohonan</span><br /><i>Request Letter Number</i>
            </td>
            <td width='30%'>
                <input type="text" name="izin_request_letter" size="25" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp)));?>
" style="font-weight: bold;" />
            </td>
        </tr>   
        <tr>
            <td>
                <span style="text-decoration: underline;">Masa Berlaku</span><br /><i>Expired Date</i>
            </td>
            <td>
                <label id="str_masa_berlaku"><?php echo $_smarty_tpl->getVariable('result')->value['input_masa_berlaku'];?>
</label>
            </td>
            <td><span style="text-decoration: underline;">Penunjukkan Oleh</span><br /><i>Appointment By</i></td>
            <td>
                <input type="text" name="penanggungjawab" size="20" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['penanggungjawab'])===null||$tmp==='' ? '' : $tmp)));?>
" />
            </td>
        </tr>  
        <tr>
            <td>
                <span style="text-decoration: underline;">Season Code</span><br /><i>Yang sedang berjalan</i>
            </td>
            <td>
                <input type="text" name="izin_season" size="11" maxlength="3" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_season'])===null||$tmp==='' ? '' : $tmp)));?>
" style="text-align: center;" readonly="readonly" id="str_season_cd" />
            </td>
            <td><span style="text-decoration: underline;">Jabatan</span><br /><i>Job Title</i></td>
            <td>
                <input type="text" name="jabatan" size="20" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['jabatan'])===null||$tmp==='' ? '' : $tmp)));?>
" />
            </td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>
                Semua field wajib diisi! kesalahan pada format input merupakan tanggungjawab pengguna dan akan dikembalikan (Revisi) pada pemohon!
            </td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan dan Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>