<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 05:30:20
         compiled from "application/views\task/izin_rute/index.html" */ ?>
<?php /*%%SmartyHeaderCode:181495600cb4cbe8310-76309196%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac959daa8e58b894d58374355f65d49345837d85' => 
    array (
      0 => 'application/views\\task/izin_rute/index.html',
      1 => 1442892349,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '181495600cb4cbe8310-76309196',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_capitalize')) include 'C:\xampp\htdocs\development\angudonline\system\plugins\smarty\libs\plugins\modifier.capitalize.php';
?><div class="breadcrum">
    <p>
        <a href="#">Task Manager Izin Rute</a><span></span>
        <small><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('com_user')->value['role_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('com_user')->value['role_nm']));?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total ( <b style="color: red;"><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> ) Permohonan Yang Harus Diproses!</li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation">
        <div class="navigation-button">
            <ul>
                <li>
                    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('task/izin_rute/proses_cari');?>
" method="post">
                        <input type="text" name="airlines_nm" value="<?php echo $_smarty_tpl->getVariable('search')->value['airlines_nm'];?>
" placeholder="Semua Operator Penerbangan" size="30" maxlength="50" style="vertical-align: bottom; padding: 3px; text-align: center; border: 1px solid #C8E4F1; background-color: #D8F3FF; color: #666;" />
                        <input type="text" name="group_nm" value="<?php echo $_smarty_tpl->getVariable('search')->value['group_nm'];?>
" placeholder="Semua Jenis Permohonan" size="30" maxlength="50" style="vertical-align: bottom; padding: 3px; text-align: center; border: 1px solid #C8E4F1; background-color: #D8F3FF; color: #666;" />
                        <input type="submit" name="save" style="vertical-align: bottom; cursor: pointer; background-color: #FEA546; border: 1px solid #FF9421; padding: 2px 5px; vertical-align: middle; color: #fff;" value="Cari" />
                        <input type="submit" name="save" style="vertical-align: bottom; cursor: pointer; background-color: #FEA546; border: 1px solid #FF9421; padding: 2px 5px; vertical-align: middle; color: #fff;" value="Reset" />
                    </form>
                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<table class="table-view" width="100%">
    <tr>
        <th width='5%'>No</th>
        <th width='25%'>Operator</th>
        <th width='10%'>Rute Penerbangan</th>
        <th width='10%'>Domestik /<br /> Internasional</th>
        <th width='20%'>Surat<br/>Permohonan</th>
        <th width='15%'>Didaftarkan Oleh</th>
        <th width='15%'></th>
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
        <td>
            <?php echo $_smarty_tpl->tpl_vars['result']->value['airlines_nm'];?>

            <br />
            <b><?php echo $_smarty_tpl->tpl_vars['result']->value['group_nm'];?>
</b>
        </td>
        <td align="center"><b style="text-decoration: underline;"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
</b> <br /> <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
</b></td>
        <td align="center"><?php echo smarty_modifier_capitalize($_smarty_tpl->tpl_vars['result']->value['izin_flight']);?>
</td>
        <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['result']->value['group_alias']!='migrasi'){?>
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp);?>
</span><br />
            <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date']))===null||$tmp==='' ? '' : $tmp);?>

            <?php }else{ ?>
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp);?>
</span><br />
            Penerbitan :  <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_published_date']))===null||$tmp==='' ? '' : $tmp);?>

            <?php }?>
        </td>
        <td align="center">
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['pengirim'])===null||$tmp==='' ? '-' : $tmp);?>
</span><br />
            <?php echo (($tmp = @((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_date'],'ins'))))===null||$tmp==='' ? '' : $tmp);?>

        </td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @(((($_smarty_tpl->tpl_vars['result']->value['task_link']).('/')).($_smarty_tpl->tpl_vars['result']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button">
                <?php if ($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2)==00){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],3,2);?>
 menit yang lalu
                <?php }elseif($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2);?>
 jam yang lalu
                <?php }else{ ?>
                <?php echo $_smarty_tpl->tpl_vars['result']->value['selisih_hari'];?>
 hari yang lalu
                <?php }?>
            </a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Belum ada permohonan izin rute penerbangan yang di ajukan</td>
    </tr>
    <?php } ?>
</table>