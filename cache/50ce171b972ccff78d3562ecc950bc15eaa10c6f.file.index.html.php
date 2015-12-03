<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 07:16:49
         compiled from "application/views\task/manager/index.html" */ ?>
<?php /*%%SmartyHeaderCode:10310565e8cd184d109-10737357%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '50ce171b972ccff78d3562ecc950bc15eaa10c6f' => 
    array (
      0 => 'application/views\\task/manager/index.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10310565e8cd184d109-10737357',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'C:\xampp\htdocs\kapalonline\system\plugins\smarty\libs\plugins\modifier.date_format.php';
?><div class="breadcrum">
    <p>
        <a href="#">Task Manager</a><span></span>
        <small><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('com_user')->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm']));?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info"><strong><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</strong> Record&nbsp;</li>
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
        <th width='20%'>Operator</th>
        <th width='13%'>Nomor Permohonan</th>
        <th width='17%'>Jenis Penerbangan</th>
        <th width='24%'>Tanggal</th>
        <th width='5%'>Jumlah</th>
        <th width='4%'></th>
        <th width='13%'></th>
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
        <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm']));?>
</b></td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['document_no'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['document_no']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['data_type'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['data_type'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['data_type']));?>
 <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['data_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['data_flight']));?>
</td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['data_flight']=="domestik"){?>
                Tanggal : <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y")));?>
 / <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y")));?>

            <?php }else{ ?>
                <?php if ($_smarty_tpl->tpl_vars['result']->value['date_start']=="0000-00-00"){?>
                Tangal Masuk : - / -
                <?php }else{ ?>
                Tangal Masuk : <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start'],"%d %b %Y")));?>
 / <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_start_upto'],"%d %b %Y")));?>

                <?php }?>
                <br />
                <?php if ($_smarty_tpl->tpl_vars['result']->value['date_start']=="0000-00-00"){?>
                Tangal Masuk : - / -
                <?php }else{ ?>
                Tangal Keluar : <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end'],"%d %b %Y")));?>
 / <?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d %b %Y"), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d %b %Y"),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['date_end_upto'],"%d %b %Y")));?>

                <?php }?>
            <?php }?>
        </td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['registration_total'];?>
</td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['notes']==''){?>
            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.notes.blue.png" alt="" title="Tidak ada catatan" />
            <?php }else{ ?>
            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/icon.notes.red.png" alt="" title="Ada catatan khusus" />
            <?php }?>
        </td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @(($_smarty_tpl->tpl_vars['result']->value['task_link']).('/index/')).($_smarty_tpl->tpl_vars['result']->value['data_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">
                <?php if ($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2)==00){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],3,2);?>
 minutes ago
                <?php }elseif($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2);?>
 hour ago
                <?php }else{ ?>
                <?php echo $_smarty_tpl->tpl_vars['result']->value['selisih_hari'];?>
 day ago
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