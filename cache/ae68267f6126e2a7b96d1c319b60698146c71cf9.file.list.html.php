<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 04:13:36
         compiled from "application/views\web/regulation/list.html" */ ?>
<?php /*%%SmartyHeaderCode:11670565e61e0926738-38475904%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae68267f6126e2a7b96d1c319b60698146c71cf9' => 
    array (
      0 => 'application/views\\web/regulation/list.html',
      1 => 1441883448,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11670565e61e0926738-38475904',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Web Management</a><span></span>
        <small>Regulation</small>
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
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('web/regulation/add/');?>
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
        <th width="15%">Dasar Hukum</th>
        <th width="29%">Tentang</th>
        <th width="10%">File</th>
        <th width="10%">Diunduh</th>
        <th width="20%">Last Update</th>
        <th width="12%"></th>
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
        <td><b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['judul'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['judul'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['judul']));?>
</b></td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['deskripsi'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['deskripsi'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['deskripsi']));?>
</td>
        <td align='center'>
            <?php if ($_smarty_tpl->tpl_vars['result']->value['file_name']!=''){?>
            <a href="<?php ob_start();?><?php echo ('web/regulation/download/').($_smarty_tpl->tpl_vars['result']->value['data_id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getVariable('config')->value->site_url($_tmp1);?>
">download</a>
            <?php }else{ ?>
            <i>Belum diupload</i>
            <?php }?>
        </td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['download'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['mdd']);?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('web/regulation/edit/').($_smarty_tpl->tpl_vars['result']->value['data_id']));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('web/regulation/delete_process/').($_smarty_tpl->tpl_vars['result']->value['data_id']));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus data berikut ini?');">Hapus</a>
        </td>
    </tr>    
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>