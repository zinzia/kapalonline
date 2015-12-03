<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:05:39
         compiled from "application/views\pengaturan/block/list.html" */ ?>
<?php /*%%SmartyHeaderCode:1076756554fb3adf7a4-70695816%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '467a6a49502701988939401c6e0d9c76ee57272c' => 
    array (
      0 => 'application/views\\pengaturan/block/list.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1076756554fb3adf7a4-70695816',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'C:\xampp\htdocs\kapalonline\system\plugins\smarty\libs\plugins\modifier.date_format.php';
?><div class="breadcrum">
    <p>        
        <a href="#">Settings</a><span></span>
        <small>Block Airlines</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/block/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Status</th>
                <td width="10%">
                    <select name="block_st">
                        <option value="1" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['block_st'])===null||$tmp==='' ? '' : $tmp)=="1"){?>selected="selected"<?php }?>>LOCKED</option>
                        <option value="0" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['block_st'])===null||$tmp==='' ? '' : $tmp)=="0"){?>selected="selected"<?php }?>>OPEN</option>
                    </select>
                </td>
                <td align='right' width="80%">
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
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/block/add');?>
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
        <th width="20%">Airlines</th>
        <th width="30%">Alasan Pemblokiran</th>
        <th width="15%">Tanggal<br />Pemblokiran</th>
        <th width="20%">Penanggungjawab</th>
        <th width="10%"></th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['airlines_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['airlines_nm'])))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['block_reason'])===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['block_date'],'%d %b %y'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['block_date'],'%d %b %y'),SMARTY_RESOURCE_CHAR_SET) : strtoupper(smarty_modifier_date_format($_smarty_tpl->tpl_vars['result']->value['block_date'],'%d %b %y')));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name']));?>
</td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['block_by']==$_smarty_tpl->getVariable('com_user')->value['user_id']){?>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/block/edit/').($_smarty_tpl->tpl_vars['result']->value['block_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Update</a>
            <?php }?>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>