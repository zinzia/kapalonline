<?php /* Smarty version Smarty-3.0.7, created on 2015-11-30 08:25:36
         compiled from "application/views\member/registration/email.html" */ ?>
<?php /*%%SmartyHeaderCode:1034565bf9f064f727-24763619%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e78a370345d69206f92215784fdd5dbfb9b1935' => 
    array (
      0 => 'application/views\\member/registration/email.html',
      1 => 1448867931,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1034565bf9f064f727-24763619',
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
        <small>Register User</small>
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
					<th>Terima Kasih</th>
				</tr>
				<tr><td>
				</br>Terimakasih telah melakukan registrasi akun pada SIM Pendaftaran Kapal, 
				Silahkan cek email anda dan masukkan kode konfirmasi ke dalam halaman konfirmasi sebelum 12 jam dari sekarang . 
					</br></br>
				</td>
				</tr>
            </table>
    </div>
</div>

<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li align="left"><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration/confirmation');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Halaman Konfirmasi</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
