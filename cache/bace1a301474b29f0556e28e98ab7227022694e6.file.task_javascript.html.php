<?php /* Smarty version Smarty-3.0.7, created on 2015-09-22 05:30:26
         compiled from "application/views\task/izin_rute/task_javascript.html" */ ?>
<?php /*%%SmartyHeaderCode:296405600cb52c00f93-96747931%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bace1a301474b29f0556e28e98ab7227022694e6' => 
    array (
      0 => 'application/views\\task/izin_rute/task_javascript.html',
      1 => 1442892347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '296405600cb52c00f93-96747931',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script language="javascript" type="text/javascript">
    /*
     * Ajax
     */
    $(document).ready(function() {
        /*
         * NOTES
         */
        // dialog box
        $('#form-notes').dialog({
            autoOpen: false,
            modal: true,
            title: "Catatan Rute Penerbangan",
            draggable: false,
            width: 400,
            height: 170
        });
        // trigger dialog notes
        $('.button-notes-red').click(function() {
            // open dialog
            var izin_id = $(this).attr('title');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: 'izin_id=' + izin_id,
                url: '<?php echo $_smarty_tpl->getVariable('config')->value->site_url("task/izin_rute/detail_notes_by_id/");?>
',
                success: function(result) {
                    //--
                    $('#notes_izin_id').val(izin_id);
                    $('#notes_value').val(result.notes);
                    // open
                    $('#form-notes').dialog('open');
                }
            });
            // return false
            return false;
        });
        // trigger dialog notes
        $('.button-notes').click(function() {
            // open dialog
            var izin_id = $(this).attr('title');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: 'izin_id=' + izin_id,
                url: '<?php echo $_smarty_tpl->getVariable('config')->value->site_url("task/izin_rute/detail_notes_by_id/");?>
',
                success: function(result) {
                    //--
                    $('#notes_izin_id').val(izin_id);
                    $('#notes_value').val(result.notes);
                    // open
                    $('#form-notes').dialog('open');
                }
            });
            // return false
            return false;
        });
        // process save notes
        $('#form-tambah-notes').submit(function(event) {
            // --
            event.preventDefault();
            // --
            $(document).ajaxStart(function() {
                $("#ajax-loader-notes").show();
            });
            $(document).ajaxStop(function() {
                $("#ajax-loader-notes").hide();
            });
            // process ajax
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                url: '<?php echo $_smarty_tpl->getVariable('config')->value->site_url("task/izin_rute/save_notes");?>
',
                success: function(result) {
                    if (result.notes !== '') {
                        $("#notes_" + result.izin_id).removeClass("button-notes").addClass("button-notes-red");
                    } else {
                        $("#notes_" + result.izin_id).removeClass("button-notes-red").addClass("button-notes");
                    }
                    // open
                    $('#form-notes').dialog('close');
                }
            });
            // --
            return false;
        });
        /*
         * CATATAN PERMOHONAN
         */
        // dialog box
        $('#form-catatan').dialog({
            autoOpen: false,
            modal: true,
            title: "Catatan Proses Permohonan",
            draggable: false,
            width: 500,
            height: 400
        });
        // trigger dialog catatan
        $('#button-catatan').click(function() {
            // open
            $('#form-catatan').dialog('open');
            // return false
            return false;
        });
        // process save notes
        $('#form-tambah-catatan').submit(function(event) {
            // --
            event.preventDefault();
            // --
            $(document).ajaxStart(function() {
                $("#ajax-loader-catatan").show();
            });
            $(document).ajaxStop(function() {
                $("#ajax-loader-catatan").hide();
            });
            // process ajax
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                url: '<?php echo $_smarty_tpl->getVariable('config')->value->site_url("task/izin_rute/save_catatan");?>
',
                success: function(result) {
                    // open
                    $('#form-catatan').dialog('close');
                }
            });
            // --
            return false;
        });
        /*
         * TELAAH STAFF
         */
        // dialog box
        $('#form-telaah').dialog({
            autoOpen: false,
            modal: true,
            title: "Telaah Staff",
            draggable: false,
            width: 400,
            height: 300
        });
        // trigger dialog telaah
        $('#button-telaah').click(function() {
            // open
            $('#form-telaah').dialog('open');
            // return false
            return false;
        });
        /*
         * SLOT CLEARANCE
         */
        // dialog box
        $('#form-slot').dialog({
            autoOpen: false,
            modal: true,
            title: "Slot Attachment",
            draggable: false,
            width: 600,
            height: 400
        });
        // trigger dialog slot
        $('#button-slot').click(function() {
            // open
            $('#form-slot').dialog('open');
            // return false
            return false;
        });
        /*
         * FILES ATTACHMENT
         */
        // dialog box
        $('#form-files').dialog({
            autoOpen: false,
            modal: true,
            title: "Files Attachment",
            draggable: false,
            width: 600,
            height: 450
        });
        // trigger dialog files
        $('#button-files').click(function() {
            // open
            $('#form-files').dialog('open');
            // return false
            return false;
        });

    });
</script>