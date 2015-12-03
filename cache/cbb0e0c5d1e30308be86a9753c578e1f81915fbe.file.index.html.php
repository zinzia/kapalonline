<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:43:52
         compiled from "application/views\report/published_izin/index.html" */ ?>
<?php /*%%SmartyHeaderCode:15755600dc88efc267-09707302%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbb0e0c5d1e30308be86a9753c578e1f81915fbe' => 
    array (
      0 => 'application/views\\report/published_izin/index.html',
      1 => 1441883444,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15755600dc88efc267-09707302',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Penerbitan Izin Rute / FA</a><span></span>
        <small>Izin Rute Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Search</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('report/published_izin/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>    
                <td width="10%">Periode</td>
                <td width="10%">
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
                        <option value="<?php echo $_smarty_tpl->tpl_vars['tahun']->value['tahun'];?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['tahun'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['tahun']->value['tahun']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tahun']->value['tahun'];?>
</option>
                        <?php }} ?>
                    </select>
                </td>
                <td width="15%">
                    <select name="data_flight" class="data_flight">
                        <option value="">-- semua --</option>
                        <option value="domestik" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='domestik'){?>selected="selected"<?php }?>>DOMESTIK</option>
                        <option value="internasional" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='internasional'){?>selected="selected"<?php }?>>INTERNASIONAL</option>
                    </select>  
                </td>
                <td width="15%">Status Bayar</td>
                <td width="15%">
                    <select name="payment_st" class="payment_st">
                        <option value="">-- semua --</option>
                        <option value="00" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['payment_st'])===null||$tmp==='' ? '' : $tmp)=='00'){?>selected="selected"<?php }?>>BELUM BAYAR</option>
                        <option value="11" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['payment_st'])===null||$tmp==='' ? '' : $tmp)=='11'){?>selected="selected"<?php }?>>SUDAH BAYAR</option>
                        <option value="22" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['payment_st'])===null||$tmp==='' ? '' : $tmp)=='22'){?>selected="selected"<?php }?>>TIDAK BAYAR</option>
                    </select>  
                </td>
                <td width='25%' align='right' rowspan="2">
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
                <li class="info">Total&nbsp;<strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['total'])===null||$tmp==='' ? 0 : $tmp);?>
</strong>&nbsp;Record&nbsp;</li>
                <?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['data'])===null||$tmp==='' ? '' : $tmp);?>

            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width='4%'>No</th>
        <th width='20%'>Surat<br />Permohonan</th>
        <th width='10%'>Tanggal</th>
        <th width='10%'>Jenis<br />Penerbangan</th>
        <th width='26%'>Rute<br />Perihal</th>
        <th width='18%'>Pemohon</th>
        <th width='12%'>Status<br /> Pembayaran</th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['airlines_nm'];?>
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_request_letter'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_request_letter'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_request_letter']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins')));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm']));?>
</td>
        <td align="center">
            <span style="text-decoration: underline;"><?php echo $_smarty_tpl->tpl_vars['result']->value['operator_name'];?>
</span>
            <br />
            <?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_date']);?>

        </td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['payment_st']=='11'||$_smarty_tpl->tpl_vars['result']->value['payment_st']=='22'){?>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @((('report/published_izin/').($_smarty_tpl->tpl_vars['result']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-approve">
                Download
            </a>
            <?php }else{ ?>
            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.waiting.png" alt="" /> Belum Bayar
            <?php }?>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="9">Data not found!</td>
    </tr>
    <?php } ?>
</table>