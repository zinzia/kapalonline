<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 05:30:17
         compiled from "application/views\dashboard/welcome/default.html" */ ?>
<?php /*%%SmartyHeaderCode:258455600cb495963e6-34101985%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5233b63575428026696b2225b150c4bf8e54e6d8' => 
    array (
      0 => 'application/views\\dashboard/welcome/default.html',
      1 => 1441883418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '258455600cb495963e6-34101985',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="dashboard-welcome">
    <h3><?php echo $_smarty_tpl->getVariable('tanggal')->value['hari'];?>
, <?php echo $_smarty_tpl->getVariable('tanggal')->value['tanggal'];?>
</h3>
    <div class="dashboard-profile">
        <div style="float: left;">
            <div class="dashboard-profile-sidebar">
                <h4>Angkutan Udara Niaga Berjadwal</h4>
                <h5><?php if (!empty($_smarty_tpl->getVariable('task_berjadwal',null,true,false)->value)){?><?php echo $_smarty_tpl->getVariable('task_berjadwal')->value['task_nm'];?>
<?php }else{ ?>Tidak punya tugas disini!<?php }?></h5>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_berjadwal')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Waiting / On Process</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_berjadwal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($_smarty_tpl->tpl_vars['data']->value['task_link']).('/index/')).($_smarty_tpl->tpl_vars['data']->value['data_id']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
</b>
                                <br />
                                Type :
                                <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm']));?>
 
                                <br />
                                <small>
                                    Penerbangan :
                                    <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']))))===null||$tmp==='' ? '' : $tmp);?>

                                    -
                                    <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']))))===null||$tmp==='' ? '' : $tmp);?>

                                    /
                                    <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start_upto']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start_upto']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start_upto']))))===null||$tmp==='' ? '' : $tmp);?>

                                    -
                                    <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end_upto']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end_upto']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end_upto']))))===null||$tmp==='' ? '' : $tmp);?>

                                    <br />
                                    Rute :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all']));?>

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
                <h4>Angkutan Udara Niaga Tidak Berjadwal dan Bukan Niaga</h4>
                <h5><?php if (!empty($_smarty_tpl->getVariable('task_tidak_berjadwal',null,true,false)->value)){?><?php echo $_smarty_tpl->getVariable('task_tidak_berjadwal')->value['task_nm'];?>
<?php }else{ ?>Tidak punya tugas disini!<?php }?></h5>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_tidak_berjadwal')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Waiting / On Process</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_tidak_berjadwal')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($_smarty_tpl->tpl_vars['data']->value['task_link']).('/index/')).($_smarty_tpl->tpl_vars['data']->value['registration_code']));?>
">
                                <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airlines_nm']));?>
</b>
                                <br />
                                Type :
                                <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm']));?>
 
                                <br />
                                <small>
                                    Penerbangan :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_start'])));?>

                                    -
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['data']->value['date_end'])));?>

                                    <br />
                                    Rute :
                                    <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['rute_all']));?>

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
            <div class="clear"></div>
            <div class="dashboard-profile-sidebar">
                <h4>Izin Rute Penerbangan Domestik</h4>
                <div class="info">
                    <p><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('total_waiting_domestik')->value)===null||$tmp==='' ? '0' : $tmp),0,',','.');?>
 Waiting / On Process</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_domestik')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((((($_smarty_tpl->tpl_vars['data']->value['task_link']).('/')).($_smarty_tpl->tpl_vars['data']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['data']->value['registrasi_id']));?>
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
 Waiting / On Process</p>
                </div>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_waiting_internasional')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <p>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((((($_smarty_tpl->tpl_vars['data']->value['task_link']).('/')).($_smarty_tpl->tpl_vars['data']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['data']->value['registrasi_id']));?>
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
                <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('dashboard/account_settings/data_pribadi');?>
">Update my profile</a></li>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="dashboard-profile-content">
            <br />
            <h5>Statistik Pelayanan Flight Approval <?php echo $_smarty_tpl->getVariable('tanggal')->value['tahun'];?>
</h5>
            <div id="chart">
                <script type="text/javascript">
                    var chart = new FusionCharts("<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/charts/StackedColumn2D.swf", "chart", "100%", "285", "0", "1");
                    chart.setDataURL("<?php echo $_smarty_tpl->getVariable('config')->value->site_url('dashboard/welcome/data_chart');?>
");
                    chart.render("chart");
                </script> 
            </div>

            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
