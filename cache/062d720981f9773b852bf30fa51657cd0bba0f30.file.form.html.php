<?php /* Smarty version Smarty-3.0.7, created on 2015-11-26 08:47:28
         compiled from "application/views\member/registration/form.html" */ ?>
<?php /*%%SmartyHeaderCode:265505656b910575fd5-91732417%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '062d720981f9773b852bf30fa51657cd0bba0f30' => 
    array (
      0 => 'application/views\\member/registration/form.html',
      1 => 1448524047,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '265505656b910575fd5-91732417',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
    $(document).ready(function() {
        // date picker
        $(".tanggal_from").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            minDate: <?php echo (($tmp = @$_smarty_tpl->getVariable('hari_pengajuan')->value['batasan'])===null||$tmp==='' ? '0' : $tmp);?>
,
            onClose: function( selectedDate ) {
                $( ".tanggal_to" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $(".tanggal_to").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            minDate: <?php echo (($tmp = @$_smarty_tpl->getVariable('hari_pengajuan')->value['batasan'])===null||$tmp==='' ? '0' : $tmp);?>
,
            onClose: function( selectedDate ) {
                $( ".tanggal_from" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        $(".tanggal_from_2").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            minDate: <?php echo (($tmp = @$_smarty_tpl->getVariable('hari_pengajuan')->value['batasan'])===null||$tmp==='' ? '0' : $tmp);?>
,
            onClose: function( selectedDate ) {
                $( ".tanggal_to_2" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $(".tanggal_to_2").datepicker({
            showOn: 'both',
            buttonImage: '<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
/resource/doc/images/icon/calendar.gif',
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            minDate: <?php echo (($tmp = @$_smarty_tpl->getVariable('hari_pengajuan')->value['batasan'])===null||$tmp==='' ? '0' : $tmp);?>
,
            onClose: function( selectedDate ) {
                $( ".tanggal_from_2" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        // timepicker
        $('.waktu').timepicker({
            showPeriodLabels: false
        });
        // --
        $(".rute_all").select2({
            placeholder: "-- Pilih Rute Pada Izin Rute Yang Dimiliki --",
            width: 470,
            allowClear: true
        });
        $(".airport_iata_cd").select2({
            placeholder: "-- Pilih Bandar Udara --",
            width: 250,
            allowClear: true
        });
        $(".services_cd").select2({
            placeholder: "-- Pilih Keterangan --",
            width: 290,
        });
    });
</script>
<script type="text/javascript">
    function prev() {
        window.location = "<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/registration_scheduled/add/').($_smarty_tpl->getVariable('result')->value['data_id']));?>
";
    }
</script>
<style type="text/css">
    .rute_all .select2-choice {
        width: 460px !important;
        font-weight: bold;
    }
    .rute_all .select2-default {
        width: 460px !important;
        font-weight: bold;
    }
    .airport_iata_cd .select2-choice {
        width: 240px !important;
    }
    .airport_iata_cd .select2-default {
        width: 240px !important;
    }
    .services_cd .select2-choice {
        width: 280px !important;
    }
    .services_cd .select2-default {
        width: 280px !important;
    }
</style>
<div class="breadcrum">
    <p>
        <a href="#">Registration</a><span></span>
        <small>Registration User</small>
    </p>
    <div class="clear"></div>
</div>
<div class="navigation">
    <div class="navigation-button">
        <ul>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_scheduled/');?>
"><img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/back-icon.png" alt="" />Back to Login</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="step-box">
    <ul>
        <li>
            <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url(('member/registration_scheduled/add/').($_smarty_tpl->getVariable('result')->value['data_id']));?>
" class="active"><b>Registration User</b><br />Pendaftaran Kapal</a>
        </li>
    </ul>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/registration_scheduled/form_process');?>
" method="post" onsubmit="return validation()">
    <input type="hidden" name="data_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['data_id'])===null||$tmp==='' ? '' : $tmp);?>
">
    <input type="hidden" name="data_flight" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['data_flight'])===null||$tmp==='' ? '' : $tmp);?>
">
    <table class="table-form" width='100%'>
        <tr>
            <td width='3%'>1.</td>
            <td width='97%' colspan="6"><span style="text-decoration: underline;">Pesawat Udara</span> <br /><i>Aircraft</i></td>
        </tr>
        <tr>
            <td></td>
            <td>b)</td>
            <td><span style="text-decoration: underline;">Tipe</span><br /><i>Type</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="aircraft_type" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['aircraft_type'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" placeholder="B737-800" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>c)</td>
            <td><span style="text-decoration: underline;">Tanda Pendaftaran dan Nama Panggilan</span><br /><i>Registrations and Call Signs</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="registration" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['registration'])===null||$tmp==='' ? '' : $tmp);?>
" size="35" maxlength="50" placeholder="PK-ABC" />
                <input name="call_sign" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['call_sign'])===null||$tmp==='' ? '' : $tmp);?>
" size="3" maxlength="3" style="text-align: center;" placeholder="XXX" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td width='3%'>a)</td>
            <td width='28%'><span style="text-decoration: underline;">Tanggal dan Jam</span><br /><i>Date and Time</i></td>
            <td width='1%'>:</td>
            <td width='65%' colspan="3">
                <input name="date_start" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['date_start'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" class="tanggal_from" id="date_start" placeholder="0000-00-00" />
                &nbsp;&nbsp;&nbsp;s/d&nbsp;&nbsp;
                <input name="date_end" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['date_end'])===null||$tmp==='' ? '' : $tmp);?>
" size="10" class="tanggal_to" id="date_end" placeholder="0000-00-00" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>b)</td>
            <td><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="rute_all" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['rute_all'])===null||$tmp==='' ? '' : $tmp);?>
" size="74" maxlength="100" onchange="split(this.value)" placeholder="JOG-CGK" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>c)</td>
            <td><span style="text-decoration: underline;">Pendaratan Teknis di</span><br /><i>Technical Landing at</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="technical_landing" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['technical_landing'])===null||$tmp==='' ? '' : $tmp);?>
" maxlength="100" placeholder="JOG,CGK" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>d)</td>
            <td><span style="text-decoration: underline;">Pendaratan Niaga di</span><br /><i>Commercial Landing at</i></td>
            <td>:</td>
            <td colspan="3">
        <input name="niaga_landing" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['niaga_landing'])===null||$tmp==='' ? '' : $tmp);?>
" maxlength="100" placeholder="JOG,CGK" />
            </td>
        </tr>
        <tr>
            <td width='3%'>3.</td>
            <td width='97%' colspan="6"><span style="text-decoration: underline;">Jumlah orang dalam Pesawat udara</span> <br /><i>Total number of person on board</i></td>
        </tr>
        <tr>
            <td></td>
            <td width='3%'>a)</td>
            <td width='28%'><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
            <td width='1%'>:</td>
            <td width='65%' colspan="3">
                <input name="flight_pilot" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['flight_pilot'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" placeholder="CAPT. JOHN DOE" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>b)</td>
            <td><span style="text-decoration: underline;">Awak pesawat udara lainnya</span> *1)<br /><i>Other crew members</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="flight_crew" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['flight_crew'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" placeholder="PLUS CREW" />
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>c)</td>
            <td><span style="text-decoration: underline;">Penumpang / Barang </span> *2)<br /><i>Passengers / Cargo</i></td>
            <td>:</td>
            <td colspan="3">
                <input name="flight_goods" type="text" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['flight_goods'])===null||$tmp==='' ? '' : $tmp);?>
" size="20" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['services_cd'])===null||$tmp==='' ? '' : $tmp)!='F'){?><?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['services_cd'])===null||$tmp==='' ? '' : $tmp)=='P'){?>disabled<?php }?><?php }else{ ?>disabled<?php }?> placeholder="PAX/CARGO MANIFEST" />
                <em>* wajib diisi selain Ferry Flight atau Positioning Flight</em>
            </td>
        </tr>
        <tr>
            <td width='3%'>4.</td>
            <td colspan="2"><span style="text-decoration: underline;">Keterangan</span> <br /><i>Remark</i></td>
            <td>:</td>
            <td colspan="3">
                <select name="services_cd_label" class="services_cd" disabled>
                    <option value=""></option>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_service_code')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['data']->value['services_cd'];?>
" <?php if ((($tmp = @$_smarty_tpl->getVariable('result')->value['services_cd'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['data']->value['services_cd']){?>selected="selected"<?php }?>><?php echo ((mb_detect_encoding($_smarty_tpl->tpl_vars['data']->value['services_nm'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->tpl_vars['data']->value['services_nm']));?>
</option>
                    <?php }} ?>
                </select>
                <input type="hidden" name="services_cd" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['services_cd'])===null||$tmp==='' ? '' : $tmp);?>
">
                <em>* wajib diisi</em>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="vertical-align:top;">
                <textarea name="catatan" rows="9" cols="50"><?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['catatan'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
            </td>
            <td colspan="3">
                
            </td>
        </tr>
    </table>
    <table class="table-form" width='100%'>
        <tr>
            <td colspan="2">
                <span style="text-decoration: underline;">Tes</i>
            </td>
        </tr>
        
		</table>
    <table class="table-form" width='100%'>
        <tr class="submit-box">
            <td colspan="2">
                <input name="save" value="Batalkan" class="submit-button" type="button" onclick="prev();" />
            </td>
            <td colspan="2" align='right'>
                <input type="submit" name="save" value="Simpan" class="submit-button" />
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    // jQuery('#rute_all').on('click', function(rute) {
    //     $('#niaga_landing').empty();
    //     var values = rute.currentTarget.value.split(",");
    //     $.each(values, function (index, value) {
    //         $('#niaga_landing').append($('<option/>', {
    //             value: value,
    //             text: value
    //         }));
    //     })
    // })
    // jQuery('#technical_landing').on('click', function(rute) {
    //     var values = rute.currentTarget.value.split(",");
    //     var rute_all = $('#rute_all').val().split(",");
    //     $.each(values, function (index, value) {
    //         var found = jQuery.inArray(value, rute_all);
    //         if (found != "-1") {
    //             alert("Pendaratan teknis tidak dapat dilakukan pada rute " + value);
    //         }
    //     })
    // })
</script>

<!-- validation -->
<script type="text/javascript">
    function validation() {
        var err = false;
        var rute_all = $('#rute_all').val().split(/[\s,-/]/);
        // validasi technical landing di luar rute utama
        var technical_landing = $('#technical_landing').val().split(/[\s,-/]/);
        $.each(technical_landing, function (index, value) {
            var found = jQuery.inArray(value, rute_all);
            if (found != "-1") {
                err = true;
                alert("Pendaratan teknis tidak dapat dilakukan pada rute " + value);
            }
        })
        if (err == true) {
            return false;
        }
        // validasi niaga landing di dalam rute utama
        var niaga_landing = $('#niaga_landing').val().split(/[\s,-/]/);
        if (niaga_landing != "") {
            $.each(niaga_landing, function (index, value) {
                var found = jQuery.inArray(value, rute_all);
                if (found == "-1") {
                    err = true;
                    alert("Pendaratan niaga tidak dapat dilakukan pada rute " + value);
                }
            })
            if (err == true) {
                return false;
            }
        }
        // validasi jumlah rute utamma di bawah batas 11
        var total_rute_all = 1;
        $.each(rute_all, function (index, value) {
            if (total_rute_all > 11) {
                err = true;
            }
            total_rute_all = total_rute_all + 1;
        })
        if (err == true) {
            alert('Jumlah rute melebihi batas maksimal <?php echo (($tmp = @$_smarty_tpl->getVariable('jml_rute')->value)===null||$tmp==='' ? "11" : $tmp);?>
 Rute');
            return false;
        }
    }

    function split(rute_all) {
        var rute = rute_all.split('-');
        var bandara_1 = document.getElementsByClassName("bandara_1");
        var bandara_2 = document.getElementsByClassName("bandara_2");
        for (var i = 0; i < bandara_1.length; i++) {
            bandara_1[i].value = rute[0];
        };
        for (var i = 0; i < bandara_2.length; i++) {
            bandara_2[i].value = rute[1];
        };
    }

    $(document).ready(function() {
        var rute_all = document.getElementById("rute_all").value;
        var rute = rute_all.split('-');
        var bandara_1 = document.getElementsByClassName("bandara_1");
        var bandara_2 = document.getElementsByClassName("bandara_2");
        for (var i = 0; i < bandara_1.length; i++) {
            if (rute[0]) {
            bandara_1[i].value = rute[0];
            }
        };
        for (var i = 0; i < bandara_2.length; i++) {
            if (rute[1]) {
            bandara_2[i].value = rute[1];
            }
        };
    });

    function time_converter_etd(temp) {
        var bandara = document.getElementById("etd").value;
        var sendData = {
            bandara: bandara,
            time: temp,
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/member/registration_scheduled/time_converter/",
            dataType: "json",
            data: sendData,
            success: function (results) {
                document.getElementById("etd_utc").value = results;
            },
        });
    }

    function time_converter_etd_2(temp) {
        var bandara = document.getElementById("etd_2").value;
        var sendData = {
            bandara: bandara,
            time: temp,
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/member/registration_scheduled/time_converter/",
            dataType: "json",
            data: sendData,
            success: function (results) {
                document.getElementById("etd_2_utc").value = results;
            },
        });
    }

    function time_converter_eta(temp) {
        var bandara = document.getElementById("eta").value;
        var sendData = {
            bandara: bandara,
            time: temp,
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/member/registration_scheduled/time_converter/",
            dataType: "json",
            data: sendData,
            success: function (results) {
                document.getElementById("eta_utc").value = results;
            },
        });
    }

    function time_converter_eta_2(temp) {
        var bandara = document.getElementById("eta_2").value;
        var sendData = {
            bandara: bandara,
            time: temp,
        };
        $.ajax({
            type: "POST",
            url: "<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
index.php/member/registration_scheduled/time_converter/",
            dataType: "json",
            data: sendData,
            success: function (results) {
                document.getElementById("eta_2_utc").value = results;
            },
        });
    }
</script>
