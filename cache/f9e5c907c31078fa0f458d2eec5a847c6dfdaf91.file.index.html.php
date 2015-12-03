<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 16:10:43
         compiled from "application/views\izin_pending_domestik/migrasi/index.html" */ ?>
<?php /*%%SmartyHeaderCode:24296565720f3903591-08006966%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f9e5c907c31078fa0f458d2eec5a847c6dfdaf91' => 
    array (
      0 => 'application/views\\izin_pending_domestik/migrasi/index.html',
      1 => 1441883445,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24296565720f3903591-08006966',
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
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Permohonan Direvisi</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/pending_izin');?>
">Izin Rute Penerbangan</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/pending_izin');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Waiting List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_pending_domestik/migrasi/index/').($_smarty_tpl->getVariable('detail')->value['izin_id']));?>
" class="active"><b>Step 1</b><br />Data Permohonan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 2</b><br />Data Rute Penerbangan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 3</b><br />Review Permohonan</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<div class="dashboard">
    <table class="table-form" width="100%">
        <tr>
            <td><h3>Catatan Perbaikan</h3></td>
        </tr>
        <tr>
            <td>
                <?php if ($_smarty_tpl->getVariable('catatan')->value!=''){?>
                <p style="color: #B72C0F; font-size: 12px;"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('catatan')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('catatan')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('catatan')->value));?>
</p>
                <?php }else{ ?>
                Tidak ada catatan yang diinputkan.
                <?php }?>
            </td>
        </tr>
    </table>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_pending_domestik/migrasi/edit_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="process_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['process_id'])===null||$tmp==='' ? '' : $tmp);?>
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
            <td width='20%'>
                <span style="text-decoration: underline;">Rute Pairing</span><br /><i>Pair Routes</i>
            </td>
            <td width='30%'><b><?php echo $_smarty_tpl->getVariable('result')->value['izin_rute_start'];?>
</b> / <b><?php echo $_smarty_tpl->getVariable('result')->value['izin_rute_end'];?>
</b></td>
            <td width='20%'>
                <span style="text-decoration: underline;">Tanggal Surat Diterbitkan</span><br /><i>Published Letter Date</i>
            </td>
            <td width='30%'>
                <input type="text" name="izin_published_date" size="10" maxlength="10" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['izin_published_date'])===null||$tmp==='' ? '' : $tmp);?>
" class="tanggal" readonly="readonly" style="text-align: center;" placeholder="Wajib diisi" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Jenis Penerbangan</span><br /><i>Flight Type</i>
            </td>
            <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('result')->value['pax_cargo'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('result')->value['pax_cargo'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('result')->value['pax_cargo']));?>
</b></td>
            <td>
                <span style="text-decoration: underline;">Nomor Surat Terbit</span><br /><i>Published Letter Number</i>
            </td>
            <td>
                <input type="text" name="izin_published_letter" size="30" maxlength="50" value="<?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('result')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp)));?>
" style="text-align: center;" placeholder="Wajib diisi" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Season Code</span><br /><i>For IASM Airport</i>
            </td>
            <td colspan="3"><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_season'])===null||$tmp==='' ? '' : $tmp)));?>
</td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>
                <input type="submit" name="save" value="Batalkan Permohonan" class="reset-button" onclick="return confirm('Apakah anda yakin akan membatalkan permohonan ini?');" />
            </td>
            <td>Semua field wajib diisi! kesalahan pada format input merupakan tanggungjawab pengguna dan akan dikembalikan (Revisi) pada pemohon!</td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan dan Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>