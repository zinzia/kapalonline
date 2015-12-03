<?php /* Smarty version Smarty-3.0.7, created on 2015-09-29 10:20:58
         compiled from "application/views\base/online/document.html" */ ?>
<?php /*%%SmartyHeaderCode:31276560a49ea2129f0-30363189%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '57c3b1210ce574ebd9dd3c1ea89e74f775ae767c' => 
    array (
      0 => 'application/views\\base/online/document.html',
      1 => 1441883443,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31276560a49ea2129f0-30363189',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <!-- head -->
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <meta name='description' content="<?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['meta_desc'])===null||$tmp==='' ? '' : $tmp);?>
" />
        <meta name='keywords' content="<?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['meta_keyword'])===null||$tmp==='' ? '' : $tmp);?>
" />
        <meta name='robots' content='index,follow' />
        <title><?php echo (($tmp = @$_smarty_tpl->getVariable('page')->value['nav_title'])===null||$tmp==='' ? 'Home' : $tmp);?>
 - <?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['site_title'])===null||$tmp==='' ? '' : $tmp);?>
</title>
        <link href="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/logo/logo.png" rel="SHORTCUT ICON" />
        <!-- themes style -->
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('THEMESPATH')->value;?>
" />
        <!-- other style -->
        <?php echo $_smarty_tpl->getVariable('LOAD_STYLE')->value;?>

    </head>
    <!-- body -->
    <body class="common">
        <!-- load javascript -->
        <?php echo $_smarty_tpl->getVariable('LOAD_JAVASCRIPT')->value;?>

        <!-- end of javascript	-->
        <!-- layout -->
        <div class="page">
            <div class="header">
                <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/logo/logo.png" alt="" />
                <div class="header-title">
                    <h1>KEMENTERIAN PERHUBUNGAN REPUBLIK INDONESIA</h1>
                    <h2>Direktorat Angkutan Udara</h2>
                </div>
                <div class="header-link">
                    <ul class="languages">
                        <?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_languages')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
?>
                        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(((('home/welcome/change_lang/').($_smarty_tpl->tpl_vars['lang']->value['lang_id'])).('/')).($_smarty_tpl->getVariable('nav_active')->value));?>
" <?php if ($_smarty_tpl->getVariable('languages')->value['lang_id']==$_smarty_tpl->tpl_vars['lang']->value['lang_id']){?>class="active"<?php }?>><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/lang/<?php echo $_smarty_tpl->tpl_vars['lang']->value['lang_icon'];?>
" alt="" /><?php echo $_smarty_tpl->tpl_vars['lang']->value['lang_name'];?>
</a></li>
                        <?php }} ?>
                        <li class="info">
                            <?php echo $_smarty_tpl->getVariable('tanggal')->value;?>

                        </li>
                    </ul>
                    <ul>
                        <?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_top_nav')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
?>
                        <li>
                            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url($_smarty_tpl->tpl_vars['menu']->value['nav_url']);?>
" <?php if ($_smarty_tpl->tpl_vars['menu']->value['nav_id']==$_smarty_tpl->getVariable('top_menu_selected')->value){?>class="active"<?php }?>><?php echo $_smarty_tpl->tpl_vars['menu']->value['lang_label'];?>
</a>
                        </li>
                        <?php }} ?>
                    </ul>
                </div>
            </div>
            <div class="main-content">
                <div class="breadcrum">                    
                    <p class="info" style="font-weight: normal; font-size: 11px; font-family: tahoma; float: left;">
                        <small>Pelayanan Izin Rute Penerbangan dan Persetujuan Terbang ( <em>Flight Approval</em> )</small><br />
                        <small><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/contact.png" alt="" style="height: 12px;" />151 / (021) 151</small>
                        <small><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/email.icon.png" alt="" style="height: 12px;" />info151@dephub.go.id</small>
                        <small><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/twitter.icon.png" alt="" style="height: 14px;" /><a href="https://twitter.com/kemenhub151" target="_blank">@kemenhub151</a></small>
                        <small><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/facebook.icon.png" alt="" style="height: 14px;" /><a href="https://www.facebook.com/pages/Kemenhub151/364857507021671" target="_blank">kemenhub151</a></small>
                    </p>
                    <p class="info">
                        <small><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/call_center.png" alt="" style="height: 42px;" /></small>
                    </p>
                    <div class="clear"></div>
                </div>
                <?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('template_content')->value), $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
            </div>
        </div>
        <div class="footer">
            <p>
                <?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_bottom_nav')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
?>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url($_smarty_tpl->tpl_vars['menu']->value['nav_url']);?>
" <?php if ($_smarty_tpl->tpl_vars['menu']->value['nav_id']==$_smarty_tpl->getVariable('top_menu_selected')->value){?>class="active"<?php }?>><?php echo $_smarty_tpl->tpl_vars['menu']->value['lang_label'];?>
</a>
                <?php }} ?>
            </p>
            <p style="color: #666; font-size: 11px; font-family: tahoma; font-weight: bold;">
                &copy; Copyright 2015 Direktorat Angkutan Udara - Direktorat Jenderal Perhubungan Udara
            </p>
        </div>
        <!-- end of layout -->
    </body>
    <!-- end body -->
</html>