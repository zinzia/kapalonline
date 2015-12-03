<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:05:35
         compiled from "application/views\pengaturan/services_code/svc.html" */ ?>
<?php /*%%SmartyHeaderCode:87856554faf4dd728-33422041%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11dd9a073c5db4d6f0ee2089e781c4a5fa006ff0' => 
    array (
      0 => 'application/views\\pengaturan/services_code/svc.html',
      1 => 1441883435,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '87856554faf4dd728-33422041',
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
        <small>Services Code</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/services_code/svc_add/');?>
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
        <th width="10%">Kode</th>
        <th width="70%">Nama</th>
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
        <td align='center'><?php echo $_smarty_tpl->tpl_vars['result']->value['services_cd'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['services_nm'];?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/services_code/svc_edit/').($_smarty_tpl->tpl_vars['result']->value['services_cd']));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('pengaturan/services_code/svc_hapus_process/').($_smarty_tpl->tpl_vars['result']->value['services_cd']));?>
" class="button-hapus" onclick="return delete_data();">Hapus</a>
        </td>
    </tr>    
    <?php }} else { ?>
    <tr>
        <td colspan="4">Data tidak ditemukan!</td>
    </tr>
    <?php } ?>
</table>