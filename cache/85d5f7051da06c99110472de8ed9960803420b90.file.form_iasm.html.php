<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 09:57:43
         compiled from "application/views\izin_domestik/perpanjangan/form_iasm.html" */ ?>
<?php /*%%SmartyHeaderCode:31416560109f7e1ddd7-88856104%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85d5f7051da06c99110472de8ed9960803420b90' => 
    array (
      0 => 'application/views\\izin_domestik/perpanjangan/form_iasm.html',
      1 => 1442892356,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31416560109f7e1ddd7-88856104',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_domestik/perpanjangan/rute_data_search_process');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="izin_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('rute')->value['izin_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-info" width='100%'>
        <tr>
            <th>Input Data Rute Berdasarkan Slot Dibawah ini!</th>
        </tr>            
        <tr>
            <td align='center'>
                <select name="rute_all">
                    <option value="<?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
" <?php if ($_smarty_tpl->getVariable('detail')->value['izin_rute_start']==$_smarty_tpl->getVariable('search')->value['rute_all']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
</option>
                    <option value="<?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
" <?php if ($_smarty_tpl->getVariable('detail')->value['izin_rute_end']==$_smarty_tpl->getVariable('search')->value['rute_all']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
</option>
                </select>
                <input name="season_code" type="text" value="<?php echo $_smarty_tpl->getVariable('season_cd')->value;?>
" size="5" maxlength="3" style="text-align: center;" readonly="readonly" />
                <input name="services_code" type="text" value="<?php echo $_smarty_tpl->getVariable('service_type')->value;?>
" size="5" maxlength="1" style="text-align: center;" readonly="readonly"  />
                <input name="flight_no" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['flight_no'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="6" placeholder="Filght Number" style="text-align: center;" />
                <input name="save" type="submit" value="View" class="button-edit" />
                <input name="save" type="submit" value="Reset" class="button-edit" />
            </td>
        </tr>
    </table>
</form>
<div class="clear"></div>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_domestik/perpanjangan/rute_add_process_iasm');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="izin_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('rute')->value['izin_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <div style="float: left; width: 49%;">
        <table class="table-view" width='100%'>
            <tr style="font-weight: bold;">
				<td align="center"> Rute </td> 
				<td align="center"> Tipe</td> 
				<td align="center"> Nomor</td> 
				<td align="center"> ETD </td> 
				<td align="center"> DOS </td> 
				<td align="center"> RON </td> 
				<td align="center"> Start / End</td> 
				<td> </td>
			</tr>	
			<?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_depart')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <?php if ($_smarty_tpl->getVariable('data')->value->serviceTypeDeparture!=' '&&$_smarty_tpl->getVariable('data')->value->nextStation==$_smarty_tpl->getVariable('rute_to')->value&&($_smarty_tpl->getVariable('search')->value['flight_no']==''||sprintf("%.2f",$_smarty_tpl->getVariable('data')->value->serviceNoDeparture)==$_smarty_tpl->getVariable('search')->value['flight_no'])){?>
            <?php $_smarty_tpl->tpl_vars['used'] = new Smarty_variable($_smarty_tpl->getVariable('m_slot_check')->value->is_slot_used_departure(array($_smarty_tpl->getVariable('com_user')->value['airlines_id'],((($_smarty_tpl->getVariable('data')->value->airportCode).('-')).($_smarty_tpl->getVariable('data')->value->nextStation)),$_smarty_tpl->getVariable('data')->value->aircraftType,$_smarty_tpl->getVariable('data')->value->numSeats,$_smarty_tpl->getVariable('data')->value->serviceNoDeparture,((((substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,0,2))).(':')).((substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,2,2)))).(':00'),$_smarty_tpl->getVariable('data')->value->doop,((($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp)),substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),substr($_smarty_tpl->getVariable('data')->value->endDate,0,10))), null, null);?>
            <?php $_smarty_tpl->tpl_vars['local'] = new Smarty_variable($_smarty_tpl->getVariable('m_slot_check')->value->get_slot_local_time($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,$_smarty_tpl->getVariable('data')->value->doop,((($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp)),$_smarty_tpl->getVariable('local_time_from')->value['airport_utc_sign'],$_smarty_tpl->getVariable('local_time_from')->value['airport_utc'],(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10)),(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10))), null, null);?>
            <!-- parameters -->
            <input type="hidden" name="departure_rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($_smarty_tpl->getVariable('data')->value->airportCode).('-')).($_smarty_tpl->getVariable('data')->value->nextStation);?>
" />
            <input type="hidden" name="departure_aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" />
            <input type="hidden" name="departure_capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" />
            <input type="hidden" name="departure_flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoDeparture;?>
" />
            <input type="hidden" name="departure_etd_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo ((((substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,0,2))).(':')).((substr($_smarty_tpl->getVariable('data')->value->clearedTimeDeparture,2,2)))).(':00');?>
" />
            <input type="hidden" name="departure_doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
" />
            <input type="hidden" name="departure_roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
" />
            <input type="hidden" name="departure_start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->startDate,0,10);?>
" />
            <input type="hidden" name="departure_end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->endDate,0,10);?>
" />
            <!-- end of parameters -->
            <tr>  
                <td align="center" width='15%'><b><?php echo $_smarty_tpl->getVariable('data')->value->airportCode;?>
</b>-<?php echo $_smarty_tpl->getVariable('data')->value->nextStation;?>
</td>
                <td align="center" width='10%'><?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
</td>
                <td align="center" width='10%'><?php echo $_smarty_tpl->getVariable('data')->value->serviceNoDeparture;?>
</td>
                <td align="center" width='10%'><?php echo substr($_smarty_tpl->getVariable('local')->value['local_time'],0,5);?>
</td>
                <td align="center" width='15%'><?php echo $_smarty_tpl->getVariable('local')->value['dos'];?>
</td>
                <td align="center" width='5%'><?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
</td>
                <td align="center" width='30%' style="font-size: 10px;">
                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins')));?>

                    /
                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins')));?>

                </td>
                <td align="center" width='5%'>
                    <?php if ($_smarty_tpl->getVariable('used')->value=='0'){?>
                    <input type="radio" name="departure_izin_data_slot" value="<?php echo $_smarty_tpl->getVariable('no')->value;?>
" />
                    <?php }?>
                </td>
            </tr>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>
            <?php }?>
            <?php }} else { ?>
            <tr>
                <td colspan="8">Tidak ditemukan data slot departure!</td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div style="float: right; width: 49%;">
        <table class="table-view" width='100%'>
			<tr style="font-weight: bold;">
				<td align="center"> Rute </td> 
				<td align="center"> Tipe</td> 
				<td align="center"> Nomor</td> 
				<td align="center"> ETA </td> 
				<td align="center"> DOS </td> 
				<td align="center"> RON </td> 
				<td align="center"> Start / End</td> 
				<td> </td>
			</tr>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_arrive')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
            <?php if ($_smarty_tpl->getVariable('data')->value->serviceTypeArrival!=' '&&$_smarty_tpl->getVariable('data')->value->lastStation==$_smarty_tpl->getVariable('rute_from')->value&&($_smarty_tpl->getVariable('search')->value['flight_no']==''||sprintf("%.2f",$_smarty_tpl->getVariable('data')->value->serviceNoArrival)==$_smarty_tpl->getVariable('search')->value['flight_no'])){?>
            <?php $_smarty_tpl->tpl_vars['used'] = new Smarty_variable($_smarty_tpl->getVariable('m_slot_check')->value->is_slot_used_arrival(array($_smarty_tpl->getVariable('com_user')->value['airlines_id'],((($_smarty_tpl->getVariable('data')->value->lastStation).('-')).($_smarty_tpl->getVariable('data')->value->airportCode)),$_smarty_tpl->getVariable('data')->value->aircraftType,$_smarty_tpl->getVariable('data')->value->numSeats,$_smarty_tpl->getVariable('data')->value->serviceNoArrival,((((substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,0,2))).(':')).((substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,2,2)))).(':00'),$_smarty_tpl->getVariable('data')->value->doop,((($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp)),substr($_smarty_tpl->getVariable('data')->value->startDate,0,10),substr($_smarty_tpl->getVariable('data')->value->endDate,0,10))), null, null);?>
            <?php $_smarty_tpl->tpl_vars['local'] = new Smarty_variable($_smarty_tpl->getVariable('m_slot_check')->value->get_slot_local_time($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,$_smarty_tpl->getVariable('data')->value->doop,((($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp)),$_smarty_tpl->getVariable('local_time_to')->value['airport_utc_sign'],$_smarty_tpl->getVariable('local_time_to')->value['airport_utc'],(substr($_smarty_tpl->getVariable('data')->value->startDate,0,10)),(substr($_smarty_tpl->getVariable('data')->value->endDate,0,10))), null, null);?>
            <!-- parameters -->
            <input type="hidden" name="arrival_rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($_smarty_tpl->getVariable('data')->value->lastStation).('-')).($_smarty_tpl->getVariable('data')->value->airportCode);?>
" />
            <input type="hidden" name="arrival_aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" />
            <input type="hidden" name="arrival_capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" />
            <input type="hidden" name="arrival_flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoArrival;?>
" />
            <input type="hidden" name="arrival_eta_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo ((((substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,0,2))).(':')).((substr($_smarty_tpl->getVariable('data')->value->clearedTimeArrival,2,2)))).(':00');?>
" />
            <input type="hidden" name="arrival_doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
" />
            <input type="hidden" name="arrival_roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
" />
            <input type="hidden" name="arrival_start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->startDate,0,10);?>
" />
            <input type="hidden" name="arrival_end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->endDate,0,10);?>
" />
            <!-- end of parameters -->
            <tr>  
                <td align="center" width='15%'><?php echo $_smarty_tpl->getVariable('data')->value->lastStation;?>
-<b><?php echo $_smarty_tpl->getVariable('data')->value->airportCode;?>
</b></td>
                <td align="center" width='10%'><?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
</td>
                <td align="center" width='10%'><?php echo $_smarty_tpl->getVariable('data')->value->serviceNoArrival;?>
</td>
                <td align="center" width='10%'><?php echo substr($_smarty_tpl->getVariable('local')->value['local_time'],0,5);?>
</td>
                <td align="center" width='15%'><?php echo $_smarty_tpl->getVariable('local')->value['dos'];?>
</td>
                <td align="center" width='5%'><?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
</td>
                <td align="center" width='30%' style="font-size: 10px;">
                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10),'ins')));?>

                    /
                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date(substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10),'ins')));?>

                </td>
                <td align="center" width='5%'>
                    <?php if ($_smarty_tpl->getVariable('used')->value=='0'){?>
                    <input type="radio" name="arrival_izin_data_slot" value="<?php echo $_smarty_tpl->getVariable('no')->value;?>
" />
                    <?php }?>
                </td>
            </tr>
            <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>
            <?php }?>
            <?php }} else { ?>
            <tr>
                <td colspan="8">Tidak ditemukan data slot arrival!</td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="clear"></div>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td colspan="2">
                Pastikan data yang anda masukkan adalah data yang benar!
            </td>
            <td colspan="2" align='right'>
                <input type="submit" name="save" value="Process" class="submit-button" />
            </td>
        </tr>
    </table>
</form>