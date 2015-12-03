<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 06:42:53
         compiled from "application/views\member/registration/reset_success.html" */ ?>
<?php /*%%SmartyHeaderCode:28071565d335d546676-60216033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ec72fb6d7e409e55dc0fba68afd0325d19ae7187' => 
    array (
      0 => 'application/views\\member/registration/reset_success.html',
      1 => 1448947450,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28071565d335d546676-60216033',
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
        <a href="#">Settings</a><span></span>
        <small>Reset Success</small>
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
				</br>Perubahan Password Anda Sudah Berhasil, Silahkan cek email untuk melihat password baru anda. 
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
