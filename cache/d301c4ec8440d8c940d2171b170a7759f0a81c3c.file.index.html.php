<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 10:33:54
         compiled from "application/views\member/report_izin/index.html" */ ?>
<?php /*%%SmartyHeaderCode:20999565d6982b6bf65-19046738%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd301c4ec8440d8c940d2171b170a7759f0a81c3c' => 
    array (
      0 => 'application/views\\member/report_izin/index.html',
      1 => 1443500820,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20999565d6982b6bf65-19046738',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_capitalize')) include 'C:\xampp\htdocs\kapalonline\system\plugins\smarty\libs\plugins\modifier.capitalize.php';
?><div class="breadcrum">
    <p>
        <a href="#">Laporan Izin Rute Penerbangan</a><span></span>
        <small>Rute Penerbangan <?php echo smarty_modifier_capitalize($_smarty_tpl->getVariable('search')->value['data_flight']);?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Search By</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/report_izin/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
            <tr>    
                <td width="10%">Periode / Season</td>
                <td width="20%">
                    <input type="text" name="season_cd" value="<?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('search')->value['season_cd'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('search')->value['season_cd'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('search')->value['season_cd']));?>
" size="5" maxlength="3" style="text-align: center;" />
                    <select name="bulan">
                        <?php  $_smarty_tpl->tpl_vars['bulan'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_bulan')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['bulan']->key => $_smarty_tpl->tpl_vars['bulan']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['bulan']->key;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['bulan'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['i']->value){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['bulan']->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['bulan']->value,SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['bulan']->value));?>
</option>
                        <?php }} ?>
                    </select>
                </td>
                <td width="10%">
                    <select name="tahun">
                        <?php  $_smarty_tpl->tpl_vars['tahun'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_tahun')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['tahun']->key => $_smarty_tpl->tpl_vars['tahun']->value){
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['tahun']->value;?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['tahun'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['tahun']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tahun']->value;?>
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
                <td width="15%">Airport ( IATA Code )</td>
                <td width="10%">
                    <input type="text" name="airport_iata_cd" value="<?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('search')->value['airport_iata_cd'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('search')->value['airport_iata_cd'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('search')->value['airport_iata_cd']));?>
" size="10" maxlength="3" placeholder="-- semua --" style="text-align: center;" />
                </td>
                <td align='right' align='20%'>
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
        <th width="4%">No</th>
        <th width="15%">Rute<br /> Penerbangan</th>
        <th width="14%">Status <br />Penerbangan</th>
        <th width="11%">Jenis <br />Pesawat</th>
        <th width="8%">Kapasitas <br /> Pesawat</th>
        <th width="10%">Frekuensi / <br /> Minggu</th>
        <th width="10%">Kapasitas / <br /> Minggu</th>
        <th width="10%">Kapasitas / <br /> Tahun</th>
        <th width="10%">Masa Berlaku</th>
        <th width="8%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
</td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['pairing']=='VV'){?>Vice Versa<?php }?>
            <?php if ($_smarty_tpl->tpl_vars['result']->value['pairing']=='OW'){?>One Way Services<?php }?>
        </td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['tipe'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['tipe'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['tipe']));?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['capacity'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['total_frekuensi'];?>
X</td>
        <td align="center"><?php echo number_format($_smarty_tpl->tpl_vars['result']->value['kapasitas_week'],0,',','.');?>
</td>
        <td align="center"><?php echo number_format($_smarty_tpl->tpl_vars['result']->value['kapasitas_year'],0,',','.');?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['masa_berlaku'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['masa_berlaku'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['masa_berlaku'],'ins')));?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('member/report_izin/detail/').($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'])).('/')).($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
" class="button">Details</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="10">Data not found!</td>
    </tr>
    <?php } ?>
</table>