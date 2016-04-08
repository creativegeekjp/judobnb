<div class="wrap hs-admin-theme">

    <div id="icon-options-general" class="icon32"></div>
    <h2>HS Custom Admin Theme Style</h2>
    <a href="?mode=addtheme" class="button-primary createnew"><?php _e('Create New HS Custom Theme','hs_admin_theme') ?></a>
    <a href="?mode=restoredefault" class="button-primary createnew"><?php _e('Restore to Default','hs_admin_theme') ?> </a>
    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">
                <?php if ($verify == "AddNewTheme") { ?>
                    <div class="postbox add-custom-theme">

                        <h3><span><?php _e('Create New HS Custom Admin Theme Style', 'hs_admin_theme') ?></span></h3>
                        <div class="inside">
                            <form name="newtheme" method="post" action="">
                                <table> 
                                    <tr>
                                        <td>
                                            <?php _e('Theme Name', 'hs_admin_theme') ?>
                                        </td> 
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input type="text" name="themename" placeholder="Theme Name" required="required" value="<?php echo $themename; ?>" ><div class="tooltip"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php _e('Menu Background Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" value="<?php echo $menubgcolor; ?>" name="menubgcolor" id="menubgcolor" onchange="submit();" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                           <?php _e('Menu Background Color on Hover ', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menuhoverbgcolor" id="menuhoverbgcolor" value="<?php echo $menuhoverbgcolor; ?>" onchange="submit();">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php _e('Sub-Menu Background Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="submenubgcolor" id="submenubgcolor" value="<?php echo $submenubgcolor; ?>" onchange="submit();">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php _e('Menu Font Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menufontcolor" id="menufontcolor" value="<?php echo $menufontcolor; ?>" onchange="submit();">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php _e('Menu Font Color on Hover', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menufontcolorhover" id="menufontcolorhover"  value="<?php echo $menufontcolorhover; ?>" onchange="submit();">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input class="button-primary" type="submit" name="save_newtheme" value="<?php _e('Save', 'hs_admin_theme'); ?>" /> 
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div> <!-- .inside -->

                    </div> <!-- .postbox -->  
                    <?php
                } elseif ($verify == "EditTheme") {
                    ?>
                    <div class="postbox edit-custom-theme">

                        <h3><span><?php _e("Update/Save As HS Custom Admin Theme Style",'hs_admin_theme' ) ?></span></h3>
                        <div class="inside">
                            <form name="updatetheme" method="post" action="">
                                <input type="hidden" name="themeid" value="<?php echo $themeid; ?>">
                                <table > 
                                    <tr>
                                        <td>
                                             <?php _e('Theme Name', 'hs_admin_theme') ?>
                                        </td> 
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input type="text" name="themenameupdate" placeholder="Theme Name" required="required" value="<?php echo $themename; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                             <?php _e('Menu Background Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menubgcolor" id="menubgcolor"  value=<?php echo $menubgcolor; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php _e('Menu Background Color on Hover ', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menuhoverbgcolor" id="menuhoverbgcolor"  value=<?php echo $menuhoverbgcolor; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                             <?php _e('Sub-Menu Background Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="submenubgcolor" id="submenubgcolor" value=<?php echo $submenubgcolor; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                           <?php _e('Menu Font Color', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menufontcolor" id="menufontcolor" value=<?php echo $menufontcolor; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                             <?php _e('Menu Font Color on Hover', 'hs_admin_theme') ?>
                                        </td>
                                        <td>
                                            :
                                        </td>
                                        <td>
                                            <input class="color {hash:true}" name="menufontcolorhover" id="menufontcolorhover" value=<?php echo $menufontcolorhover; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="100%">

                                            <?php
                                            if ($themeid > '3') {
                                                ?>
                                                <input class="button-primary" type="submit" name="update_theme" value="<?php _e('Update', 'hs_admin_theme'); ?>" /> 
                                            <?php }
                                            ?>
                                            <input class="button-primary" type="button" id="saveas_btn" name="saveas_theme" value="<?php _e('Save As', 'hs_admin_theme'); ?>" onclick="getfield();" /> 
                                            <input type="text" name="themename" id="themename" placeholder="Theme Name"  style="visibility: hidden;">
                                            <input class="button-primary" type="submit" id="saveas_newtheme" name="save_newtheme" value="<?php _e('Save As', 'hs_admin_theme'); ?>" style="visibility: hidden;" /> 
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div> <!-- .inside -->

                    </div> <!-- .postbox -->  
                <?php }
                ?>
                <div class="postbox our-theme">

                    <h3><span>Available Themes</span></h3>
                    <div class="inside">
                        <form name="readythemes" method="post" action="">
                            <table> 
                                <tr>
                                    <td>
                                        Select Theme</td> 
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="100%">

                                        <?php
                                        global $wpdb;
                                        $table_name = $wpdb->prefix . "hs_admin_theme";
                                        $sql = $wpdb->get_results("SELECT * FROM $table_name ");
                                        foreach ($sql as $row) {
                                            ?>
                                            <label class="customthemes">
                                                <input type="radio" name="selecttheme" id="<?php echo $row->id ?>" <?php checked(@$selecttheme, $row->id); ?> value="<?php echo $row->id ?>" onchange="submit();">
                                                <label for="<?php echo $row->id ?>"><?php echo $row->theme; ?> </label><?php if ($row->id > '3') { ?><a href="?mode=delete&del_id=<?php echo $row->id ?>"> X</a> <?php } ?>
                                                <table>
                                                    <tr>
                                                        <td style="background:<?php echo $row->menubg; ?> ">&nbsp;</td>
                                                        <td style="background:<?php echo $row->menuhoverbg; ?> ">&nbsp;</td>
                                                        <td style="background:<?php echo $row->submenubg; ?> ">&nbsp;</td>
                                                        <td style="background:<?php echo $row->menufontcolor; ?> ">&nbsp;</td>
                                                        <td style="background:<?php echo $row->menufonthover; ?> ">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </label>

                                        <?php } ?>

                                    </td>
                                </tr>

                            </table>
                        </form>
                    </div> <!-- .inside -->

                </div> <!-- .postbox -->  

            </div> <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox aboutus">

                        <h3><span>About Company</span></h3>
                        <div class="inside">
                            <a href="http://heliossolutions.in/" target="_blank"><img src="<?php echo $hs_admin_theme_plugin_url ?>/images/cmp_logo.png"></a>
                            <p >Helios Solution is an Indian IT outsourcing company who works on many IT technologies such as wordpress, magento, joomla, drupal, opencart, cakephp, .NET etc </p>
                        </div> <!-- .inside -->
                    </div> <!-- .postbox -->


                </div> <!-- .meta-box-sortables -->

            </div> <!-- #postbox-container-1 .postbox-container -->

        </div> <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div> <!-- #poststuff -->

</div> <!-- .wrap -->

<script>
    function getfield(){
        document.getElementById("themename").style.visibility="visible";
        document.getElementById("saveas_newtheme").style.visibility="visible";
        document.getElementById("saveas_btn").style.display="none";
    }
</script>