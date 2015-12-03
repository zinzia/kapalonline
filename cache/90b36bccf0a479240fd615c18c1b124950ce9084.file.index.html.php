<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:08:21
         compiled from "application/views\member/registration_internasional/index.html" */ ?>
<?php /*%%SmartyHeaderCode:213385600d435e49549-04597444%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '90b36bccf0a479240fd615c18c1b124950ce9084' => 
    array (
      0 => 'application/views\\member/registration_internasional/index.html',
      1 => 1441883439,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '213385600d435e49549-04597444',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // dropdown menu
        $(".group").click(function() {
            $(this).toggleClass('down');
            $('.group-box').toggleClass('down');
            $('.group-box').slideToggle(100);
            return false;
        });
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran</a><span></span>
        <small>Rute Luar Negeri</small>
    </p>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<div class="content-dashboard">
    <h4>
        <a href="#" class="group">
            Pilihan Permohonan Izin Rute Penerbangan
        </a>
    </h4>
    <div class="clear"></div>
    <div style="display: none;" class="group-box">
        <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_group')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
        <div class="map-box">
            <p>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/registration_internasional/disclaimer/').($_smarty_tpl->tpl_vars['data']->value['group_id']));?>
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
 Permohonan Izin Rute Penerbangan Internasional</u></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr>
        <th width='5%'>No</th>
        <th width='10%'>Jenis<br />Penerbangan</th>
        <th width='30%'>Jenis Permohonan</th>
        <th width='20%'>Diinput Oleh</th>
        <th width='15%'>Tanggal</th>
        <th width='20%'></th>
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
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight']));?>
</td>
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
 / <?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
<br>(<?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm']));?>
)</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['operator_name']));?>
</td>
        <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['mdd'],'ins'), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['mdd'],'ins'),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['mdd'],'ins')));?>
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
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('member/registration_internasional/delete_process/').($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" onclick="return confirm('Apakah anda yakin akan menghapus data ini?')" class="button-hapus">
                Delete
            </a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Data not found!</td>
    </tr>
    <?php } ?>
</table>