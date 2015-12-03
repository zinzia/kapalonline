<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 05:15:34
         compiled from "application/views\pengaturan/member/list.html" */ ?>
<?php /*%%SmartyHeaderCode:25078565d1ee6406430-14238514%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3fbfe2ff4d9aeb76c5c45e6478fceb3806983fc4' => 
    array (
      0 => 'application/views\\pengaturan/member/list.html',
      1 => 1448943333,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25078565d1ee6406430-14238514',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <small>Member</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/member/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Nama</th>
                <td>
                    <input name="operator_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="40" placeholder="-- semua --" />
                </td>
                <td width='60%' rowspan="2" align="right">
                    <input name="save" type="submit" value="Tampilkan" />
                    <input name="save" type="submit" value="Reset" />
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
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width="4%">No</th>
        <th width="33%">Nama Member</th>
        <th width="15%">Username</th>
        <th width="20%">E-mail</th>
        <th width="15%">Phone Number</th>
        <th width="13%"></th>
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
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['user_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['user_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['user_name']));?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['user_mail'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['operator_phone'];?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/member/edit/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/member/delete/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>