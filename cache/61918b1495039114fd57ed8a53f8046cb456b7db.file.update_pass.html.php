<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 04:48:04
         compiled from "application/views\pengaturan/operator/update_pass.html" */ ?>
<?php /*%%SmartyHeaderCode:20277565d18746c48f4-35196829%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61918b1495039114fd57ed8a53f8046cb456b7db' => 
    array (
      0 => 'application/views\\pengaturan/operator/update_pass.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20277565d18746c48f4-35196829',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator');?>
">Operator</a><span></span>
        <small>Test</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/update_pass_process');?>
" method="post" enctype="multipart/form-data">
    <table class="table-input" width="100%">
        <tr>
            <td>
                <input type="submit" name="save" value="update">
            </td>
        </tr>
    </table>
</form>
