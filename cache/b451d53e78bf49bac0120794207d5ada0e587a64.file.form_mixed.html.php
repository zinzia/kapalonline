<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:54:22
         compiled from "application/views\izin_internasional/perpanjangan/form_mixed.html" */ ?>
<?php /*%%SmartyHeaderCode:106915600defeb1c152-88739871%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b451d53e78bf49bac0120794207d5ada0e587a64' => 
    array (
      0 => 'application/views\\izin_internasional/perpanjangan/form_mixed.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '106915600defeb1c152-88739871',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perpanjangan/rute_data_search_process');?>
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
<h4>Data slot clearance yang telah dikonversi kedalam local time</h4>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perpanjangan/rute_add_process_mixed');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="izin_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('rute')->value['izin_id'])===null||$tmp==='' ? '' : $tmp);?>
">
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
    <input type="hidden" name="services_cd" value="departure">
    <input type="hidden" name="slot_rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($_smarty_tpl->getVariable('data')->value->airportCode).('-')).($_smarty_tpl->getVariable('data')->value->nextStation);?>
" />
    <input type="hidden" name="slot_aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" />
    <input type="hidden" name="slot_capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" />
    <input type="hidden" name="slot_flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoDeparture;?>
" />
    <input type="hidden" name="slot_etd_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->clearedTimeDeparture;?>
" />
    <input type="hidden" name="slot_doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
" />
    <input type="hidden" name="slot_roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
" />
    <input type="hidden" name="slot_start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->startDate,0,10);?>
" />
    <input type="hidden" name="slot_end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->endDate,0,10);?>
" />
    <!-- end of parameters -->
	
	<?php if ($_smarty_tpl->getVariable('no')->value==1){?>
    <table class="table-form" width='100%'> 
		<tr style="font-weight: bold;">
			<td align='center' width='4%'></td>
			<td align='center' width='8%'>Rute</td>
			<td align='center' width='8%'>Tipe</td>
			<td align='center' width='8%'>Kapasitas</td>
			<td align='center' width='6%'>Nomor</td>
			<td align='center' width='8%'>ETD</td>
			<td align='center' width='15%'>ETA</td>
			<td align='center' width='8%'>DOS</td>
			<td align='center' width='8%'>RON</td>
			<td>&nbsp&nbsp&nbsp&nbsp Start &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp End</td>
			<td align='center'></td>
		</tr> 
	</table>
	<?php }?>
	
    <table class="table-form" width='100%'>
        <tr>
            <td align='center' width='4%'><?php echo $_smarty_tpl->getVariable('no')->value;?>
.</td>
            <td align='center' width='7%'>
                <input type="text" name="rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="7" maxlength="7" value="<?php echo (($_smarty_tpl->getVariable('data')->value->airportCode).('-')).($_smarty_tpl->getVariable('data')->value->nextStation);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoDeparture;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="etd_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="6" maxlength="5" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['local_time'],0,5);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='15%'>
                <input type="text" name="eta_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="15" maxlength="5" value="<?php echo substr((($tmp = @$_smarty_tpl->getVariable('result')->value['eta'])===null||$tmp==='' ? '' : $tmp),0,5);?>
" style="text-align: center; background-color: #FFD3DF; border: 1px solid #FF8CAC;" class="waktu" readonly="readonly" placeholder="ETA ( Local Time )" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="7" maxlength="7" value="<?php echo $_smarty_tpl->getVariable('local')->value['dos'];?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="2" maxlength="1" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td width='28%'>
                <input type="text" name="start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="8" maxlength="10" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10);?>
" readonly="readonly" style="text-align: center;" />
                <input type="text" name="end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="8" maxlength="10" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10);?>
" readonly="readonly" style="text-align: center;" />
            </td>
            <td align='center' width='4%'>
                <?php if ($_smarty_tpl->getVariable('used')->value=='0'){?>
                <input type="submit" name="save[<?php echo $_smarty_tpl->getVariable('no')->value;?>
]" value="+" class="submit-button" /> 
                <?php }?>
            </td>
        </tr>
    </table>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>
    <?php }?>
    <?php }} ?>
</form>
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perpanjangan/rute_add_process_mixed');?>
" method="post">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="izin_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('rute')->value['izin_id'])===null||$tmp==='' ? '' : $tmp);?>
">
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
    <input type="hidden" name="services_cd" value="arrival">
    <input type="hidden" name="slot_rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($_smarty_tpl->getVariable('data')->value->lastStation).('-')).($_smarty_tpl->getVariable('data')->value->airportCode);?>
" />
    <input type="hidden" name="slot_aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" />
    <input type="hidden" name="slot_capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" />
    <input type="hidden" name="slot_flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoArrival;?>
" />
    <input type="hidden" name="slot_eta_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->clearedTimeArrival;?>
" />
    <input type="hidden" name="slot_doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('data')->value->doop;?>
" />
    <input type="hidden" name="slot_roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? 0 : $tmp);?>
" />
    <input type="hidden" name="slot_start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->startDate,0,10);?>
" />
    <input type="hidden" name="slot_end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" value="<?php echo substr($_smarty_tpl->getVariable('data')->value->endDate,0,10);?>
" />
    <!-- end of parameters -->
	
	<?php if ($_smarty_tpl->getVariable('no')->value==1){?>
    <table class="table-form" width='100%'> 
		<tr style="font-weight: bold;">
			<td align='center' width='4%'></td>
			<td align='center' width='8%'>Rute</td>
			<td align='center' width='8%'>Tipe</td>
			<td align='center' width='8%'>Kapasitas</td>
			<td align='center' width='6%'>Nomor</td>
			<td align='center' width='15%'>ETD</td>
			<td align='center' width='8%'>ETA</td>
			<td align='center' width='8%'>DOS</td>
			<td align='center' width='8%'>RON</td>
			<td>&nbsp&nbsp&nbsp&nbsp Start &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp End</td>
			<td align='center'></td>
		</tr> 
	</table>
	<?php }?>
	
    <table class="table-form" width='100%'> 
        <tr>
			<td align='center' width='4%'><?php echo $_smarty_tpl->getVariable('no')->value;?>
.</td>
            <td align='center' width='7%'>
                <input type="text" name="rute_all_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="7" maxlength="7" value="<?php echo (($_smarty_tpl->getVariable('data')->value->lastStation).('-')).($_smarty_tpl->getVariable('data')->value->airportCode);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="aircraft_type_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->aircraftType;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="capacity_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->numSeats;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="flight_no_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="5" maxlength="5" value="<?php echo $_smarty_tpl->getVariable('data')->value->serviceNoArrival;?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='15%'>
                <input type="text" name="etd_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="15" maxlength="5" value="<?php echo substr((($tmp = @$_smarty_tpl->getVariable('result')->value['etd'])===null||$tmp==='' ? '' : $tmp),0,5);?>
" style="text-align: center; background-color: #FFD3DF; border: 1px solid #FF8CAC;" class="waktu" readonly="readonly" placeholder="ETD ( Local Time )" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="eta_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="6" maxlength="5" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['local_time'],0,5);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="doop_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="7" maxlength="7" value="<?php echo $_smarty_tpl->getVariable('local')->value['dos'];?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td align='center' width='7%'>
                <input type="text" name="roon_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="2" maxlength="1" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('data')->value->turnaroundDays)===null||$tmp==='' ? '0' : $tmp);?>
" style="text-align: center;" readonly="readonly" />
            </td>
            <td width='28%'>
                <input type="text" name="start_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="8" maxlength="10" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['start_date'],0,10);?>
" readonly="readonly" style="text-align: center;" />
                <input type="text" name="end_date_<?php echo $_smarty_tpl->getVariable('no')->value;?>
" size="8" maxlength="10" value="<?php echo substr($_smarty_tpl->getVariable('local')->value['end_date'],0,10);?>
" readonly="readonly" style="text-align: center;" />
            </td>
            <td align='center' width='8%'>
                <?php if ($_smarty_tpl->getVariable('used')->value=='0'){?>
                <input type="submit" name="save[<?php echo $_smarty_tpl->getVariable('no')->value;?>
]" value="+" class="submit-button" /> 
                <?php }?>
            </td>
        </tr>
    </table>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable($_smarty_tpl->getVariable('no')->value+1, null, null);?>
    <?php }?>
    <?php }} ?>
</form>