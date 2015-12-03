<?php /* Smarty version Smarty-3.0.7, created on 2015-11-24 09:10:01
         compiled from "application/views\member/registration_domestik/index.html" */ ?>
<?php /*%%SmartyHeaderCode:2887756541b590adf62-79069368%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '60e88c3d58027f4526398a8ab62515ca9ee4b7a1' => 
    array (
      0 => 'application/views\\member/registration_domestik/index.html',
      1 => 1443841845,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2887756541b590adf62-79069368',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran</a><span></span>
        <small>Rute Dalam Negeri</small>
    </p>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="content-dashboard">
    <h4>
        <a href="#">
            Pilihan Permohonan Izin Rute Penerbangan
        </a>
    </h4>
    <div class="clear"></div>
    <div class="group-box">
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_group')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <div class="map-box">
            <p>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/registration_domestik/disclaimer/').($_smarty_tpl->tpl_vars['data']->value['group_id']));?>
">
                    <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/<?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['group_alias'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->tpl_vars['data']->value['group_alias'],SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->tpl_vars['data']->value['group_alias']));?>
.icon.png" alt="" />
                    <?php echo $_smarty_tpl->tpl_vars['data']->value['group_nm'];?>
<br />
                    <small><?php echo $_smarty_tpl->tpl_vars['data']->value['group_desc'];?>
</small>
                </a>
            </p>
        </div>
        <?php }} ?>
    </div>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">Total ( <b style="color: red;">Belum Dikirim</b> ) <u><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
 Permohonan Izin Rute Penerbangan Domestik</u></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr>
        <th width='5%'>No</th>
        <th width='29%'>Jenis Permohonan</th>
        <th width='10%'>Rute<br/>Penerbangan</th>
        <th width='20%'>Surat<br/>Permohonan</th>
        <th width='20%'>Didaftarkan Oleh</th>
        <th width='16%'></th>
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
        <td><b style="color: #999;"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm']));?>
</b></td>
        <td align="center"><b style="color: #999;"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
<br /><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
</b></td>
        <td align="center">
            <span style="text-decoration: underline;"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_request_letter'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_request_letter'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_request_letter']));?>
</span><br /><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date'],'ins')));?>

        </td>
        <td align="center">
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['operator_name'])===null||$tmp==='' ? '-' : $tmp);?>
</span><br />
            <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['mdd']))===null||$tmp==='' ? '' : $tmp);?>

        </td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @(($_smarty_tpl->tpl_vars['result']->value['group_link']).('/index/')).($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-edit">
                <?php if ($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2)==00){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],3,2);?>
 minutes
                <?php }elseif($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2);?>
 hour
                <?php }else{ ?>
                <?php echo $_smarty_tpl->tpl_vars['result']->value['selisih_hari'];?>
 day
                <?php }?>
            </a>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('member/registration_domestik/delete_process/').($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button-hapus" onclick="return confirm('Apakah anda yakin akan menghapus data ini?')">
                Delete
            </a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="6">Data not found!</td>
    </tr>
    <?php } ?>
</table>