<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 07:43:41
         compiled from "application/views\member/monitoring_izin/index.html" */ ?>
<?php /*%%SmartyHeaderCode:112685600ea8d1d8a18-10376773%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0936dd6b61866fb193e3ed36f94a824adb36f906' => 
    array (
      0 => 'application/views\\member/monitoring_izin/index.html',
      1 => 1441883437,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '112685600ea8d1d8a18-10376773',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="breadcrum">
    <p>
        <a href="#">Monitoring Permohonan</a><span></span>
        <small>Izin Rute Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="search-box">
    <h3><a href="#">Search</a></h3>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/monitoring_izin/proses_cari');?>
" method="post">
        <table class="table-search" width="100%" border="0">
            <tr>
                <th width="10%">Nomor Surat Permohonan</th>
                <td width="16%">
                    <input name="izin_request_letter" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['izin_request_letter'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="50" placeholder="-- semua --" style="text-align: center;" />
                </td>
                <th width="10%">Jenis Penerbangan</th>
                <td width="17%">
                    <select name="izin_flight" class="izin_flight">
                        <option value="">-- semua --</option>
                        <option value="domestik" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_flight'])===null||$tmp==='' ? '' : $tmp)=='domestik'){?>selected="selected"<?php }?>>DOMESTIK</option>
                        <option value="internasional" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_flight'])===null||$tmp==='' ? '' : $tmp)=='internasional'){?>selected="selected"<?php }?>>INTERNASIONAL</option>
                    </select>  
                </td>
                <th width="10%">Jenis Permohonan</th>
                <td width="17%">
                    <select name="izin_st">
                        <option value="">-- semua --</option>
                        <option value="baru" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='baru'){?>selected="selected"<?php }?>>BARU</option>
                        <option value="perpanjangan" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='perpanjangan'){?>selected="selected"<?php }?>>PERPANJANGAN</option>
                        <option value="pencabutan" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='pencabutan'){?>selected="selected"<?php }?>>PENCABUTAN</option>
                        <option value="penundaan" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='penundaan'){?>selected="selected"<?php }?>>PENUNDAAN</option>
                        <option value="perubahan" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='perubahan'){?>selected="selected"<?php }?>>PERUBAHAN</option>
                        <option value="notam" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='notam'){?>selected="selected"<?php }?>>NOTAM</option>
                        <option value="migrasi" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['izin_st'])===null||$tmp==='' ? '' : $tmp)=='migrasi'){?>selected="selected"<?php }?>>MIGRASI</option>
                    </select>  
                </td>
                <td width='20%' align='right'>
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
                <li class="info">Total ( <b style="color: red;"><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> ) Permohonan Yang Sedang Diproses!</li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<table class="table-view" width="100%">
    <tr>
        <th width='5%'>No</th>
        <th width='10%'>Domestik / Internasional</th>
        <th width='25%'>Jenis Permohonan</th>
        <th width='10%'>Rute Penerbangan</th>
        <th width='18%'>Surat<br/>Permohonan</th>
        <th width='18%'>Didaftarkan Oleh</th>
        <th width='14%'></th>
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
        <td><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['group_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['group_nm']));?>
</td>
        <td align="center"><b style="text-decoration: underline;"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_start']));?>
</b> <br /> <b><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_rute_end']));?>
</b></td>
        <td align="center">
			<?php if ($_smarty_tpl->tpl_vars['result']->value['group_alias']!='migrasi'){?>
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['izin_request_letter'])===null||$tmp==='' ? 'Surat permohonan tidak diisikan' : $tmp);?>
</span><br />
            <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_letter_date']))===null||$tmp==='' ? '' : $tmp);?>

			<?php }?>
        </td>
        <td align="center">
            <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['operator_name'])===null||$tmp==='' ? '-' : $tmp);?>
</span><br />
            <?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_request_date']))===null||$tmp==='' ? '' : $tmp);?>

        </td>
        <td align="center">
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @((('member/monitoring_izin/').($_smarty_tpl->tpl_vars['result']->value['group_alias'])).('/')).($_smarty_tpl->tpl_vars['result']->value['registrasi_id']))===null||$tmp==='' ? '' : $tmp));?>
" class="button">
                <?php if ($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0&&substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2)==00){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],3,2);?>
 Menit yang lalu
                <?php }elseif($_smarty_tpl->tpl_vars['result']->value['selisih_hari']==0){?>
                <?php echo substr($_smarty_tpl->tpl_vars['result']->value['selisih_waktu'],0,2);?>
 Jam yang lalu
                <?php }else{ ?>
                <?php echo $_smarty_tpl->tpl_vars['result']->value['selisih_hari'];?>
 Hari yang lalu
                <?php }?>
            </a>
        </td>
    </tr>
    <?php }} else { ?>
    <tr>
        <td colspan="7">Belum ada permohonan Izin Rute Yang Diajukan!</td>
    </tr>
    <?php } ?>
</table>