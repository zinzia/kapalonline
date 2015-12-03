<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 04:13:43
         compiled from "application/views\web/content/index.html" */ ?>
<?php /*%%SmartyHeaderCode:1284565e61e7d4e810-33395718%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5630bb42dea8ce8e88cf758ee059cc3478c43f20' => 
    array (
      0 => 'application/views\\web/content/index.html',
      1 => 1441883448,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1284565e61e7d4e810-33395718',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Web Management</a><span></span>
        <small>Web Content</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info"><strong><?php echo $_smarty_tpl->getVariable('total')->value;?>
 Records</strong></li>
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
        <th width="90%">Judul</th>
        <th width="6%"></th>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align='center'><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['content_title'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['content_title'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['content_title']));?>
</b></td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('web/content/web_content_lang/').($_smarty_tpl->tpl_vars['result']->value['data_id']));?>
" class="button-edit">Edit</a>
        </td>
    </tr>    
    <?php }} else { ?>
    <tr>
        <td colspan="3">Data not found!</td>
    </tr>
    <?php } ?>
</table>