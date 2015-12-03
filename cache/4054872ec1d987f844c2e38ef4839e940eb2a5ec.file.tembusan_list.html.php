<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:04:56
         compiled from "application/views\pengaturan/preferences/tembusan_list.html" */ ?>
<?php /*%%SmartyHeaderCode:2603556554f8868e619-70713828%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4054872ec1d987f844c2e38ef4839e940eb2a5ec' => 
    array (
      0 => 'application/views\\pengaturan/preferences/tembusan_list.html',
      1 => 1441883433,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2603556554f8868e619-70713828',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    function delete_data() {
        if (confirm("Apakah anda yakin akan menghapus data ini?")) {
            return true;
        } else {
            return false;
        }
    }
</script>
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
" class="active">Surat Izin Rute - Tembusan</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/kepada');?>
">Surat Izin Rute - Kepada</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/mail/');?>
">Email Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/invoices/');?>
">Invoices Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/tarif/');?>
">Tarif Settings</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/peraturan/');?>
">Undang - undang</a></li>
    </ul>
    <div class="clear"></div>
    <div class="sub-content">
        <div class="navigation">
            <div class="navigation-button">
                <ul>
                    <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/preferences/tembusan_add/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
        <!-- notification template -->
        <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
        <!-- end of notification template-->
        <table class="table-view" width="100%">
            <tr>
                <th width="5%">No</th>
                <th width="40%">Nama</th>
                <th width="40%">Email</th>
                <th width="15%"></th>
            </tr>
            <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
 $_smarty_tpl->tpl_vars['no']->value = $_smarty_tpl->tpl_vars['result']->key;
?>
            <tr <?php if (($_smarty_tpl->tpl_vars['no']->value%2)!=0){?>class="blink-row"<?php }?>>
                <td align='center'><?php echo $_smarty_tpl->tpl_vars['no']->value+1;?>
.</td>
                <td><?php echo $_smarty_tpl->tpl_vars['result']->value['redaksional_nm'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['result']->value['redaksional_mail'];?>
</td>
                <td align="center">
                    <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/preferences/tembusan_edit/').($_smarty_tpl->tpl_vars['result']->value['redaksional_id']));?>
" class="button-edit">Edit</a>
                    <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/preferences/tembusan_hapus_process/').($_smarty_tpl->tpl_vars['result']->value['redaksional_id']));?>
" class="button-hapus" onclick="return delete_data();">Hapus</a>
                </td>
            </tr>    
            <?php }} else { ?>
            <tr>
                <td colspan="3">Data tidak ditemukan!</td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>