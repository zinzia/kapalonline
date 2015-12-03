<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 08:49:27
         compiled from "application/views\login/operator/form.html" */ ?>
<?php /*%%SmartyHeaderCode:29870565bff87b22d51-49637172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '354a7c05912a2c14ecbbf6e6de53872e18de2fef' => 
    array (
      0 => 'application/views\\login/operator/form.html',
      1 => 1448869766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29870565bff87b22d51-49637172',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(function() {
        $(".login").jCryption({
            getKeysURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/crypt?getPublicKey=true');?>
",
            handshakeURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/crypt?handshake=true');?>
"
        });
    });
</script>
<?php if ((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)==''){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off" />
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off" />
        </div>
        <div style="float: left; margin: 0; padding: 0;">
            <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div class="action">
            <button></button>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error'){?>
<div class="alert red"><b>Your account is not found!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div style="float: left; margin: 0; padding: 0;">
            <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div class="action">
            <button></button>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='locked'){?>
<div class="alert red"><b>Your account has been locked!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div style="float: left; margin: 0; padding: 0;">
            <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div class="action">
            <button></button>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error_captcha'){?>
<div class="alert red"><b>Captcha doesn't match!</b> <p>Please Try Again</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div style="float: left; margin: 0; padding: 0;">
            <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div class="action">
            <button></button>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='still'){?>
<div class="alert red"><b>You are still login!</b> <p>Please Try Again or contact your administrator</p></div>
<?php }else{ ?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/login_process');?>
" method="post" autocomplete="off" class="login">
        <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
        <div class="input">
            <input name="username" maxlength="30" type="text" placeholder="Username" autocomplete="off"/>
            <br/>
            <input name="pass" maxlength="30" type="password" placeholder="Password" autocomplete="off"/>
        </div>
        <div style="float: left; margin: 0; padding: 0;">
            <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('login/operatorlogin/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div class="action">
            <button></button>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }?>