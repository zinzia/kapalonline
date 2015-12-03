<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:21:27
         compiled from "application/views\member/registration_domestik/disclaimer.html" */ ?>
<?php /*%%SmartyHeaderCode:18035600e557edfcc0-26841852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4269742378c943d9196d0c2e7182674cf02c415d' => 
    array (
      0 => 'application/views\\member/registration_domestik/disclaimer.html',
      1 => 1441883436,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18035600e557edfcc0-26841852',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran</a><span></span>
        <a href="#">Rute Dalam Negeri</a><span></span>
        <small><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('group')->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('group')->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('group')->value['group_nm']));?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_domestik/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="dashboard-welcome">
    <div class="dashboard-profile">
        <div class="dashboard-profile-disclaimer">
            <h4>SYARAT DAN KETENTUAN PERMOHONAN <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('group')->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('group')->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('group')->value['group_nm']));?>
 <?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('group')->value['data_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('group')->value['data_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('group')->value['data_flight']));?>
</h4>
            <div class="list">
                <div>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <p>
                        <a><?php echo ((mb_detect_encoding((($tmp = @$_smarty_tpl->tpl_vars['data']->value['disclaimer_content'])===null||$tmp==='' ? '' : $tmp), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper((($tmp = @$_smarty_tpl->tpl_vars['data']->value['disclaimer_content'])===null||$tmp==='' ? '' : $tmp),SMARTY_RESOURCE_CHAR_SET) : strtoupper((($tmp = @$_smarty_tpl->tpl_vars['data']->value['disclaimer_content'])===null||$tmp==='' ? '' : $tmp)));?>
</a>
                    </p>
                    <?php }} ?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_domestik/disclaimer_process');?>
" method="post">
            <input type="hidden" name="group_id" value="<?php echo $_smarty_tpl->getVariable('group')->value['group_id'];?>
" />
            <table class="table-input" width="100%">
                <tr>
                    <td>
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Masukkan kode diatas</b>
                        <br>
                        <input type="text" name="captcha" size="20" maxlength="10" style="background-color:#CDD0D9" />
                    </td>
                </tr>
                <tr>
                    <td><h4><label style="width: auto;"><input type="checkbox" name="agree" /> Saya setuju dengan persyaratan yang ada pada sistem pelayanan permohonan izin rute penerbangan ini!</label></h4></td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="save" value="Lanjutkan" class="submit-button" />
                    </td>
                </tr>
            </table>
        </form>
        <div class="clear"></div>
    </div>
</div>
