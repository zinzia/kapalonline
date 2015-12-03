<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:46:00
         compiled from "application/views\settings/portal/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:2754656556738b34334-05305610%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71554677c68982927e2085337c759a726facec87' => 
    array (
      0 => 'application/views\\settings/portal/edit.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2754656556738b34334-05305610',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="head-content">
    <h3>Web Portal</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="" class="active">Edit Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminportal/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminportal');?>
">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminportal/process_update');?>
" method="post">
    <input type="hidden" name="portal_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['portal_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="2">Edit Web Portal</th>
        </tr>
        <tr>
            <td width="25%">Nama Portal *</td>
            <td width="75%"><input type="text" name="portal_nm" maxlength="30" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['portal_nm'])===null||$tmp==='' ? '' : $tmp);?>
" /> </td>
        </tr>
        <tr>
            <td>Judul Web *</td>
            <td><input type="text" name="site_title" maxlength="40" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['site_title'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Deskripsi Web *</td>
            <td><input type="text" name="site_desc" maxlength="70" size="100" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['site_desc'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Meta Description *</td>
            <td><input type="text" name="meta_desc" maxlength="70" size="100" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['meta_desc'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td>Meta Keyword *</td>
            <td><input type="text" name="meta_keyword" maxlength="70" size="100" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['meta_keyword'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="Simpan" class="edit-button" /> </td>
        </tr>
    </table>
</form>
