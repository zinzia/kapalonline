<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:43:13
         compiled from "application/views\base/admin/document.html" */ ?>
<?php /*%%SmartyHeaderCode:939856556691789858-78571294%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '613d93128b61b05c08ba0b5a98043d953025c6be' => 
    array (
      0 => 'application/views\\base/admin/document.html',
      1 => 1441883443,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '939856556691789858-78571294',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <!-- head -->
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <meta name='description' content="<?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['meta_desc'])===null||$tmp==='' ? '' : $tmp);?>
" />
        <meta name='keywords' content="<?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['meta_keyword'])===null||$tmp==='' ? '' : $tmp);?>
" />
        <meta name='robots' content='index,follow' />
        <title><?php echo (($tmp = @$_smarty_tpl->getVariable('page')->value['nav_title'])===null||$tmp==='' ? 'Home' : $tmp);?>
 - <?php echo (($tmp = @$_smarty_tpl->getVariable('site')->value['site_title'])===null||$tmp==='' ? '' : $tmp);?>
</title>
        <link href="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/favicon.jpg" rel="SHORTCUT ICON" />
        <!-- themes style -->
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('THEMESPATH')->value;?>
" media="screen" />
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
                <h1>CiSmart 3.0</h1>
                <h2>CodeIgniter Framework and Smarty Template Engine</h2>
            </div>
            <a name="lebaran"></a>
            <div class="navigation">
                <div class="info"></div>
                <ul>
                    <?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list_top_nav')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
?>
                    <li>
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url($_smarty_tpl->tpl_vars['menu']->value['nav_url']);?>
" <?php if ($_smarty_tpl->tpl_vars['menu']->value['nav_id']==$_smarty_tpl->getVariable('top_menu_selected')->value){?>class="active"<?php }?>>
                           <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/nav/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['menu']->value['nav_icon'])===null||$tmp==='' ? 'default.png' : $tmp);?>
" alt="" /><?php echo $_smarty_tpl->tpl_vars['menu']->value['nav_title'];?>
<br /><small><?php echo $_smarty_tpl->tpl_vars['menu']->value['nav_desc'];?>
</small>
                        </a>
                    </li>
                    <?php }} ?>
                </ul>
                <div class="clear"></div>
            </div>
            <div class="main-content">
                <div class="sidebar">
                    <?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('template_sidebar')->value), $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
                </div>
                <div class="content">
                    <?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('template_content')->value), $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <!-- end of layout	-->
    </body>
    <!-- end body -->
</html>