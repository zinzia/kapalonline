<?php /* Smarty version Smarty-3.0.7, created on 2015-09-29 10:20:58
         compiled from "application/views\home/welcome/index.html" */ ?>
<?php /*%%SmartyHeaderCode:7413560a49ea41a2f3-43629676%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd729fc2a7ea1ec23ed7af840390084cf6498aded' => 
    array (
      0 => 'application/views\\home/welcome/index.html',
      1 => 1441883418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7413560a49ea41a2f3-43629676',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="content-home">
    <div class="login-box">
        <h4><?php echo $_smarty_tpl->getVariable('home_login_title')->value;?>
</h4>
        <?php if (!empty($_smarty_tpl->getVariable('com_user',null,true,false)->value)){?>
        <form action="#" method="post">
            <table width="100%">
                <tr>
                    <td colspan="2" align="center">
                        <img src="<?php echo $_smarty_tpl->getVariable('com_user')->value['operator_photo'];?>
" style="height: 64px; width: auto; padding-bottom: 10px;" alt="" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-family: tahoma; font-size: 11px;">
                        <b><?php echo ((mb_detect_encoding($_smarty_tpl->getVariable('com_user')->value['operator_name'], 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtoupper($_smarty_tpl->getVariable('com_user')->value['operator_name'],SMARTY_RESOURCE_CHAR_SET) : strtoupper($_smarty_tpl->getVariable('com_user')->value['operator_name']));?>
, <br />(<?php echo $_smarty_tpl->getVariable('com_user')->value['airlines_nm'];?>
)</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-family: tahoma; font-size: 11px;">
                        <?php echo $_smarty_tpl->getVariable('com_user')->value['operator_phone'];?>

                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-family: tahoma; font-size: 11px;">
                        <?php echo $_smarty_tpl->getVariable('com_user')->value['user_mail'];?>

                    </td>
                </tr>
                <tr>
                    <td width="100%" align="right" colspan="2" style="padding-top: 10px;">
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('member/welcome');?>
" style="color: #FF00BA;">Back to your private menu? Click Here!</a>
                    </td>
                </tr>
            </table>
        </form>
        <?php }else{ ?>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/login_process');?>
" method="post" autocomplete="off" class="login" accept-charset="utf-8">
            <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
            <table width="100%">
                <tr>
                    <td colspan="2">
                        Username :
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="text" name="username" style="width: 200px;" autocomplete="off"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Password :
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="password" name="pass" style="width: 200px;" autocomplete="off"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo (($tmp = @$_smarty_tpl->getVariable('captcha')->value['image'])===null||$tmp==='' ? '' : $tmp);?>
 <br/><br/><input type="text" name="captcha" size="10" maxlength="10" style="background-color:#f7ecbc;color:#000" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Masukkan kode diatas
                        <br>
                        
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('information/member/forget_password');?>
"><?php echo $_smarty_tpl->getVariable('home_forget')->value;?>
</a>
                    </td>
                    <td align="right">
                        <input type="submit" value="<?php echo $_smarty_tpl->getVariable('home_button_signin')->value;?>
" class="submit-button" style=""/>
                    </td>
                </tr>
            </table>
        </form>
        <?php }?>
    </div>
    <div class="info-box">
        <div class="list-subcorp">
            <div class="list-subcorp bdr-inside">
                <ul>
                    <li>
                        <a href="https://aol.dephub.go.id/siuau">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/izin.usaha.icon.png" alt="" /><br />
                            Izin Badan Usaha
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/izin.rute.icon.png" alt="" /><br />
                            Izin Rute Penerbangan (<em>Flight Route Permit</em>)
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/izin.terbang.icon.png" alt="" /><br />
                            Persetujuan Terbang <br />(<em>Flight Approval</em>)
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/report.produksi.png" alt="" /><br />
                            Pelaporan Data Produksi (<em>Reporting of Production</em>)
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/ontime.icon.png" alt="" /><br />
                            Pelaporan Data On-time Performance
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <img src="<?php echo $_smarty_tpl->getVariable('BASEURL')->value;?>
resource/doc/images/icon/perintis.icon.png" alt="" /><br />
                            Pelaporan Data Angkutan Udara Perintis
                        </a>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="info-box">
        <h4>Flight Approval</h4>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('/information/document/proses_cari/');?>
" method="post" autocomplete="false">
            <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
            <table class="table-input" width="100%">
                <tr>
                    <td colspan="2">
                        <?php echo $_smarty_tpl->getVariable('home_info_cek')->value;?>

                    </td>
                </tr>
                <tr>
                    <td width="80%">
                        <input name="published_no" maxlength="30" size="30" type="text" style="text-align: center;" placeholder="<?php echo $_smarty_tpl->getVariable('home_published_number')->value;?>
" autocomplete="off"/>
                    </td>
                    <td width="20%">
                        <button class="submit-button"><?php echo $_smarty_tpl->getVariable('home_button_find')->value;?>
</button>
                    </td>
                </tr>
            </table>
        </form>
        <div class="clear"></div>
        <br />
        <h4>Izin Rute</h4>
        <form action="<?php echo $_smarty_tpl->getVariable('config')->value->site_url('/information/document_izin/proses_cari/');?>
" method="post" autocomplete="false">
            <input type="hidden" name="<?php echo $_smarty_tpl->getVariable('token_nm')->value;?>
" value="<?php echo $_smarty_tpl->getVariable('token')->value;?>
"/>
            <table class="table-input" width="100%">
                <tr>
                    <td colspan="2">
                        <?php echo $_smarty_tpl->getVariable('home_info_izin')->value;?>

                    </td>
                </tr>
                <tr>
                    <td width="80%">
                        <input name="izin_published_letter" maxlength="30" size="30" type="text" style="text-align: center;" placeholder="<?php echo $_smarty_tpl->getVariable('home_published_number')->value;?>
" autocomplete="false"/>
                    </td>
                    <td width="20%">
                        <button class="submit-button"><?php echo $_smarty_tpl->getVariable('home_button_find')->value;?>
</button>
                    </td>
                </tr>
            </table>
        </form>
        <div class="clear"></div>
        <br />
        <h4><?php echo $_smarty_tpl->getVariable('home_info_title')->value;?>
</h4>
        <ul>
            <?php  $_smarty_tpl->tpl_vars['news'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('rs_news')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['news']->key => $_smarty_tpl->tpl_vars['news']->value){
?>
            <li>
                <a href="<?php echo $_smarty_tpl->getVariable('config')->value->site_url((($tmp = @('information/news/index/').($_smarty_tpl->tpl_vars['news']->value['news_id']))===null||$tmp==='' ? '' : $tmp));?>
">
                    <small><?php echo $_smarty_tpl->getVariable('dtm')->value->get_full_date($_smarty_tpl->tpl_vars['news']->value['news_post_date']);?>
</small><br />
                    <?php echo $_smarty_tpl->tpl_vars['news']->value['news_lang_title'];?>
<br />
                    <small style="color: #555;"><?php echo substr($_smarty_tpl->tpl_vars['news']->value['news_lang_content'],0,200);?>
...</small>
                </a>
            </li>
            <?php }} else { ?>
            <li>
                <a href="#">
                    Sorry , there are no updates
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="clear"></div>
</div>
