<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:05:49
         compiled from "application/views\pengaturan/aircraft/list.html" */ ?>
<?php /*%%SmartyHeaderCode:2371756554fbd15cc40-72807321%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3863973045d6a817c5cf6c8fa8ac198ada920613' => 
    array (
      0 => 'application/views\\pengaturan/aircraft/list.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2371756554fbd15cc40-72807321',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <small>Tipe Pesawat</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/aircraft/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Pembuat</th>
                <td width="20%">
                    <input id="tags_pembuat" name="manufacture" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['manufacture'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" />
                </td>
                <th width="10%">Tipe</th>
                <td width="20%">
                    <input id="tags_tipe" name="model" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['model'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="25" />
                </td>
                <td width='40%' align='right'>
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
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/aircraft/add');?>
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
        <th width="4%">No</th>
        <th width="20%">Pembuat</th>
        <th width="20%">Tipe</th>
        <th width="20%">Tahun</th>
        <th width="20%">Kapasitas Standard</th>
        <th width="16%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_aircraft')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['aircraft_manufacture'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_manufacture'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_manufacture'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['aircraft_model'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_model'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_model'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['aircraft_product_year'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_product_year'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_product_year'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['aircraft_std_capacity'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_std_capacity'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['aircraft_std_capacity'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/aircraft/edit/').($_smarty_tpl->tpl_vars['result']->value['aircraft_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/aircraft/delete/').($_smarty_tpl->tpl_vars['result']->value['aircraft_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>