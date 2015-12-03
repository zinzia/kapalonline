<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:59:03
         compiled from "application/views\stakeholder/welcome/default.html" */ ?>
<?php /*%%SmartyHeaderCode:27100565691975e36a8-13228214%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b7d573df83be7f8be7b51b4b3650712b1ac44c0' => 
    array (
      0 => 'application/views\\stakeholder/welcome/default.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27100565691975e36a8-13228214',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="dashboard-welcome">
    <h3><?php echo $_smarty_tpl->getVariable('tanggal')->value['hari'];?>
, <?php echo $_smarty_tpl->getVariable('tanggal')->value['tanggal'];?>
</h3>
    <div class="search-box">
        <h2><a href="#">Search</a></h2>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/welcome/proses_cari');?>
" method="post">
            <table class="table-search" width="100%" border="0">
                <tr>
                    <th width="5%">Nomor</th>
                    <td width="15%">
                        <input name="published_no" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['published_no'])===null||$tmp==='' ? '' : $tmp);?>
" size="40" maxlength="40" />
                    </td>
                    <th width="5%">Operator</th>
                    <td width="15%">
                        <input name="airlines_nm" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="50" />
                    </td>
                    <th width="5%">Bandara</th>
                    <td width="15%">
                        <input name="airport_iata_cd" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['airport_iata_cd'])===null||$tmp==='' ? '' : $tmp);?>
" size="3" maxlength="3" />
                    </td>
                    <td align="right">
                        <input name="save" type="submit" value="Tampilkan" />
                        <input name="save" type="submit" value="Reset" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="dashboard-profile">
        <div style="float: left;">
            <div class="dashboard-profile-sidebar">
                <h4>Angkutan Udara Niaga Berjadwal</h4>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_berjadwal')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Flight Approval</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_berjadwal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('stakeholder/realisasi/form/').($_smarty_tpl->tpl_vars['data']->value['data_id']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['published_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['published_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['published_no']));?>
</b>
                                <br />
                                Nomor Penerbangan : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['flight_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['flight_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['flight_no']));?>

                                <br />
                                Type : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm']));?>
 
                                <br />
                                <small>
                                    Penerbangan :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start'])));?>

                                    -
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end'])));?>

                                    <br />
                                    Rute :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all']));?>

                                </small>
                            </a>
                        </p>
                        <?php }} else { ?>
                        <p>
                            <a href="#">No request found</a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="dashboard-profile-sidebar">
                <h4>Angkutan Udara Niaga Tidak Berjadwal dan Bukan Niaga</h4>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_tidak_berjadwal')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Flight Approval</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_tidak_berjadwal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('stakeholder/realisasi/form/').($_smarty_tpl->tpl_vars['data']->value['data_id']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['published_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['published_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['published_no']));?>
</b>
                                <br />
                                Nomor Penerbangan : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['flight_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['flight_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['flight_no']));?>

                                <br />
                                Type : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm']));?>
 
                                <br />
                                <small>
                                    Penerbangan :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start'])));?>

                                    -
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end'])));?>

                                    <br />
                                    Rute :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all']));?>

                                </small>
                            </a>
                        </p>
                        <?php }} else { ?>
                        <p>
                            <a href="#">No request found</a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="dashboard-profile-sidebar">
                <h4>Izin Rute Penerbangan Domestik</h4>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_domestik')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Izin Rute</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_domestik')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('stakeholder/published_izin/').($_smarty_tpl->tpl_vars['data']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['data']->value['registrasi_id']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
</b>
                                <br />
                                Permohonan : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['group_nm']));?>

                                <br />
                                <small>
                                    Rute : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end']));?>

                                    <br />
                                    <?php if ($_smarty_tpl->tpl_vars['data']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],0,2)==00){?>
                                    <?php echo substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],3,2);?>
 minutes ago
                                    <?php }elseif($_smarty_tpl->tpl_vars['data']->value['selisih_hari']==0){?>
                                    <?php echo substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],0,2);?>
 hour ago
                                    <?php }else{ ?>
                                    <?php echo $_smarty_tpl->tpl_vars['data']->value['selisih_hari'];?>
 day ago
                                    <?php }?>
                                </small>
                            </a>
                        </p>
                        <?php }} else { ?>
                        <p>
                            <a href="#">No request found</a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="dashboard-profile-sidebar">
                <h4>Izin Rute Penerbangan Internasional</h4>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_internasional')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Izin Rute</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_internasional')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('stakeholder/published_izin/').($_smarty_tpl->tpl_vars['data']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['data']->value['registrasi_id']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
</b>
                                <br />
                                Permohonan : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['group_nm']));?>

                                <br />
                                <small>
                                    Rute : <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['izin_rute_end']));?>

                                    <br />
                                    <?php if ($_smarty_tpl->tpl_vars['data']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],0,2)==00){?>
                                    <?php echo substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],3,2);?>
 minutes ago
                                    <?php }elseif($_smarty_tpl->tpl_vars['data']->value['selisih_hari']==0){?>
                                    <?php echo substr($_smarty_tpl->tpl_vars['data']->value['selisih_waktu'],0,2);?>
 hour ago
                                    <?php }else{ ?>
                                    <?php echo $_smarty_tpl->tpl_vars['data']->value['selisih_hari'];?>
 day ago
                                    <?php }?>
                                </small>
                            </a>
                        </p>
                        <?php }} else { ?>
                        <p>
                            <a href="#">No request found</a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-profile-content">
            <img src="<?php echo $_smarty_tpl->getVariable('com_user')->value['operator_photo'];?>
" alt="" class="user-img" />
            <ul>
                <li><b><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_name'];?>
, <br />(<?php echo $_smarty_tpl->getVariable('com_user')->value['role_nm'];?>
)</b><br /><?php echo $_smarty_tpl->getVariable('com_user')->value['sub_direktorat'];?>
</li>
                <li><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/phone.icon.png" alt="" /><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_phone'];?>
</li>
                <li><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/address.icon.png" alt="" /><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_address'];?>
</li>
                <li><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/email.icon.png" alt="" /><?php echo $_smarty_tpl->getVariable('com_user')->value['user_mail'];?>
</li>
                <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/account_settings/data_pribadi');?>
">Update my profile</a></li>
            </ul>
            <div class="clear"></div>
        </div>
<!--         <div class="dashboard-profile-content">
            <br />
            <h5>Statistik Pelayanan Flight Approval <?php echo $_smarty_tpl->getVariable('tanggal')->value['tahun'];?>
</h5>
            <div id="chart">
                <script type="text/javascript">
                    var chart = new FusionCharts("<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/charts/StackedColumn2D.swf", "chart", "100%", "285", "0", "1");
                    chart.setDataURL("<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/data_chart');?>
");
                    chart.render("chart");
                </script> 
            </div>

            <div class="clear"></div>
        </div>
 -->        <div class="clear"></div>
    </div>
</div>
