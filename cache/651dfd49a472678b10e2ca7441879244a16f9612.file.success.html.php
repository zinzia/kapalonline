<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 08:36:06
         compiled from "application/views\member/registration/success.html" */ ?>
<?php /*%%SmartyHeaderCode:29291565bfc6668edd4-47232420%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '651dfd49a472678b10e2ca7441879244a16f9612' => 
    array (
      0 => 'application/views\\member/registration/success.html',
      1 => 1448867280,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29291565bfc6668edd4-47232420',
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
        <small>Confirmation Success</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li> </li>
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
            <table class="table-input" width="100%">
                <tr class="headrow">
					<th>Selamat</th>
				</tr>
				<tr><td>
				</br>Registrasi Account Anda Sudah Berhasil, Silahkan klik tombol dibawah untuk kembali ke halaman login. 
					</br>
				</td>
				</tr>
            </table>
    </div>
</div>

<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li align="left"><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('home/welcome');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back to Login</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
