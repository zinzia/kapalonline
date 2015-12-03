<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:12:37
         compiled from "application/views\member/payment_izin/success.html" */ ?>
<?php /*%%SmartyHeaderCode:54175600d5354e13b3-00019644%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '15702567ef7603dd65d19ed5184ba655654e5829' => 
    array (
      0 => 'application/views\\member/payment_izin/success.html',
      1 => 1441883440,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '54175600d5354e13b3-00019644',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Penagihan dan Pembayaran</a><span></span>
        <small>Izin Rute Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin');?>
">Create Invoices</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/pending');?>
">Waiting For Payment</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/failed');?>
">Payment Failed</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/success');?>
" class="active">PAYMENT SUCCESS</a></li>
    </ul>
</div>
<div class="clear"></div>
<div class="sub-content">
    <!-- notification template -->
    <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
    <!-- end of notification template-->
    <div class="search-box">
        <h3><a href="#">Search</a></h3>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/proses_cari_success');?>
" method="post">
            <table class="table-search" width="100%" border="0">
                <tr>
                    <th width="15%">Kode Billing</th>
                    <td width="50%">
                        <input name="virtual_account" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['virtual_account'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="30" />
                    </td>
                    <td align="right" width="35%">
                        <input name="save" type="submit" value="Tampilkan" />
                        <input name="save" type="submit" value="Reset" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info"><strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['start'])===null||$tmp==='' ? '0' : $tmp);?>
 - <?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['end'])===null||$tmp==='' ? '0' : $tmp);?>
</strong> dari <strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['total'])===null||$tmp==='' ? '0' : $tmp);?>
</strong> Pembayaran yang berhasil</li>
                <?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['data'])===null||$tmp==='' ? '' : $tmp);?>

            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <table class="table-view" width="100%">
        <tr>
            <th width='4%'>No</th>
            <th width='13%'>Kode Billing</th>
            <th width='18%'>Tanggal Penagihan</th>
            <th width='18%'>Tanggal<br />Update Pembayaran</th>
            <th width='10%'>Jumlah<br />Ijin Rute</th>          
            <th width='10%'>Jumlah<br />Tagihan</th>
            <th width='12%'>Nomor<br />Kuitansi</th>
            <th width='15%'></th>
        </tr>
        <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
        <?php $_smarty_tpl->tpl_vars['total'] = new Smarty_variable(0, null, null);?>
        <?php $_smarty_tpl->tpl_vars['jml'] = new Smarty_variable(0, null, null);?>
        <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
        <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
            <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['virtual_account'];?>
</td>
            <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['inv_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['inv_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['inv_date'],'ins')));?>
</td>
            <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['tgl_update'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['tgl_update'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['tgl_update'],'ins')));?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['total_ijin'];?>
</td>
            <td align="right"><?php echo number_format((($tmp = @$_smarty_tpl->tpl_vars['result']->value['inv_total'])===null||$tmp==='' ? 0 : $tmp),0,",",".");?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['no_kuitansi'];?>
</td>
            <td align="center">
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('member/payment_izin/cetak_kwitansi/').($_smarty_tpl->tpl_vars['result']->value['inv_id']))===null||$tmp==='' ? 0 : $tmp));?>
" class="button">Cetak Bukti Pembayaran</a>
            </td>
        </tr>
        <?php $_smarty_tpl->tpl_vars['total'] = new Smarty_variable($_smarty_tpl->getVariable('total')->value+$_smarty_tpl->tpl_vars['result']->value['inv_total'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['jml'] = new Smarty_variable($_smarty_tpl->getVariable('jml')->value+$_smarty_tpl->tpl_vars['result']->value['total_ijin'], null, null);?>
        <?php }} else { ?>
        <tr>
            <td colspan="8">Data not found!</td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="4" align="center">TOTAL</td>
            <td align="center"><b><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('jml')->value)===null||$tmp==='' ? 0 : $tmp),0,",",".");?>
</b></td>
            <td align="right"><b><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp),0,",",".");?>
</b></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

