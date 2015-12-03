<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:05:26
         compiled from "application/views\pengaturan/stakeholder/list.html" */ ?>
<?php /*%%SmartyHeaderCode:3146556554fa6d0f0e0-91542270%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd97a391752bf741d26eb86eaa3f8839ab841d6df' => 
    array (
      0 => 'application/views\\pengaturan/stakeholder/list.html',
      1 => 1441883433,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3146556554fa6d0f0e0-91542270',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <small>Member Stakeholder</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Pencarian</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/stakeholder/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Nama</th>
                <td width="15%">
                    <input name="operator_name" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['operator_name'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="40" placeholder="-- semua --" />
                </td>
                <th width="10%">Status</th>
                <td width="15%">
                    <select name="member_status">
                        <option value="">-- semua --</option>
                        <option value="airnav" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['member_status'])===null||$tmp==='' ? '' : $tmp)=='airnav'){?>selected="selected"<?php }?>>AIRNAV</option>
                        <option value="bandara" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['member_status'])===null||$tmp==='' ? '' : $tmp)=='bandara'){?>selected="selected"<?php }?>>BANDARA</option>
                        <option value="otoritas" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['member_status'])===null||$tmp==='' ? '' : $tmp)=='otoritas'){?>selected="selected"<?php }?>>OTORITAS</option>
                    </select>
                </td>
                <th width="10%">Bandara</th>
                <td width="15%">
                    <select name="airport_id">
                        <option value="">-- semua --</option>
                        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_airport')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['airport_id'];?>
"  <?php if ($_smarty_tpl->tpl_vars['data']->value['airport_id']==(($tmp = @$_smarty_tpl->getVariable('search')->value['airport_id'])===null||$tmp==='' ? '' : $tmp)){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['airport_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['airport_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['airport_nm']));?>
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
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/stakeholder/add');?>
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
        <th width="23%">Nama Member</th>
        <th width="15%">Status Member</th>
        <th width="20%">E-mail</th>
        <th width="15%">Phone Number</th>
        <th width="10%">Jumlah<br />Airport</th>
        <th width="13%"></th>
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
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['member_status'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['member_status'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['member_status']));?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['result']->value['user_mail'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['operator_phone'];?>
</td>
        <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['total_airport'];?>
</td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/stakeholder/edit/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">Edit</a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('pengaturan/stakeholder/delete/').($_smarty_tpl->tpl_vars['result']->value['user_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus">Hapus</a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>