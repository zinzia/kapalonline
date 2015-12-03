<?php /* Smarty version Smarty-3.0.7, created on 2015-12-01 04:40:13
         compiled from "application/views\pengaturan/operator/list.html" */ ?>
<?php /*%%SmartyHeaderCode:27674565d169db82515-52983424%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2436443ed1460503509abc857dc4487c84dcfba4' => 
    array (
      0 => 'application/views\\pengaturan/operator/list.html',
      1 => 1448941202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27674565d169db82515-52983424',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        /*
         * COMBO BOX
         */
        $(".pelabuhan").select2({
            placeholder: "Pilih Pelabuhan",
            allowClear: true,
            width: 300
        });
		
    });
</script>
<style type="text/css">
    .select2-choice {
        width: 280px !important;
    }
    .select2-default {
        width: 280px !important;
    }
</style>

<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <small>Operator</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Nama</th>
                <td width="15%">
                    <input name="operator_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="40" placeholder="-- semua --" />
                </td>
                <th width="10%">Pelabuhan</th>
                <td width="40%">
                    <select name="pelabuhan_id" class="pelabuhan">
                        <option value="">-- semua --</option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_pelabuhan_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'];?>
" <?php if (($_smarty_tpl->tpl_vars['data']->value['pelabuhan_id'])==(($tmp = @$_smarty_tpl->getVariable('search')->value['pelabuhan_id'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_nm'];?>
 - <?php echo $_smarty_tpl->tpl_vars['data']->value['pelabuhan_kd'];?>
</option>
						<?php }} ?>
                    </select>       
                </td>
                <td width='25%' align='right'>
                    <input name="save" type="submit" value="Tampilkan" />
                    <input name="save" type="submit" value="Reset" />
                </td>
            </tr>
        </table>
    </form>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total&nbsp;<strong><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['total'])===null||$tmp==='' ? 0 : $tmp);?>
</strong>&nbsp;Record&nbsp;</li><?php echo (($tmp = @$_smarty_tpl->getVariable('pagination')->value['data'])===null||$tmp==='' ? '' : $tmp);?>

            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/operator/add');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/add-icon.png" alt="" /> Add Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width="4%">No</th>
        <th width="20%">Name</th>
        <th width="22%">Pelabuhan</th>
        <th width="15%">Jabatan</th>
        <th width="12%">E-mail</th>
        <th width="12%">Phone Number</th>
        <th width="15%"></th>
    </tr>
    <?php $_smarty_tpl->tpl_vars['no'] = new Smarty_variable(1, null, null);?>
    <?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_id')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
?>
    <tr <?php if (($_smarty_tpl->getVariable('no')->value%2)!=1){?>class="blink-row"<?php }?>>
        <td align="center"><?php echo $_smarty_tpl->getVariable('no')->value++;?>
.</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['pelabuhan_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['pelabuhan_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['pelabuhan_nm']));?>
 - <?php echo $_smarty_tpl->tpl_vars['result']->value['pelabuhan_kd'];?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['jabatan'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['jabatan'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['jabatan']));?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['user_mail'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['operator_phone'];?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/operator/edit/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/operator/delete/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>