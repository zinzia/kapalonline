<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 07:41:29
         compiled from "application/views\home/welcome/form.html" */ ?>
<?php /*%%SmartyHeaderCode:13558565bef99ceab15-15879655%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12fed2d9c2bdc0fbb29aa017420bf99c2adb1f40' => 
    array (
      0 => 'application/views\\home/welcome/form.html',
      1 => 1448865679,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13558565bef99ceab15-15879655',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(function() {
        $(".login").jCryption({
            getKeysURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/crypt?getPublicKey=true');?>
",
            handshakeURL: "<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/crypt?handshake=true');?>
"
        });
    });
</script>
<?php if ((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)==''){?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/login_process');?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div style="float: left; margin: 0; padding: 0;">
            <button></button>
        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration');?>
" class="register-link">Register New</a>
        </div>
		<div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error'){?>
<div class="alert red"><b>Your account is not found!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/login_process');?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div style="float: left; margin: 0; padding: 0;">
            <button></button>
        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="register-link">Register New</a>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='locked'){?>
<div class="alert red"><b>Your account has been locked!</b> <p>Please Try Again or contact your administrator</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/login_process');?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div style="float: left; margin: 0; padding: 0;">
            <button></button>
        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="register-link">Register New</a>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='error_captcha'){?>
<div class="alert red"><b>Captcha doesn't match!</b> <p>Please Try Again</p></div>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/login_process');?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div style="float: left; margin: 0; padding: 0;">
            <button></button>
        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="register-link">Register New</a>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }elseif((($tmp = @$_smarty_tpl->getVariable('login_st')->value)===null||$tmp==='' ? '' : $tmp)=='still'){?>
<div class="alert red"><b>You are still login!</b> <p>Please Try Again or contact your administrator</p></div>
<?php }else{ ?>
<div class="content">
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/login_process');?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="captcha-reload">Re-load Pages</a>
            <br />
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome/reset');?>
" class="reset-link">Reset Password</a>
        </div>
        <div class="clear"></div>
        <br/>
        <input type="text" name="captcha" size="10" maxlength="10" class="captcha" placeholder="Type Captcha" autocomplete="off" />
        <br/>
        <br/>
        <div style="float: left; margin: 0; padding: 0;">
            <button></button>
        </div>
        <div style="float: right; margin: 0; padding: 0;">
            <br />
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
" class="register-link">Register New</a>
        </div>
        <div class="clear"></div>
    </form>
</div>
<?php }?>