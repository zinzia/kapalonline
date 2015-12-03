<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:24:15
         compiled from "application/views\member/registration_scheduled/list.html" */ ?>
<?php /*%%SmartyHeaderCode:175375600e5ffa85f47-31085294%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79c1ff8a035906e29769bb023322a831d4a004e8' => 
    array (
      0 => 'application/views\\member/registration_scheduled/list.html',
      1 => 1441883438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175375600e5ffa85f47-31085294',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'C:\xampp\htdocs\development\angudonline\system\plugins\smarty\libs\plugins\modifier.date_format.php';
?><div class="breadcrum">
    <p>
        <a href="#">Registration</a><span></span>
        <small>Flight Approval Angkutan Udara Niaga Berjadwal</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total Pending ( <b style="color: red;">Belum Dikirim</b> ) <u><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
 Permohonan FA</u></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_scheduled/disclaimer/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/document_edit.png" alt="" />New Registration</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width='5%'>No</th>
        <th width='10%'>Domestik / <br />Internasional</th>
        <th width='18%'>Tanggal</th>
        <th width='15%'>Tanda Pendaftaran / Nomor Penerbangan</th>
        <th width='14%'>Rute</th>
        <th width='20%'>Remark</th>
        <th width='18%'></th>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['data_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight'])))===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center">
            <?php echo (($tmp = @((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"))))===null||$tmp==='' ? '-' : $tmp);?>
 / 
            <?php echo (($tmp = @((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"))))===null||$tmp==='' ? '-' : $tmp);?>

        </td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['flight_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no'])))===null||$tmp==='' ? '-' : $tmp);?>
/<?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['flight_no_2'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no_2'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['flight_no_2'])))===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all'])))===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['services_nm'])))===null||$tmp==='' ? '-' : $tmp);?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('member/registration_scheduled/add/').($_smarty_tpl->tpl_vars['result']->value['data_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">
                <?php if ($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2)==00){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],3,2);?>
 minutes
                <?php }elseif($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2);?>
 hour
                <?php }else{ ?>
                <?php echo $_smarty_tpl->tpl_vars['result']->value['selisih_hari'];?>
 day
                <?php }?>
            </a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('member/registration_scheduled/delete/').($_smarty_tpl->tpl_vars['result']->value['data_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus data ini?')">
                Delete
            </a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>