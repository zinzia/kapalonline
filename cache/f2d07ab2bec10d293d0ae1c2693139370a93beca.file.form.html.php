<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 05:51:41
         compiled from "application/views\login/stakeholder/form.html" */ ?>
<?php /*%%SmartyHeaderCode:4856568fdd8fdb69-85432285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2d07ab2bec10d293d0ae1c2693139370a93beca' => 
    array (
      0 => 'application/views\\login/stakeholder/form.html',
      1 => 1441883440,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4856568fdd8fdb69-85432285',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(function () {
        $(".login").jCryption({
            getKeysURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/crypt?getPublicKey=true');?>
",
            handshakeURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/crypt?handshake=true');?>
"
        });
    });
</script>
<?php if ((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)==''){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div class="action">
            <input value="" name="save" type="submit"/>
        </div>
        <div class="clear"></div>            
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error'){?>
<div class="alert red"><b>Your account is not found!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div class="action">
            <input value="" name="save" type="submit"/>
        </div>
        <div class="clear"></div>            
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='locked'){?>
<div class="alert red"><b>Your account has been locked!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div class="action">
            <input value="" name="save" type="submit"/>
        </div>
        <div class="clear"></div>            
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='still'){?>
<div class="alert red"><b>You are still login!</b> <p>Please Try Again or contact your administrator</p></div>
<?php }else{ ?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/stakeholderlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div class="action">
            <input value="" name="save" type="submit"/>
        </div>
        <div class="clear"></div>            
    </form>
</div>
<?php }?>