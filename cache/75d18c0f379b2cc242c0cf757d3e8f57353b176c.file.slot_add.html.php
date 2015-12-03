<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:55:05
         compiled from "application/views\izin_internasional/perpanjangan/slot_add.html" */ ?>
<?php /*%%SmartyHeaderCode:2375600df29882c54-57334758%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '75d18c0f379b2cc242c0cf757d3e8f57353b176c' => 
    array (
      0 => 'application/views\\izin_internasional/perpanjangan/slot_add.html',
      1 => 1442894814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2375600df29882c54-57334758',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal").datepicker({
            showOn: 'both',
            changeMonth: true,
            changeYear: true,
            yearRange: '1940:2015',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
        });
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Pendaftaran Izin Rute</a><span></span>
        <small><?php echo $_smarty_tpl->getVariable('detail')->value['group_nm'];?>
</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="pageRow">
        <div class="pageNav">
            <ul>
                <li class="info">
                    Surat Permohonan : <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['izin_request_letter'])===null||$tmp==='' ? '-' : $tmp);?>
</span>, 
                    <br />
                    Tanggal : <span style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->getVariable('detail')->value['izin_request_letter_date']))===null||$tmp==='' ? '-' : $tmp);?>
</span>, ( For <?php echo $_smarty_tpl->getVariable('detail')->value['izin_season'];?>
 )
                    <br />
                    Rute : <b><?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_start'];?>
 / <?php echo $_smarty_tpl->getVariable('detail')->value['izin_rute_end'];?>
</b>
                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('izin_internasional/perpanjangan/list_slot/').($_smarty_tpl->getVariable('detail')->value['registrasi_id']));?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Slot List</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('izin_internasional/perpanjangan/add_slot_process');?>
" method="post" enctype="multipart/form-data">
    <input type="hidden" name="registrasi_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('detail')->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width="100%">
        <tr>
            <td width="20%">
                <span style="text-decoration: underline;">Subyek Surat</span><br /><i>Letter Subject</i>
            </td>
            <td width="80%">
                <input type="text" name="slot_subject" value="<?php echo $_smarty_tpl->getVariable('result')->value['slot_subject'];?>
" size="70" maxlength="100" />
            </td>
        </tr>    
        <tr>
            <td width="20%">
                <span style="text-decoration: underline;">Nomor Surat</span><br /><i>Letter Number</i>
            </td>
            <td width="80%">
                <input type="text" name="slot_number" value="<?php echo $_smarty_tpl->getVariable('result')->value['slot_number'];?>
" size="40" maxlength="50" />
            </td>
        </tr>   
        <tr>
            <td width="20%">
                <span style="text-decoration: underline;">Tanggal Surat</span><br /><i>Letter Date</i>
            </td>
            <td width="80%">
                <input type="text" name="slot_date" value="<?php echo $_smarty_tpl->getVariable('result')->value['slot_date'];?>
" size="10" maxlength="10" class="tanggal" readonly="readonly" style="text-align: center;" />
            </td>
        </tr>   
        <tr>
            <td width="20%">
                <span style="text-decoration: underline;">Perihal</span><br /><i>Letter Description</i>
            </td>
            <td width="80%">
                <input type="text" name="slot_desc" value="<?php echo $_smarty_tpl->getVariable('result')->value['slot_desc'];?>
" size="50" maxlength="50" />
            </td>
        </tr>   
        <tr>
            <td width="20%">
                <span style="text-decoration: underline;">Lampiran Berkas</span><br /><i>File Attachment</i>
            </td>
            <td width="80%">
                <input type="file" name="slot_file_name" />
                <small>* 1024KB, pdf|jpeg|jpg|docx|doc|xls|xlsx</small>
            </td>
        </tr>   
    </table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td>
                Penting! wajib diisi semua field diatas!
            </td>
            <td align='right'>
                <input type="submit" name="save" value="Simpan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>