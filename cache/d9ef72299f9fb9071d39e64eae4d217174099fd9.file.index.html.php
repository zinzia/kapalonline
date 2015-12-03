<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:59:19
         compiled from "application/views\stakeholder/report_izin/index.html" */ ?>
<?php /*%%SmartyHeaderCode:28192565691a71a0665-41558774%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9ef72299f9fb9071d39e64eae4d217174099fd9' => 
    array (
      0 => 'application/views\\stakeholder/report_izin/index.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28192565691a71a0665-41558774',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Data Report</a><span></span>
        <small>Izin Rute Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/report_izin/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <td width="10%">Airlines</td>
                <td width="30%">
                    <select name="airlines_id" class="airlines_id">
                        <option value="">-- semua --</option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                            <option value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['airlines_id'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['airlines_id'])===null||$tmp==='' ? '' : $tmp)==(($tmp = @$_smarty_tpl->tpl_vars['data']->value['airlines_id'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
</option>
                        <?php }} ?>
                    </select>  
                </td>
                <td width="15%">
                    <select name="data_flight" class="data_flight">
                        <option value="domestik" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='domestik'){?>selected="selected"<?php }?>>DOMESTIK</option>
                        <option value="internasional" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='internasional'){?>selected="selected"<?php }?>>INTERNASIONAL</option>
                    </select>  
                </td>
                <td align='right' align='45%'>
                    <input name="save" type="submit" value="Tampilkan" />
                    <input name="save" type="submit" value="Reset" />
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
</strong> Rute Penerbangan</li>
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
        <th width="5%">No</th>
        <th width="10%">Kode Izin</th>
        <th width="10%">Rute Penerbangan</th>
        <th width="10%">Jenis Pesawat</th>
        <th width="10%">Kapasitas <br /> Pesawat</th>
        <th width="10%">Frekuensi / <br /> Minggu</th>
        <th width="10%">Kapasitas / <br /> Minggu</th>
        <th width="10%">Kapasitas / <br /> Tahun</th>
        <th width="15%">Masa Berlaku</th>
        <th width="10%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['kode_izin'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['kode_izin'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['kode_izin']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['aircraft_type'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_type'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_type']));?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['aircraft_capacity'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['frekuensi_week'];?>
X</td>
        <td align="center"><?php echo number_format($_smarty_tpl->tpl_vars['result']->value['kapasitas_week'],0,',','.');?>
</td>
        <td align="center"><?php echo number_format($_smarty_tpl->tpl_vars['result']->value['kapasitas_year'],0,',','.');?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_expired_date']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_expired_date']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_expired_date'])));?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('stakeholder/report_izin/detail/').($_smarty_tpl->tpl_vars['result']->value['kode_izin']));?>
" class="button">Details</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="10">Data not found!</td>
    </tr>
    <?php } ?>
</table>