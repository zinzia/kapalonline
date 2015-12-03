<?php /* Smarty version Smarty-3.0.7, created on 2015-12-02 04:06:20
         compiled from "application/views\pengaturan/preferences/email.html" */ ?>
<?php /*%%SmartyHeaderCode:12153565e602c4d5a38-21058452%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ace729a3abc1e8001e64b45025b0536d4f50d045' => 
    array (
      0 => 'application/views\\pengaturan/preferences/email.html',
      1 => 1441883433,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12153565e602c4d5a38-21058452',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <small>Preferences</small>
    </p>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/');?>
">Surat Izin Rute - Tembusan</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/kepada');?>
">Surat Izin Rute - Kepada</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/mail/');?>
" class="active">Email Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/invoices/');?>
">Invoices Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/tarif/');?>
">Tarif Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/peraturan/');?>
">Undang - undang</a></li>
    </ul>
    <div class="clear"></div>
    <div class="sub-content">
        <!-- notification template -->
        <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
        <!-- end of notification template-->
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/mail_edit_process');?>
" method="post">
            <input type="hidden" name="pref_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['pref_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="2">Edit Data</th>
                </tr>
                <tr>
                    <td width='20%'>SMTP Address</td>
                    <td width='80%'>
                        <input type="text" name="address" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['address'])===null||$tmp==='' ? '' : $tmp);?>
" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td width='20%'>SMTP Port</td>
                    <td width='80%'>
                        <input type="text" name="port" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['port'])===null||$tmp==='' ? '' : $tmp);?>
" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td width='20%'>Mail User</td>
                    <td width='80%'>
                        <input type="text" name="user_name" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr>
                    <td width='20%'>Mail Password</td>
                    <td width='80%'>
                        <input type="text" name="user_pass" maxlength="50" size="50" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_pass'])===null||$tmp==='' ? '' : $tmp);?>
" /> 
                        <em>* wajib diisi</em>
                    </td>
                </tr>
                <tr class="submit-box">
                    <td colspan="2">
                        <input type="submit" name="save" value="Simpan" class="submit-button" />
                        <input type="reset" name="save" value="Reset" class="reset-button" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>