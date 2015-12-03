<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:59:14
         compiled from "application/views\stakeholder/realisasi/index.html" */ ?>
<?php /*%%SmartyHeaderCode:19173565691a2b4adc8-70856754%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4aa3a9e5ad474902c1ebc3bd187a73e311f023ec' => 
    array (
      0 => 'application/views\\stakeholder/realisasi/index.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19173565691a2b4adc8-70856754',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'C:\xampp\htdocs\kapalonline\system\plugins\smarty\libs\plugins\modifier.date_format.php';
?><script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal_from").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
        }).datepicker("setDate", "<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['tanggal_from'])===null||$tmp==='' ? 'new Date()' : $tmp);?>
");
        $(".tanggal_to").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
        }).datepicker("setDate", "<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['tanggal_to'])===null||$tmp==='' ? 'new Date()' : $tmp);?>
");
        // --
        $(".data_flight").select2({
            placeholder: "-- Semua --",
            width: 170,
            allowClear: true
        });
        $(".payment_st").select2({
            placeholder: "-- Semua --",
            width: 140,
            allowClear: true
        });
        $(".airlines_nm").select2({
            placeholder: "-- Semua --",
            width: 300,
            allowClear: true
        });
        $(".services_cd").select2({
            placeholder: "-- Semua --",
            width: 150,
            allowClear: true
        });
    });
</script>
<style type="text/css">
    .data_flight .select2-choice {
        width: 160px !important;
        font-weight: bold;
    }
    .data_flight .select2-default {
        width: 160px !important;
        font-weight: bold;
    }
    .payment_st .select2-choice {
        width: 130px !important;
        font-weight: bold;
    }
    .payment_st .select2-default {
        width: 130px !important;
        font-weight: bold;
    }
    .airlines_nm .select2-choice {
        width: 290px !important;
    }
    .airlines_nm .select2-default {
        width: 290px !important;
    }
    .services_cd .select2-choice {
        width: 140px !important;
    }
    .services_cd .select2-default {
        width: 140px !important;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Realisasi Flight Approval</a><span></span>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/realisasi/proses_cari_realisasi');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <td width="10%">Tanggal Penerbangan</td>
                <td width="40%">
                    <input name="tanggal_from" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['tanggal_from'])===null||$tmp==='' ? '' : $tmp);?>
" size="15" class="tanggal_from" style="text-align:center;" />
                    s/d
                    <input name="tanggal_to" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['tanggal_to'])===null||$tmp==='' ? '' : $tmp);?>
" size="15" class="tanggal_to" style="text-align:center;" />
                </td>
                <td width="10%">Nomor Document/FA</td>
                <td width="20%">
                    <input name="published_no" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['published_no'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" />
                </td>
                <td align="right" rowspan="3">
                    <input name="save" type="submit" value="Tampilkan" />
                    <input name="save" type="submit" value="Reset" />
                </td>
            </tr>
            <tr>
                <td>Jenis Penerbangan</td>
                <td>
                    <select name="data_type" class="data_flight">
                        <option value=""></option>
                        <option value="berjadwal" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_type'])===null||$tmp==='' ? '' : $tmp)=='berjadwal'){?>selected="selected"<?php }?>>BERJADWAL</option>
                        <option value="tidak berjadwal" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_type'])===null||$tmp==='' ? '' : $tmp)=='tidak berjadwal'){?>selected="selected"<?php }?>>TIDAK BERJADWAL</option>
                        <option value="bukan niaga" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_type'])===null||$tmp==='' ? '' : $tmp)=='bukan niaga'){?>selected="selected"<?php }?>>BUKAN NIAGA</option>
                    </select>
                    <select name="data_flight" class="data_flight">
                        <option value=""></option>
                        <option value="domestik" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='domestik'){?>selected="selected"<?php }?>>DOMESTIK</option>
                        <option value="internasional" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='internasional'){?>selected="selected"<?php }?>>INTERNASIONAL</option>
                    </select>
                </td>
                <td>Remark</td>
                <td>
                    <select name="services_cd" class="services_cd">
                        <option value=""></option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_services')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <option value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['services_cd'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['services_cd'])===null||$tmp==='' ? '' : $tmp)==(($tmp = @$_smarty_tpl->tpl_vars['data']->value['services_cd'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['services_nm'])===null||$tmp==='' ? '' : $tmp);?>
</option>
                        <?php }} ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Airlines</td>
                <td>
                    <select name="airlines_nm" class="airlines_nm">
                        <option value=""></option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <option value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp)==(($tmp = @$_smarty_tpl->tpl_vars['data']->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp);?>
</option>
                        <?php }} ?>
                    </select>
                </td>
                <td></td>
                <td>
                </td>
            </tr>
        </table>
    </form>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info"><strong><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</strong> Record</li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation">
        <div class="navigation-button">
            <ul>
                <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/realisasi/download_report/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/download-icon.png" alt="" /> Download Excel</a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width="4%">No</th>
        <th width="10%">Nomor<br />Dokumen</th>
        <th width="19%">Nomor FA</th>
        <th width="15%">Operator</th>
        <th width="13%">Tanggal</th>
        <th width="15%">Rute</th>
        <th width='18%'>Call Sign</th>
        <th width="5%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?> onclick="form_fa(<?php echo $_smarty_tpl->tpl_vars['result']->value['data_id'];?>
)" style="cursor:zoom-in;">
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['document_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['published_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['published_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['published_no']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm']));?>
</td>
        <td align="center">
            <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %y")));?>
 / 
            <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %y")));?>

        </td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['registration'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['registration'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['registration'])))===null||$tmp==='' ? '' : $tmp);?>
<br /><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['flight_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/<?php if ((($tmp = @$_smarty_tpl->tpl_vars['result']->value['data_realisasi'])===null||$tmp==='' ? '' : $tmp)=='ya'){?>icon.ok.png<?php }else{ ?>icon.waiting.png<?php }?>" alt="" /></td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>

<!-- script form fa -->
<script type="text/javascript">
    function form_fa(data_id) {
        window.open("<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/realisasi/form');?>
/" + data_id, "_blank");
    }
</script>
