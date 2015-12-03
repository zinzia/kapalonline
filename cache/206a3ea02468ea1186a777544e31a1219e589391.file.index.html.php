<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 16:09:48
         compiled from "application/views\member/pending/index.html" */ ?>
<?php /*%%SmartyHeaderCode:29629565720bc8d1bd9-28283405%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '206a3ea02468ea1186a777544e31a1219e589391' => 
    array (
      0 => 'application/views\\member/pending/index.html',
      1 => 1441883438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29629565720bc8d1bd9-28283405',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'C:\xampp\htdocs\kapalonline\system\plugins\smarty\libs\plugins\modifier.date_format.php';
?><div class="breadcrum">
    <p>
        <a href="#">Request Pending</a><span></span>
        <small>Flight Approval</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total Pending ( <b style="color: red;"><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> ) Permohonan. <u>Mohon segera di perbaiki dan dikirimkan kembali!</u></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width='4%'>No</th>
        <th width='10%'>Nomor Permohonan</th>
        <th width='12%'>Jenis Penerbangan</th>
        <th width='12%'>Domestik / <br />Internasional</th>
        <th width='15%'>Tanggal</th>
        <th width='15%'>Rute</th>
        <th width='17%'>Remark</th>
        <th width='15%'></th>
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
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['document_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['data_type'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['data_type'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['data_type']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['data_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight']));?>
</td>
        <td align="center">
            <?php if ((($tmp = @$_smarty_tpl->tpl_vars['result']->value['date_start'])===null||$tmp==='' ? '' : $tmp)=="0000-00-00"){?>
                <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d%b%y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d%b%y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d%b%y")));?>

            <?php }else{ ?>
                <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d%b%y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d%b%y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d%b%y")));?>

            <?php }?>
            /
            <?php if ((($tmp = @$_smarty_tpl->tpl_vars['result']->value['date_end'])===null||$tmp==='' ? '' : $tmp)=="0000-00-00"){?>
                <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d%b%y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d%b%y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d%b%y")));?>

            <?php }else{ ?>
                <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d%b%y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d%b%y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d%b%y")));?>

            <?php }?>
        </td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['rute_all'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['rute_all']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['services_nm']));?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @(((($tmp = @('member/pending/detail/').($_smarty_tpl->tpl_vars['result']->value['group_id']))===null||$tmp==='' ? '' : $tmp)).('/')).($_smarty_tpl->tpl_vars['result']->value['data_id']))===null||$tmp==='' ? '' : $tmp));?>
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
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="8">Data not found!</td>
    </tr>
    <?php } ?>
</table>