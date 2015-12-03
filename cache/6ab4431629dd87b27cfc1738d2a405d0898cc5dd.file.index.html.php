<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 06:12:09
         compiled from "application/views\member/payment_izin/index.html" */ ?>
<?php /*%%SmartyHeaderCode:192385600d519677401-11204882%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ab4431629dd87b27cfc1738d2a405d0898cc5dd' => 
    array (
      0 => 'application/views\\member/payment_izin/index.html',
      1 => 1441883440,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '192385600d519677401-11204882',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!-- javascript -->
<script type="text/javascript">
    function toggle(source) {
        var checkboxes = document.getElementsByName("izin_id[]");
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    $(document).ready(function() {
        $('#create').click(function(){
            uploadProcess();
        });
        function uploadProcess(){
            $.ajax({
                type: "POST",
                url:"<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/create_invoices_process');?>
",
                cache:false,
                beforeSend: function(){
                    $.blockUI({ css: { 
                            border: 'none', 
                            padding: '15px',
                            '-webkit-border-radius': '10px', 
                            '-moz-border-radius': '10px', 
                            opacity: 1, 
                            color: '#fff' 
                        },
                        message: '<h2 id="delay"></h2>'
                    });
                    $("#delay").runningDot({
                        "speed": 500,
                        "maxDots": 3,
                        "message": 'Process Generate Invoice'
                    });
                },
                success:function(data){
                    $.unblockUI(); 
                    $("#delay").runningDot("Stop");
                }

            });
            return false;
        }
    });
</script>
<div class="breadcrum">
    <p>
        <a href="#">Penagihan dan Pembayaran</a><span></span>
        <small>Izin Rute Penerbangan</small>
    </p>
    <div class="clear"></div>
</div>
<div class="sub-nav-content">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin');?>
" class="active">CREATE INVOICES</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/pending');?>
">Waiting for Payment</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/failed');?>
">Payment Failed</a></li>
        <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/success');?>
">Payment Success</a></li>
    </ul>
</div>
<div class="clear"></div>
<div class="sub-content">
<div class="notification green">
    <p><strong>PERHATIAN :</strong></p>
    <span style="float: right;display: block;margin-right: 50px;"><img width="72px" src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/warning.png"/></span>
    <ol><li>1. Pilih daftar pengajuan yg akan dibuat invoice dari daftar pengajuan di bawah</li> 
        <li>2. Dimohon untuk memilih pengajuan yg batas pembayarannya berdekatan<br/> (Misal : <i>01 April 2015 Jam 10:20:30 dan 02 April 2015 Jam 17:30:12</i>) </li>
        <li>3. Tanggal expired kode billing akan dihitung berdasarkan tanggal batas bayar terdekat</li>
        <li>4. Bayarlah sesuai dengan nominal yg tertera pada invoice sebelum <b>kode billing expired </b></li>
        <li>5. Jika Kode billing sudah <b>expired</b>, maka pemohon <b>harus melakukan pembuatan invoice kembali</b></li>
    </ol>
    <div class="clear"></div>
</div>
    <!-- notification template -->
    <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
    <!-- end of notification template-->
    <div class="search-box">
        <h3><a href="#">Search</a></h3>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/proses_cari');?>
" method="post">
            <table class="table-search" width="100%" border="0">
                <tr>
                    <th width="15%">Published Number</th>
                    <td width="50%">
                        <input name="izin_published_letter" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('search')->value['izin_published_letter'])===null||$tmp==='' ? '' : $tmp);?>
" size="30" maxlength="30" />
                        <select name="data_flight">
                            <option value="domestik" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='domestik'){?>selected="selected"<?php }?>>DOMESTIK</option>
                            <option value="internasional" <?php if ((($tmp = @$_smarty_tpl->getVariable('search')->value['data_flight'])===null||$tmp==='' ? '' : $tmp)=='internasional'){?>selected="selected"<?php }?>>INTERNASIONAL</option>
                        </select>
                    </td>
                    <td width="35%" align="right">
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
                    <li class="info">Total Ijin Rute yang telah diterbitkan&nbsp;<strong style="text-decoration: underline;"><?php echo (($tmp = @$_smarty_tpl->getVariable('total')->value)===null||$tmp==='' ? 0 : $tmp);?>
 Ijin Rute</strong>&nbsp;&nbsp;</li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
        <?php if (!empty($_smarty_tpl->getVariable('total_draft',null,true,false)->value)){?>
        <div class="navigation-button">
            <ul>
                <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/open');?>
"><?php echo $_smarty_tpl->getVariable('total_draft')->value;?>
 Draft Invoices / Failed Process</a></li>
            </ul>
        </div>
        <?php }?>
        <div class="clear"></div>
    </div>
    <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/payment_izin/create_invoices_process');?>
" method="post">
        <table class="table-view" width="100%">
            <tr>
                <th width='5%'>No</th>
                <th width='20%'>Published Number</th>
                <th width='15%'>Berjadwal / <br />Tidak Berjadwal</th>
                <th width='15%'>Domestik / <br />Internasional</th>
                <th width='10%'>Jumlah<br />Tagihan</th>
                <th width='15%'>Tanggal Terbit</th>
                <th width='15%'>Batas Bayar</th>
                <th width='5%'>
                    <input type="checkbox" onclick="toggle(this)" />
                </th>
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
                <td align="center"><?php echo $_smarty_tpl->tpl_vars['result']->value['izin_published_letter'];?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_type'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_type'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_type']));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['result']->value['izin_flight'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['result']->value['izin_flight']));?>
</td>
                <td align="right"><?php echo number_format((($tmp = @$_smarty_tpl->tpl_vars['result']->value['total_invoice'])===null||$tmp==='' ? 0 : $tmp),0,",",".");?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_published_date']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_published_date']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['izin_published_date'])));?>
</td>
                <td align="center"><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['payment_due_date']), 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['payment_due_date']),SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['result']->value['payment_due_date'])));?>
</td>
                <td align="center">
                    <input type="checkbox" name="izin_id[]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['result']->value['registrasi_id'])===null||$tmp==='' ? '' : $tmp);?>
">
                </td>
            </tr>
            <?php }} else { ?>
            <tr>
                <td colspan="8">Data not found!</td>
            </tr>
            <?php } ?>
        </table>
        <table class="table-form" width='100%'>
            <tr class="submit-box">
                <td>
                    Pilihlah data Izin Rute yang telah diterbitkan diatas untuk dibuatkan penagihannya!
                </td>
                <td align='right'>
                    <?php if (!empty($_smarty_tpl->getVariable('total',null,true,false)->value)){?>
                    <input type="submit" name="save" value="Create Invoices" class="reset-button" id="create"/>
                    <?php }?>
                </td>
            </tr>
        </table>
    </form>
</div>

