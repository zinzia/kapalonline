<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 08:22:22
         compiled from "application/views\member/registration/confirmation.html" */ ?>
<?php /*%%SmartyHeaderCode:30165565bf92e5d2628-49559978%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9be090062cfababf38558f0c182d566f150798ff' => 
    array (
      0 => 'application/views\\member/registration/confirmation.html',
      1 => 1448868068,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30165565bf92e5d2628-49559978',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal").datepicker({
            yearRange: '-50:+0',
            changeMonth: true,
            changeYear: true,
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd'
        });
		
    });
	
</script>
<div class="breadcrum">
    <p>
        <a href="#">Register</a><span></span>
        <small>Confirmation User</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back to Login</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
    <div class="sub-content">
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration/confirmation_process');?>
" method="post" enctype="multipart/form-data">
            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="4">Konfirmasi Kode</th>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
						<?php if ($_smarty_tpl->getVariable('user_mail')->value!=''){?>
						<input name="user_mail" type="text" value="<?php echo $_smarty_tpl->getVariable('user_mail')->value;?>
" size="30" maxlength="50" />
						<?php }else{ ?>
						<input name="user_mail" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['user_mail'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="50" />
                        <?php }?>
						<em>* wajib diisi</em>
                    </td>
                </tr>
				<tr>
                    <td>Kode Konfirmasi </td>
                    <td>
						<?php if ($_smarty_tpl->getVariable('confirmation_kd')->value!=''){?>
						<input name="confirmation_kd" type="text" value="<?php echo $_smarty_tpl->getVariable('confirmation_kd')->value;?>
" size="20" maxlength="30" />
                        <?php }else{ ?>
						<input name="confirmation_kd" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['confirmation_kd'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="30" />
                        <?php }?>
						<em>* wajib diisi</em>
                    </td>
                </tr>
				<tr class="submit-box">
                    <td colspan="4">
						</br>
                        <input type="submit" name="save" value="Simpan" class="submit-button" />
                        <input type="reset" name="save" value="Reset" class="reset-button" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
