<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:00:15
         compiled from "application/views\pengaturan/airlines/list.html" */ ?>
<?php /*%%SmartyHeaderCode:1546356554e6f44d7b7-69203797%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aecc0f171a0701ebff1b57e4c5d154efb66c7a4e' => 
    array (
      0 => 'application/views\\pengaturan/airlines/list.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1546356554e6f44d7b7-69203797',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>        
        <a href="#">Settings</a><span></span>
        <small>Operator Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/airlines/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="8%">Nama</th>
                <td width="10%">
                    <input name="airlines" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['airlines'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="50" />
                </td>
                <th width="8%">Brand</th>
                <td width="10%">
                    <input name="brand" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['brand'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="50" />
                </td>
                <th width="8%">IATA</th>
                <td width="10%">
                    <input name="iata" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['iata'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="5" />
                </td>
                <th width="8%">ICAO</th>
                <td width="10%">
                    <input name="icao" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['icao'])===null||$tmp==='' ? '' : $tmp);?>
" size="5" maxlength="5" />
                </td>
                <td width="28%" align='right'>
                    <input class="blue" name="save" type="submit" value="Tampilkan" />
                    <input class="orange" name="save" type="submit" value="Reset" />
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
</strong>&nbsp;Record&nbsp;</li><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['data'])===null||$tmp==='' ? '' : $tmp);?>

            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/airlines/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
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
        <th width="5%">No</th>
        <th width="25%">Nama Operator</th>
        <th width="18%">Nama Brand</th>
        <th width="10%">IATA Code</th>
        <th width="10%">ICAO Code</th>
        <th width="20%">Jenis Penerbangan</th>
        <th width="12%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airlines')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_brand'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_brand'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_brand'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['airlines_iata_cd'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['airlines_icao_cd'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_flight_type'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_flight_type'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_flight_type'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/airlines/edit/').($_smarty_tpl->tpl_vars['result']->value['airlines_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/airlines/delete/').($_smarty_tpl->tpl_vars['result']->value['airlines_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>