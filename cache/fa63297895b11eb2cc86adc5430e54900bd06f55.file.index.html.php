<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:32:28
         compiled from "application/views\member/slot/index.html" */ ?>
<?php /*%%SmartyHeaderCode:41265600d9dc3fc538-77817984%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fa63297895b11eb2cc86adc5430e54900bd06f55' => 
    array (
      0 => 'application/views\\member/slot/index.html',
      1 => 1441883437,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41265600d9dc3fc538-77817984',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Slot Time</a><span></span>
        <small>Confirmed Slot Schedule</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Search</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/slot/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="5%">Airport</th>
                <td width="5%">
                    <input name="rute_from" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['rute_from'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="3" placeholder="ex : CGK" style="text-align: center;" />
                </td>
                <th width="10%">Last / Next</th>
                <td width="5%">
                    <input name="rute_to" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['rute_to'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="3" placeholder="ex : DPS" style="text-align: center;" />
                </td>
                <th width="10%">Season Code</th>
                <td width="5%">
                    <input name="season_code" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['season_code'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="3" placeholder="ex : S15" style="text-align: center;" />
                </td>
                <th width="15%">Services Type</th>
                <td width="5%">
                    <input name="services_code" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['services_code'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="1" placeholder="ex : J" style="text-align: center;" />
                </td>
                <td width="10%">
                    <input name="flight_no" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['flight_no'])===null||$tmp==='' ? '' : $tmp);?>
" size="8" maxlength="6" placeholder="Filght Number" style="text-align: center;" />
                </td>
                <td width='30%' align='right'>
                    <input name="save" type="submit" value="View" />
                    <input name="save" type="submit" value="Switch" />
                    <input name="save" type="submit" value="Reset" />
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total ( <b style="color: red;"><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> ) Confirmed Slot have been found ( from <span style="text-decoration: underline;">Slot Time Application</span> )</li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr style="font-weight: bold;">
        <td width="5%" align='center'>No</<td>
        <td width="11%" align='center'>Rute</td>
        <td width="8%" align='center'>Tipe<br />Pesawat</td>
        <td width="10%" align='center'>Nomor<br />Penerbangan</td>
        <td width="8%" align='center'>ETD<br />(UTC)</td>
        <td width="8%" align='center'>ETA<br />(UTC)</td>
        <td width="8%" align='center'>Hari<br />Operasi</td>
        <td width="8%" align='center'>RON</td>
        <td width="8%" align='center'>Frekuensi</td>
        <td width="8%" align='center'>Services<br />Type</td>
        <td width="18%" align='center'>Tanggal Efektif</td>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>

    <?php if ($_smarty_tpl->getVariable('data')->value->serviceTypeArrival!=' '&&$_smarty_tpl->getVariable('data')->value->lastStation==$_smarty_tpl->getVariable('search')->value['rute_to']&&($_smarty_tpl->getVariable('search')->value['flight_no']==''||sprintf("%.2f",$_smarty_tpl->getVariable('data')->value->serviceNoArrival)==$_smarty_tpl->getVariable('search')->value['flight_no'])){?>
    <tr>        
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->lastStation;?>
-<b><?php echo $_smarty_tpl->getVariable('data')->value->airportCode;?>
</b></td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->serviceNoArrival;?>
</td>
        <td align="center">-</td>
        <td align="center"><?php echo substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,0,2);?>
:<?php echo substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,2,2);?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('dtm')->value->get_frequensi_dos($_smarty_tpl->getVariable('data')->value->doop);?>
 X</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->serviceTypeArrival;?>
</td>
        <td align="center">
            <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins')));?>

            /
            <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins')));?>

        </td>
    </tr>
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('data')->value->serviceTypeDeparture!=' '&&$_smarty_tpl->getVariable('data')->value->nextStation==$_smarty_tpl->getVariable('search')->value['rute_to']&&($_smarty_tpl->getVariable('search')->value['flight_no']==''||sprintf("%.2f",$_smarty_tpl->getVariable('data')->value->serviceNoDeparture)==$_smarty_tpl->getVariable('search')->value['flight_no'])){?>
    <tr>      
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>  
        <td align="center"><b><?php echo $_smarty_tpl->getVariable('data')->value->airportCode;?>
</b>-<?php echo $_smarty_tpl->getVariable('data')->value->nextStation;?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->serviceNoDeparture;?>
</td>
        <td align="center"><?php echo substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,0,2);?>
:<?php echo substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,2,2);?>
</td>
        <td align="center">-</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('dtm')->value->get_frequensi_dos($_smarty_tpl->getVariable('data')->value->doop);?>
 X</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('data')->value->serviceTypeDeparture;?>
</td>
        <td align="center">
            <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),'ins')));?>

            /
            <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10),'ins')));?>

        </td>
    </tr>
    <?php }?>
    <?php }} else { ?>
    <tr>
        <td colspan="11">Isikan parameter pencarian diatas ini secara lengkap! ( Services Type = kosong untuk menampilkan semua jenis penerbangan )</td>
    </tr>
    <?php } ?>
</table>
