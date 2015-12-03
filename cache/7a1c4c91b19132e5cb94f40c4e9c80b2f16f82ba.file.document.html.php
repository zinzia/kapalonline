<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:59:03
         compiled from "application/views\base/stakeholder/document.html" */ ?>
<?php /*%%SmartyHeaderCode:2475056569197462ac5-64948451%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7a1c4c91b19132e5cb94f40c4e9c80b2f16f82ba' => 
    array (
      0 => 'application/views\\base/stakeholder/document.html',
      1 => 1448507703,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2475056569197462ac5-64948451',
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
/resource/doc/images/logo/logo.png" rel="SHORTCUT ICON" />
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
        <script type="text/javascript">
            $(document).ready(function() {
                // dropdown menu
                $(".parent").click(function() {
                    $(this).toggleClass('down');
                    $(this).siblings().slideToggle(100);
                    return false;
                });
            });
        </script>
        <!-- layout -->
        <div class="page">
            <div class="header">
                <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/logo/logo.png" alt="" />
                <div class="header-title">
                    <h1>DIREKTORAT PERKAPALAN DAN KEPELAUTAN</h1>
                    <h2>Pendaftaran Kapal Online</h2>
                </div>
                <div class="header-link">
                    <ul class="languages">
                        <li class="info"></li>
                    </ul>
                    <ul>
                        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/logout_process');?>
" class="logout">Logout</a></li>
                        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/account_settings/user_account');?>
">Change Account</a></li>
                        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/account_settings/change_role');?>
"><?php echo $_smarty_tpl->getVariable('com_user')->value['role_nm'];?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('stakeholder/account_settings/data_pribadi');?>
"><b><?php echo $_smarty_tpl->getVariable('com_user')->value['operator_name'];?>
</b></a></li>
                    </ul>
                </div>
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
            <br />
        </div>
        <!-- end of layout	-->
    </body>
    <!-- end body -->
</html>