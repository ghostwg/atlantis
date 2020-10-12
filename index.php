<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    </head>
    <body>
        <table>
            <tbody>
                <tr>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Height</td>
                                    <td><input type="number" id="ht" name="ht"></td>
                                </tr>
                                <tr>
                                    <td>Widht</td>
                                    <td><input type="number" id="wt" name="wt"></td>
                                </tr>
                                <tr>
                                    <td>Deep</td>
                                    <td><input type="number" id="dt" name="dt"></td>
                                </tr>
                                <tr>
                                    <td>Door(s)</td>
                                    <td><input type="number" id="dr" name="dr"></td>
                                </tr>
                                <tr>
                                    <td>Shelf(s)</td>
                                    <td><input type="number" id="sh" name="sh"></td>
                                </tr>
                                <tr>
                                    <td>Unit Type</td>
                                    <td><input type="radio" name="unit_type" value="1" onchange='check_value()'>Wall Unit
                                        <input type="radio" name="unit_type" value="2" onchange='check_value()'>Tall Unit<br>
                                        <input type="radio" name="unit_type" value="3" onchange='check_value()'>Base Unit
                                        <input type="radio" name="unit_type" value="4" onchange='check_value()'>Wardrobe</td>
                                </tr>
                                <tr>
                                    <td>Construction</td>
                                    <td><input type="checkbox" name="bpt" value="1">18mm. back panel <input type="checkbox" name="ftx" value="1">FittingX</td>
                                </tr>
                                <tr>
                                    <td><input type="submit" value="Calculate"></td>
                                    <td></td>
                                </tr>
                                
                            </tbody>
                        </table>
                        </form>
                    </td>
                    <td>
                        <div id='imagedest'></div>
                    </td>
                </tr>
            </tbody>
        </table>
          
    <?php
    
    include './functions/c_wall_unit_functions.php';
    include './functions/c_base_unit_functions.php';
    include './functions/c_tall_unit_functions.php';
    include './functions/w_unit_functions.php';
        // put your code here
    $bpd=0;
    $bp_gr_shift=0.6;
        if (!empty(filter_input_array(INPUT_POST))){
            if (filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING) == "POST") {
                if (filter_input(INPUT_POST,"unit_type")<>0){
                $ht = filter_input(INPUT_POST,"ht");             // Height
                $wt = filter_input(INPUT_POST,"wt");             // Widght
                $dt = filter_input(INPUT_POST,"dt");             // Deep
                $door = filter_input(INPUT_POST,"dr");           // Amount of doors
                $shelf = filter_input(INPUT_POST,"sh");          // Amount of shelves
                $type=filter_input(INPUT_POST,"unit_type");      // Unit type: wall unit =0, tall unit = 1, base unit = 2.
                $bpd=filter_input(INPUT_POST,"bpt");             // Back panel 18mm.
                $ftx=filter_input(INPUT_POST,"ftx");             //automatic fittings for shelves set
                
                switch ($type){
                    case 1: is_kitchen_wall_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift); break;
                    case 2: is_kitchen_tall_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift); break;
                    case 3: is_kitchen_base_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift); break;
                    case 4: is_wardrobe_unit($ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift); break;
                }
            } else {
               echo "<script type='text/javascript'>alert('Choose the Unit type!');</script>";
            }
        }
    }
            
        function is_kitchen_wall_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift){
            //variables
            $bp_deep=8; // back panel size
            $door_gap=20; //door deep size
            $shelf_gap=20;
            $bpanel_gap=26;
                    
            if ($bpd==1){ $bp_deep=18;} // if back panel thickness 18mm checked, we set 18mm in to variable
            
            switch ($door) {                        // calculation of size and quantity of doors
                case 1:
                    $drl=$ht-4;
                    $drh=$wt-4;
                    $drqt=1;  
                    break;
                case 2:
                    $drl=$ht-4;
                    $drh=($wt*0.5)-4;
                    $drqt=2;  
                    break;
                default:
                    $drl=0;
                    $drh=0;
                    $drqt=0;
                    $door_gap=0;
                    break;
            }
                    
            $rpl=$lpl=$ht;                    //lenght of left, right, back panel, actually is height of unit
            $rph=$lph=$dt-$door_gap;                //height of left, right panel, actually is deep minus door gap
            $rpqt=$lpqt=$bpqt=$bkqt=$tpqt=1;        //quantity of left, right, back, top, bottom panels
            $bkpl=$ht-29;
            $bkph=$wt-29;                           //height of back panel, minus 28mm groove gap
            $tpl=$bpl=$shl=$wt-36;                  //lenght of top, bottom and shelf panels, minus sum of thickness size of left and right panel
            $tph=$bph=$dt-$door_gap-2;    //height of top, bottom panel, deep minus door gap and back panel gap
         
            if ($shelf>0){
                $shh=$dt-$door_gap-$shelf_gap-$bpanel_gap;
                $shqt=$shelf;
            } else {
                $shh=$shqt=$shl=0;
            }
            c_w_create_file($shelf,$rpl,$rph,$lpl,$lph,$bpl,$bph,$tpl,$tph,$bkpl,$bkph,$shl,$shh,$drl,$drh,$bp_deep,$ftx,$bp_gr_shift);
            show_table($ht,$wt,$dt,$door,$shelf,$type,$rpl,$rph,$rpqt,$lpl,$lph,$lpqt,$bpl,$bph,$bpqt,$tpl,$tph,$tpqt,$bkpl,$bkph,$bkqt,$shl,$shh,$shqt,$drl,$drh,$drqt,$bp_deep);
        }
        
        function is_kitchen_base_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift){
            //variables
            $bp_deep=8; // back panel size by default
            $door_gap=20; //door deep size
            $shelf_gap=20;
            $bpanel_gap=50;
                    
            if ($bpd==1){ $bp_deep=18;} // if back panel thickness 18mm checked, we set 18mm in to variable
            
            switch ($door) {                        // calculation of size and quantity of doors
                case 1:
                    $drl=$ht-4;
                    $drh=$wt-4;
                    $drqt=1;  
                    break;
                case 2:
                    $drl=$ht-4;
                    $drh=($wt*0.5)-4;
                    $drqt=2;  
                    break;
                default:
                    $drl=0;
                    $drh=0;
                    $drqt=0;
                    $door_gap=0;
                    break;
            }
                    
            $rpl=$lpl=$ht;                    //lenght of left, right, back panel, actually is height of unit
            $rph=$lph=$dt-$door_gap;                //height of left, right panel, actually is deep minus door gap
            $rpqt=$lpqt=$bpqt=$bkqt=1;              //quantity of left, right, back, bottom panels
            $tpqt=2;                                //quantity top panels
            $bkpl=$ht-29;
            $bkph=$wt-15;                              //height of back panel, minus 28mm groove gap
            $tpl=$bpl=$shl=$wt-36;                  //lenght of top, bottom and shelf panels, minus sum of thickness size of left and right panel
            $tph=150;                               //height of top panel
            $bph=$dt-$door_gap-$bpanel_gap;//height of bottom panel, deep minus door gap and back panel gap
         
            if ($shelf>0){
                $shh=$dt-(($door_gap+$shelf_gap)+($bpanel_gap+$bp_deep));
                $shqt=$shelf;
            } else {
                $shh=$shqt=$shl=0;
            }
            
            c_b_create_file($shelf,$rpl,$rph,$lpl,$lph,$bpl,$bph,$tpl,$tph,$bkpl,$bkph,$shl,$shh,$drl,$drh,$bp_deep,$ftx,$bp_gr_shift);
            show_table($ht,$wt,$dt,$door,$shelf,$type,$rpl,$rph,$rpqt,$lpl,$lph,$lpqt,$bpl,$bph,$bpqt,$tpl,$tph,$tpqt,$bkpl,$bkph,$bkqt,$shl,$shh,$shqt,$drl,$drh,$drqt,$bp_deep);
        }
        
        function is_kitchen_tall_unit($bpd,$ht,$wt,$dt,$door,$shelf,$type,$ftx,$bp_gr_shift){
            //variables
            $bp_deep=8; // back panel size
            $door_gap=20; //door deep size
            $shelf_gap=20;
            $bpanel_gap=50;
                    
            if ($bpd==1){ $bp_deep=18;} // if back panel thickness 18mm checked, we set 18mm in to variable
            
            switch ($door) {                        // calculation of size and quantity of doors
                case 1:
                    $drl=$ht-4;
                    $drh=$wt-4;
                    $drqt=1;  
                    break;
                case 2:
                    $drl=$ht-4;
                    $drh=($wt*0.5)-4;
                    $drqt=2;  
                    break;
                default:
                    $drl=0;
                    $drh=0;
                    $drqt=0;
                    $door_gap=0;
                    break;
            }
                    
            $rpl=$lpl=$ht;                          //lenght of left, right, back panel, actually is height of unit
            $rph=$lph=$dt-$door_gap;                //height of left, right panel, actually is deep minus door gap
            $rpqt=$lpqt=$bpqt=$bkqt=$tpqt=1;        //quantity of left, right, back, top, bottom panels
            $bkph=$wt-28;$bkpl=$ht-28;              //height of back panel, minus 28mm groove gap
            $tpl=$bpl=$shl=$wt-36;                  //lenght of top, bottom and shelf panels, minus sum of thickness size of left and right panel
            $tph=$bph=$dt-$door_gap-$bpanel_gap;    //height of top, bottom panel, deep minus door gap and back panel gap
         
            if ($shelf>0){
                $shh=$dt-$door_gap-$shelf_gap-$bpanel_gap-$bp_deep;
                $shqt=$shelf;
            } else {
                $shh=$shqt=$shl=0;
            }
            c_t_create_file($shelf,$rpl,$rph,$lpl,$lph,$bpl,$bph,$tpl,$tph,$bkpl,$bkph,$shl,$shh,$drl,$drh,$bp_deep,$ftx,$bp_gr_shift);
            show_table($ht,$wt,$dt,$door,$shelf,$type,$rpl,$rph,$rpqt,$lpl,$lph,$lpqt,$bpl,$bph,$bpqt,$tpl,$tph,$tpqt,$bkpl,$bkph,$bkqt,$shl,$shh,$shqt,$drl,$drh,$drqt,$bp_deep);
        }
        
        function is_wardrobe_unit($ht,$wt,$dt,$door,$shelf,$type,$ftx){
            //variables
            $bp_deep=18; // back panel size
            $door_gap=20; //door deep size
            $shelf_gap=20;
           
            switch ($door) {                        // calculation of size and quantity of doors
                case 1:
                    $drl=$ht-4;
                    $drh=$wt-4;
                    $drqt=1;  
                    break;
                case 2:
                    $drl=$ht-4;
                    $drh=($wt*0.5)-4;
                    $drqt=2;  
                    break;
                default:
                    $drl=0;
                    $drh=0;
                    $drqt=0;
                    $door_gap=0;
                    break;
            }
                    
            $rpl=$lpl=$bkpl=$ht;                          //lenght of left, right, back panel, actually is height of unit
            $rph=$lph=$bph=$tph=$dt-$door_gap-$bp_deep;                //height of left, right panel, actually is deep minus door gap
            $rpqt=$lpqt=$bpqt=$bkqt=$tpqt=1;        //quantity of left, right, back, top, bottom panels
            $bkph=$wt;              //height of back panel, minus 28mm groove gap
            $tpl=$bpl=$shl=$wt-36;                  //lenght of top, bottom and shelf panels, minus sum of thickness size of left and right panel
            
         
            if ($shelf>0){
                $shh=$dt-$door_gap-$shelf_gap-$bp_deep;
                $shqt=$shelf;
            } else {
                $shh=$shqt=$shl=0;
            }
            w_create_file($shelf,$rpl,$rph,$lpl,$lph,$bpl,$bph,$tpl,$tph,$bkpl,$bkph,$shl,$shh,$drl,$drh,$ftx);
            show_table($ht,$wt,$dt,$door,$shelf,$type,$rpl,$rph,$rpqt,$lpl,$lph,$lpqt,$bpl,$bph,$bpqt,$tpl,$tph,$tpqt,$bkpl,$bkph,$bkqt,$shl,$shh,$shqt,$drl,$drh,$drqt,$bp_deep);
        }

        
        function show_unit_desc_by_type($type){
            switch ($type){
                    case 1: $msg='Kitchen Wall Unit'; break;
                    case 2: $msg='Kitchen Tall Unit'; break;
                    case 3: $msg='Kitchen Base Unit'; break;
                    case 4: $msg='Wardrobe Unit'; break;
                }
            return $msg;
        }
        
        function show_unit_picture_by_type($type){
            switch ($type){
                    case 1: $pic='unassembled_w_unit.png'; break;
                    case 2: $pic='unassembled_w_unit.png'; break;
                    case 3: $pic='unassembled_b_unit.png'; break;
                    case 4: $pic='unassembled_b_unit.png'; break;
                }
            return $pic;
        }

        function show_table($ht,$wt,$dt,$door,$shelf,$type,$rpl,$rph,$rpqt,$lpl,$lph,$lpqt,$bpl,$bph,$bpqt,$tpl,$tph,$tpqt,$bkpl,$bkph,$bkqt,$shl,$shh,$shqt,$drl,$drh,$drqt,$bp_deep){
            echo '<table border="1" cellspacing="2" cellpadding="2" id="printTable">
                    <tbody>
                        <tr><td>
                            <button style="float: right; color: red;">Print</button>
                            <br><b>'. show_unit_desc_by_type($type).' H*W*D: '.$ht.'*'.$wt.'*'.$dt.'.</b><br>
                            Door(s):'.$door.'.<br>
                            Shelf(s):'.$shelf.'.<br>
                            <table border="1" cellspacing="2" cellpadding="2" >
                                <tbody>
                                    <tr>
                                        <td>#</td>
                                        <td>Description</td>
                                        <td>Lenght</td>
                                        <td>Height</td>
                                        <td>Thickness</td>
                                        <td>Qty.</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>RH panel</td>
                                        <td>'.$rpl.'</td>
                                        <td>'.$rph.'</td>
                                        <td></td>
                                        <td>'.$rpqt.'</td>
                                        <td>
                                            <a href=\'files/RH.tcn\' download>
                                                <img src=\'res/download.png\' width="18" height="18">
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>LH panel</td>
                                        <td>'.$lpl.'</td>
                                        <td>'.$lph.'</td>
                                        <td></td>
                                        <td>'.$lpqt.'</td>
                                        <td><a href=\'files/LH.tcn\' download>
                                            <img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Top panel</td>
                                        <td>'.$tpl.'</td>
                                        <td>'.$tph.'</td>
                                        <td></td>
                                        <td>'.$tpqt.'</td>
                                        <td><a href=\'files/TP.tcn\' download>
                                            <img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Bottom panel</td>
                                        <td>'.$bpl.'</td>
                                        <td>'.$bph.'</td>
                                        <td></td>
                                        <td>'.$bpqt.'</td>
                                        <td><a href=\'files/BP.tcn\' download>
                                            <img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Back panel</td>
                                        <td>'.$bkpl.'</td>
                                        <td>'.$bkph.'</td>
                                        <td>'.$bp_deep.'</td>
                                        <td>'.$bkqt.'</td>
                                        <td><a href=\'files/BKP.tcn\' download><img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Shelf</td>
                                        <td>'.$shl.'</td>
                                        <td>'.$shh.'</td>
                                        <td></td>
                                        <td>'.$shqt.'</td>
                                        <td><a href=\'files/Shelf.tcn\' download><img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Door</td>
                                        <td>'.$drl.'</td>
                                        <td>'.$drh.'</td>
                                        <td></td>
                                        <td>'.$drqt.'</td>
                                        <td><a href=\'files/Door.tcn\' download><img src=\'res/download.png\' width="18" height="18"></td>
                                    </tr>
                                    <tr>
                                        <img src=res/'.show_unit_picture_by_type($type).'>
                                    </tr>
                                </tbody>
                            </table>
                        </td></tr>
                    </tbody>
                </table>';
        }
        
    ?>
    
    <script>   
        function printData()
        {
            var divToPrint=document.getElementById("printTable");
            newWin= window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }

        $('button').on('click',function(){
            printData();
        })
        
        <script language='JavaScript' type='text/javascript'>

    function check_value()
    {
        switch(document.test.field.value)
        {
            case "1":
                document.getElementById("imagedest").innerHTML = "<img src='res/k_w_unit.png'>";
                break;
            case "2":
                document.getElementById("imagedest").innerHTML = "<img src='res/c_tall_rh_1sh.png'>"; 
                break;
            case "3":
                document.getElementById("imagedest").innerHTML = "<img src='res/k_b_unit.jpg'>"; 
                break;
            case "4":
                document.getElementById("imagedest").innerHTML = "<img src='res/c_tall_2d_1sh.png'>"; 
                break;
        }
    }

</script>
    
    </body>
</html>
