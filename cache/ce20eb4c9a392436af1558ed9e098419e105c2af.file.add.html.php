<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 08:17:59
         compiled from "application/views\member/registration_scheduled/add.html" */ ?>
<?php /*%%SmartyHeaderCode:61795656b22789dfa7-91017042%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ce20eb4c9a392436af1558ed9e098419e105c2af' => 
    array (
      0 => 'application/views\\member/registration_scheduled/add.html',
      1 => 1441883438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '61795656b22789dfa7-91017042',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // --
        $(".data_flight").select2({
            placeholder: "-- Pilih Jenis Penerbangan --",
            width: 200,
            allowClear: true
        });
        $(".services_cd").select2({
            placeholder: "-- Pilih Keterangan --",
            width: 280,
            allowClear: true
        });
    });
</script>
<style type="text/css">
    .data_flight .select2-choice {
        width: 190px !important;
        font-weight: bold;
    }
    .data_flight .select2-default {
        width: 190px !important;
        font-weight: bold;
    }
    .services_cd .select2-default {
        width: 270px !important;
        font-weight: bold;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Registration</a><span></span>
        <small>Flight Approval Angkutan Udara Niaga Berjadwal</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_scheduled/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/registration_scheduled/add/').($_smarty_tpl->getVariable('result')->value['data_id']));?>
" class="active"><b>Step 1</b><br />Domestic / International</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 2</b><br />Flight Approval</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 3</b><br />Data Rute Penerbangan</a>
        </li>
        <li>
            <a href="#" class="normal"><b>Step 4</b><br />File Attachment</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_scheduled/add_process');?>
" method="post">
    <input type="hidden" name="data_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['data_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width="100%">
        <tr>
            <td width='25%'>
                <span style="text-decoration: underline;">Kementerian Perhubungan Republik Indonesia</span><br />
                <i>Ministry of Transportation Of the Republic of Indonesia</i>
            </td>
            <td rowspan="3" width='35%' align='center'><b style="font-size: 24px;">FLIGHT APPROVAL</b></td>
            <td rowspan="3" width='40%'>
                <select name="data_flight" class="data_flight" id="data_flight">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_flight_type')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value;?>
" <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['data']->value;?>
<?php $_tmp1=ob_get_clean();?><?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)==$_tmp1){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value,SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value));?>
</option>
                    <?php }} ?>
                </select>
                <em>* wajib diisi</em>
                <br>
                <br>
                <select name="services_cd" class="services_cd" id="services_cd">
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_service_code')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['services_cd'];?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['services_cd'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['data']->value['services_cd']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['services_nm'];?>
</option>
                    <?php }} ?>
                </select>
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Direktorat Jenderal Perhubungan Udara </span><br />
                <i>Directorate General of Civil Aviation</i>
            </td>
        </tr>
        <tr>
            <td>
                <span style="text-decoration: underline;">Persetujuan terbang untuk wilayah Indonesia</span><br />
                <i>Flight Approval for Indonesia territory</i>
            </td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td colspan="2"></td>
            <td colspan="2" align='right'>
                <input type="submit" name="save" value="Lanjutkan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    jQuery('#data_flight').on('change', function(dom_int) {
        $.ajaxSetup({
            cache: false
        });
        var sendData = {
            data_type: 'berjadwal',
            data_flight: dom_int.val
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/member/registration_scheduled/get_services_cd/",
            dataType: "json",
            data: sendData,
            success: function (results) {
                $('#services_cd').empty();
                $.each(results, function (index, value) {
                    $('#services_cd').append($('<option/>', {
                        value: value.services_cd,
                        text: value.services_nm
                    }))
                })
            },
        });
    })
</script>
