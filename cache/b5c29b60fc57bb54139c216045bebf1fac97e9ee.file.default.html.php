<?php /* Smarty version Smarty-3.0.7, created on 2015-11-25 17:44:33
         compiled from "application/views\member/welcome/default.html" */ ?>
<?php /*%%SmartyHeaderCode:113725655e571719161-71958129%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b5c29b60fc57bb54139c216045bebf1fac97e9ee' => 
    array (
      0 => 'application/views\\member/welcome/default.html',
      1 => 1448469866,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113725655e571719161-71958129',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="dashboard-welcome">
    <h3><?php echo $_smarty_tpl->getVariable('tanggal')->value['hari'];?>
, <?php echo $_smarty_tpl->getVariable('tanggal')->value['tanggal'];?>
</h3>
    <!-- notification template -->
    <?php $_template = new Smarty_Internal_Template("base/templates/notification.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
    <!-- end of notification template-->
    <div class="dashboard-profile">
        <div style="float: left;">
            <div class="dashboard-profile-sidebar">
                <h4>Welcome</h4>
                <div class="list">
                    <div style="height: 150px; overflow: auto;">
                        
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div style="float: right;">
            <div class="dashboard-profile-content">
                
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!-- clocl -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#countdown').ClassyCountdown({
            end: '<?php echo $_smarty_tpl->getVariable('end')->value;?>
',
            now: '<?php echo $_smarty_tpl->getVariable('now')->value;?>
',
            labels: true,
            style: {
                element: "",
                textResponsive: .5,
                days: {
                    gauge: {
                        thickness: .05,
                        bgColor: "rgba(0,0,0,0)",
                        fgColor: "#1abc9c",
                        lineCap: 'round'
                    },
                    textCSS: 'font-family:\'Open Sans\'; font-size:55px; font-weight:700; color:#34495e;'
                },
                hours: {
                    gauge: {
                        thickness: .05,
                        bgColor: "rgba(0,0,0,0)",
                        fgColor: "#2980b9",
                        lineCap: 'round'
                    },
                    textCSS: 'font-family:\'Open Sans\'; font-size:55px; font-weight:700; color:#34495e;'
                },
                minutes: {
                    gauge: {
                        thickness: .05,
                        bgColor: "rgba(0,0,0,0)",
                        fgColor: "#8e44ad",
                        lineCap: 'round'
                    },
                    textCSS: 'font-family:\'Open Sans\'; font-size:55px; font-weight:700; color:#34495e;'
                },
                seconds: {
                    gauge: {
                        thickness: .05,
                        bgColor: "rgba(0,0,0,0)",
                        fgColor: "#f39c12",
                        lineCap: 'round'
                    },
                    textCSS: 'font-family:\'Open Sans\'; font-size:55px; font-weight:700; color:#34495e;'
                }

            },
            onEndCallback: function() {
                console.log("Time out!");
            }
        });
    });
</script>
