<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 07:00:43
         compiled from "application/views\pengaturan/airlines/edit.html" */ ?>
<?php /*%%SmartyHeaderCode:1896456554e8b2076f7-44942724%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '70de62db489ceab7135656421d5f309661913cb9' => 
    array (
      0 => 'application/views\\pengaturan/airlines/edit.html',
      1 => 1441883434,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1896456554e8b2076f7-44942724',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".airlines_type").select2({
            placeholder: "--- Pilih ---",
            width: 110,
            allowClear: true
        });
        $(".airlines_st").select2({
            placeholder: "--- Pilih ---",
            width: 150,
            allowClear: true
        });
        $(".airlines_flight_type").select2({
            placeholder: "--- Pilih ---",
            width: 160,
            allowClear: true
        });
        $(".airlines_nationality").select2({
            placeholder: "--- Pilih ---",
            width: 110,
            allowClear: true
        });
    });
</script>
<style type="text/css">
    .airlines_type .select2-choice {
        width: 100px !important;
    }
    .airlines_type .select2-default {
        width: 100px !important;
    }
    .airlines_st .select2-choice {
        width: 140px !important;
    }
    .airlines_st .select2-default {
        width: 140px !important;
    }
    .airlines_flight_type .select2-choice {
        width: 150px !important;
    }
    .airlines_flight_type .select2-default {
        width: 150px !important;
    }
    .airlines_nationality .select2-choice {
        width: 100px !important;
    }
    .airlines_nationality .select2-default {
        width: 100px !important;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Settings</a><span></span>
        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/airlines');?>
">Operator Penerbangan</a><span></span>
        <small>Edit Data</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/airlines');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" /> Back</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('pengaturan/airlines/edit_process');?>
" method="post">
    <input name="airlines_id" type="hidden" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_id'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" maxlength="20" />
    <table class="table-input" width="100%">
        <tr class="headrow">
            <th colspan="4">Edit Data Operator Penerbangan</th>
        </tr>
        <tr>
            <td width="15%">Nama Operator</td>
            <td width="35%">
                <input name="airlines_nm" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_nm'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
            <td width="15%">Nama Brand</td>
            <td width="35%">
                <input name="airlines_brand" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_brand'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="50" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>IATA Code</td>
            <td>
                <input name="airlines_iata_cd" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_iata_cd'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" />
                <em>* wajib diisi</em>
            </td>
            <td>ICAO Code</td>
            <td>
                <input name="airlines_icao_cd" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_icao_cd'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" maxlength="10" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td width="15%">Nomor Kontak</td>
            <td width="35%">
                <input name="airlines_contact" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_contact'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" />
            </td>
            <td width="15%">Website</td>
            <td width="35%">
                <input name="airlines_website" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_website'])===null||$tmp==='' ? '' : $tmp);?>
" size="45" maxlength="50" />
            </td>
        </tr>
        <tr>
            <td>Tipe</td>
            <td>
                <select name="airlines_type" class="airlines_type">
                    <option value="">--- Pilih ---</option>
                    <option value="kargo" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_type'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=="kargo"){?>selected="selected"<?php }?>>Kargo</option>
                    <option value="penumpang" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_type'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2=="penumpang"){?>selected="selected"<?php }?>>Penumpang</option>       
                </select>
                <em>* wajib diisi</em>
            </td>

            <td>Status Pengoperasian</td>
            <td>
                <select name="airlines_st" class="airlines_st">
                    <option value="">--- Pilih ---</option>
                    <option value="beroperasi" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp3=ob_get_clean();?><?php if ($_tmp3=="beroperasi"){?>selected="selected"<?php }?>>Beroperasi</option>
                    <option value="tidak_beroperasi" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_st'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp4=ob_get_clean();?><?php if ($_tmp4=="tidak_beroperasi"){?>selected="selected"<?php }?>>Tidak Beroperasi</option>
                </select>
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Penerbangan</td>
            <td>
                <select name="airlines_flight_type" class="airlines_flight_type">
                    <option value="">--- Pilih ---</option>
                    <option value="domestik" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_flight_type'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp5=ob_get_clean();?><?php if ($_tmp5=="domestik"){?>selected="selected"<?php }?>>Domestik</option>
                    <option value="domestik_internasional" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_flight_type'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp6=ob_get_clean();?><?php if ($_tmp6=="domestik_internasional"){?>selected="selected"<?php }?>>Domestik Internasional</option>
                    <option value="internasional" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_flight_type'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp7=ob_get_clean();?><?php if ($_tmp7=="internasional"){?>selected="selected"<?php }?>>Internasional</option>
                </select>
                <em>* wajib diisi</em>
            </td>
            <td>Milik Perusahaan</td>
            <td>
                <select name="airlines_nationality" class="airlines_nationality">
                    <option value="">--- Pilih ---</option>
                    <option value="asing" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_nationality'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp8=ob_get_clean();?><?php if ($_tmp8=="asing"){?>selected="selected"<?php }?>>Asing</option>
                    <option value="nasional" <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_nationality'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_tmp9=ob_get_clean();?><?php if ($_tmp9=="nasional"){?>selected="selected"<?php }?>>Nasional</option>
                </select>
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td>Jenis SIUP</td>
            <td>
                <input type="checkbox" name="airlines_siup[1]" value="berjadwal" <?php if ($_smarty_tpl->getVariable('airlines_siup')->value[1]=="berjadwal"){?>checked<?php }?>> Berjadwal
                <input type="checkbox" name="airlines_siup[2]" value="tidak berjadwal" <?php if ($_smarty_tpl->getVariable('airlines_siup')->value[2]=="tidak berjadwal"){?>checked<?php }?>> Tidak Berjadwal
                <input type="checkbox" name="airlines_siup[3]" value="bukan niaga" <?php if ($_smarty_tpl->getVariable('airlines_siup')->value[3]=="bukan niaga"){?>checked<?php }?>> Bukan Niaga
                <br>
                <em>* wajib diisi</em>
            </td>
            <td>Alamat</td>
            <td>
                <textarea name="airlines_address"cols="60" rows="1"><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['airlines_address'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
            </td>  
        </tr>
        <tr class="submit-box">
            <td colspan="4">
                <input type="submit" name="save[insert]" value="Simpan" class="submit-button" />
                <input type="reset" name="save[reset]" value="Reset" class="reset-button" />
            </td>
        </tr>
    </table>
</form>