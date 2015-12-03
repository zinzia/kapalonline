<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 08:43:49
         compiled from "application/views\settings/role/access_update.html" */ ?>
<?php /*%%SmartyHeaderCode:13487565566b574c723-80784860%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ff52b57b3c7683f13584749d1b128304963ad52' => 
    array (
      0 => 'application/views\\settings/role/access_update.html',
      1 => 1441883422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13487565566b574c723-80784860',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!-- javascript -->
<script type="text/javascript">
    $(function() {
        $(".checked-all").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".r" + $(this).val()).prop('checked', true);
            } else {
                $(".r" + $(this).val()).prop('checked', false);
            }
        });
    })
</script>
<!-- end of javascript -->
<div class="head-content">
    <h3>Role Permissions - <?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_nm'])===null||$tmp==='' ? '' : $tmp);?>
</h3>
    <div class="head-content-nav">
        <ul>
            <li><a href="#" class="active">Edit Data</a></li>
            <li><a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminpermissions');?>
">List Data</a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<!-- notification template -->
<?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<!-- end of notification template-->
<form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('settings/adminpermissions/process');?>
" method="post">
    <input type="hidden" name="role_id" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('result')->value['role_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
    <table class="table-view" width="100%">
        <tr>
            <th width="5%"></th>
            <th width="55%">Judul Menu</th>
            <th width="10%">Create</th>
            <th width="10%">Read</th>
            <th width="10%">Update</th>
            <th width="10%">Delete</th>
        </tr>
        <?php echo (($tmp = @$_smarty_tpl->getVariable('list_menu')->value)===null||$tmp==='' ? '' : $tmp);?>

        <tr>
            <td colspan="6"><input type="submit" name="save" value="Simpan" class="edit-button" /></td>
        </tr>
    </table>
</form>