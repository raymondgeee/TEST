<?php
include $_SERVER['DOCUMENT_ROOT']."/version.php";
$path = $_SERVER['DOCUMENT_ROOT']."/".v."/Common Data/";
set_include_path($path);
include('Templates/mysqliConnection.php');
ini_set("display_errors", "on");

$accessType = isset($_POST['accessType']) ? $_POST['accessType'] : "";
if($accessType == "accessLog")
{
    $displayId = isset($_POST['displayId']) ? $_POST['displayId'] : "";
    
    $sql = "SELECT accesslogType FROM system_accesslog WHERE accesslogUser = '".$_SESSION['idNumber']."' AND accesslogSection = '".$displayId."' AND accesslogDate LIKE '".date("Y-m-d")."%' ORDER BY accesslogDate DESC LIMIT 1";
    $queryCheckAccess = $db->query($sql);
    if($queryCheckAccess AND $queryCheckAccess->num_rows > 0)
    {
        $resultCheckAccess = $queryCheckAccess->fetch_assoc();
        $accesslogType = $resultCheckAccess['accesslogType'];

        if($accesslogType == 0)
        {
            $sql = "INSERT INTO `system_accesslog`(`accesslogUser`, `accesslogSection`, `accesslogDate`, `accesslogType`) 
                                    VALUES ('".$_SESSION['idNumber']."', '".$displayId."', now(), 1)";
            $queryInsert = $db->query($sql);
        }
    }
    else
    {
        $sql = "INSERT INTO `system_accesslog`(`accesslogUser`, `accesslogSection`, `accesslogDate`, `accesslogType`) 
                                    VALUES ('".$_SESSION['idNumber']."', '".$displayId."', now(), 1)";
        $queryInsert = $db->query($sql);
    }

    exit();
}

function createHeader($displayId, $version="", $previousLink="", $homeIcon=0, $countNotification = 0)
{
    include('Templates/mysqliConnection.php');
    
    if($displayId == 'index')
    {
        $apiLogoPH = "<img style='width:70px; height:23px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logo.png' id='philLogo'>";
        $apiLogoJP = "<img style='width:70px; height:14px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logoJP.png' id='japanLogo'>";
        echo "&emsp;&emsp;<span style='padding:5px;' class='w3-round w3-light-grey'>";
            echo $apiLogoPH;
        echo "</span>&emsp;";
        echo "<span style='padding:5px;' class='w3-round w3-light-grey'>";
            echo $apiLogoJP;
        echo "</span>";
    }
    else
    {
        $sql = "SELECT accesslogSection FROM system_accesslog WHERE accesslogUser = '".$_SESSION['idNumber']."' AND accesslogDate LIKE '".date("Y-m-d")."%' ORDER BY accesslogDate DESC LIMIT 1";
        $queryCheckAccess = $db->query($sql);
        if($queryCheckAccess AND $queryCheckAccess->num_rows > 0)
        {
            $resultCheckAccess = $queryCheckAccess->fetch_assoc();
            $accesslogSection = $resultCheckAccess['accesslogSection'];
            $accesslogType = $resultCheckAccess['accesslogType'];

            if($accesslogSection != $displayId)
            {
                $sql = "INSERT INTO `system_accesslog`(`accesslogUser`, `accesslogSection`, `accesslogDate`, `accesslogType`) 
                                        VALUES ('".$_SESSION['idNumber']."', '".$displayId."', now(), 0)";
                $queryInsert = $db->query($sql);
            }
            else
            {
                if($accesslogType == 1)
                {
                    $sql = "INSERT INTO `system_accesslog`(`accesslogUser`, `accesslogSection`, `accesslogDate`, `accesslogType`) 
                                            VALUES ('".$_SESSION['idNumber']."', '".$displayId."', now(), 0)";
                    $queryInsert = $db->query($sql);
                }
            }
        }

        if($displayId != 'L4229')
        {
            $sql = "SELECT accesslogSection FROM system_accesslog WHERE accesslogSection != '".$displayId."' AND accesslogUser = '".$_SESSION['idNumber']."' AND accesslogDate LIKE '".date("Y-m-d")."%' ORDER BY accesslogDate DESC LIMIT 1";
            $queryCheckLast = $db->query($sql);
            if($queryCheckLast AND $queryCheckLast->num_rows > 0)
            {
                $resultCheckLast = $queryCheckLast->fetch_assoc();
                $accesslogSectionData = $resultCheckLast['accesslogSection'];

                if($accesslogSectionData == 'L4229')
                {
                    $previousLink = "/".v."/raymond_accountsv2.php";
                    $homeIcon = 1;
                }
            }
        }

        $inchargeData = "";
        $sql = "SELECT softwareIncharge FROM system_software WHERE displayId = '".$displayId."'";
        $querySoftware = $db->query($sql);
        if($querySoftware AND $querySoftware->num_rows > 0)
        {
            $resultSoftware = $querySoftware->fetch_assoc();
            $softwareIncharge = $resultSoftware['softwareIncharge'];

            if($softwareIncharge != '')
            {
                $sql = "SELECT * FROM hr_employee WHERE idNumber = '".$softwareIncharge."'";
                $queryEmployee = $db->query($sql);
                if($queryEmployee AND $queryEmployee->num_rows > 0)
                {
                    $resultEmployee = $queryEmployee->fetch_assoc();
                    $firstName = trim($resultEmployee['firstName']);
                    $firstNameJP = trim($resultEmployee['firstNameJP']);
                    $surName = trim($resultEmployee['surName']);
                    $surNameJP = trim($resultEmployee['surNameJP']);
                    $profilePicture = $resultEmployee['picture'];

                    $proPic = $_SERVER['DOCUMENT_ROOT']."/".v."/11-A Employee List V2/profilepictures/".$profilePicture;
                    if(file_exists($proPic) AND $profilePicture != '')
                    {
                        $cache = filemtime($proPic);
                        $profilePicture = "/".v."/11-A Employee List V2/profilepictures/".$profilePicture."?".$cache;
                    }
                    else
                    {
                        $profilePicture = "/".v."/11-A Employee List V2/profilepictures/defaultpic.png";
                    }

                    $fullNameJP = $firstNameJP;

                    if(trim($fullNameJP) != '')
                    {
                        $fullName .= "&emsp;<label>".displayText('L241', 'utf8', 0, 0, 1);
                        $fullName .= "<br><span class='w3-tiny'>".strtoupper($firstName)."</span>";
                        $fullNameJP = " <span>&#8226;</span> <span class='w3-tiny'>".$fullNameJP."</span></label>";
                    }
                    else
                    {
                        $fullName .= "&emsp;<label>".displayText('L241', 'utf8', 0, 0, 1);
                        $fullName .= "<br><span class='w3-tiny'>".strtoupper($firstName)."</span></label>";
                    }

                    $inchargeData.= "<div id='containerIncharge'>";
                        $inchargeData.= "<div class='innerIncharge'>";
                            $inchargeData.= "<div>";
                                $inchargeData.= "<img class='w3-round w3-card-2' src='".$profilePicture."' style='width: 40px; height: 40px;'/>";
                            $inchargeData.= "</div>";
                            $inchargeData.= "<div>";
                                $inchargeData.= $fullName.$fullNameJP;
                            $inchargeData.= "</div>";
                        $inchargeData.= "</div>";
                    $inchargeData.= "</div>";
                }
            }
        }

        $sql = "SELECT * FROM system_templatesettings WHERE templateName = 'Home'";
        $queryHome = $db->query($sql);
        if($queryHome AND $queryHome->num_rows > 0)
        {
            $resultHome = $queryHome->fetch_assoc();
            $iconColor = $resultHome['iconColor'];
            $color1 = $resultHome['color1'];
            $color2 = $resultHome['color2'];
            $color3 = $resultHome['color3'];
            $color4 = $resultHome['color4'];
        }

        if($displayId == '0')
        {
            $lunchConfirmFlag = 0;
            if($_GET['country'] == 2)
            {
                $fullName = $departmentName = $picture = $changePasswordBtn = "";
                $sql = "SELECT CONCAT(surName,', ',firstName) AS fullName, picture, departmentId, employeeId FROM hr_employee WHERE idNumber LIKE '".$_SESSION['idNumber']."' LIMIT 1";
                $query = $db->query($sql);
                if($query AND $query->num_rows > 0)
                {
                    $result = $query->fetch_assoc();
                    $employeeId = $result['employeeId'];
                    $fullName = $result['fullName'];
                    $picture = trim($result['picture']);
                    $departmentId = $result['departmentId'];

                    $sql = "SELECT departmentName FROM hr_department WHERE departmentId = ".$departmentId." LIMIT 1";
                    $queryDeptName = $db->query($sql);
                    if($queryDeptName AND $queryDeptName->num_rows > 0)
                    {
                        $resultDeptName = $queryDeptName->fetch_assoc();
                        $departmentName = $resultDeptName['departmentName'];
                        $departmentName1 = $resultDeptName['departmentName'];
                    }
                }

                $idNumberConfirmArray = Array();
                $sql = "SELECT * FROM system_lunchboxconfirmation WHERE status = 0 AND idNumber = '".$_SESSION['idNumber']."' AND sendDate = '".date('Y-m-d')."'";
                $queryLunchOrderConfirm = $db->query($sql);
                if($queryLunchOrderConfirm AND $queryLunchOrderConfirm->num_rows > 0)
                {
                    $resultLunchOrderConfirm = $queryLunchOrderConfirm->fetch_assoc();

                    $sql = "SELECT * FROM system_lunchboxemailreceipient WHERE idNumber = '".$_SESSION['idNumber']."'";
                    $queryLunch = $db->query($sql);
                    if($queryLunch AND $queryLunch->num_rows > 0)
                    {
                        $resultLunch = $queryLunch->fetch_assoc();
                        $emailType = $resultLunch['emailType'];

                        if($emailType == 0) # EMAIL ONLY
                        {
                            $lunchConfirmFlag = 0;
                        }
                        else if($emailType == 1) # EMAIL & PMS ONLY
                        {
                            $lunchConfirmFlag = 1;
                        }
                    }
                }

                if($lunchConfirmFlag == 1)
                {
                    $sql = "SELECT DISTINCT sendingTime FROM system_lunchboxemailreceipient";
                    $queryTime = $db->query($sql);
                    if($queryTime AND $queryTime->num_rows > 0)
                    {
                        $resultTime = $queryTime->fetch_assoc();
                        $sendingTime = date('Y-m-d')." ".$resultTime['sendingTime'];
                    }

                    $sql = "SELECT SUM(quantity) AS totalBox FROM hr_lunchbox WHERE inputDate = '".date('Y-m-d')."'";
                    $querySum = $db->query($sql);
                    if($querySum AND $querySum->num_rows > 0)
                    {
                        $resultSum = $querySum->fetch_assoc();
                        $totalBox = $resultSum['totalBox'];
                    }
                    // $sendingTime = date('Y-m-d')." 07:15:00";
                    $sql = "SELECT timeIn FROM hr_dtr WHERE employeeId = '".$_SESSION['idNumber']."' AND timeIn LIKE '".date('Y-m-d')."%' ORDER BY dtrId ASC LIMIT 1";
                    $queryTimeIn = $db->query($sql);
                    if($queryTimeIn AND $queryTimeIn->num_rows > 0)
                    {
                        $resultTimeIn = $queryTimeIn->fetch_assoc();
                        $timeIn = $resultTimeIn['timeIn'];

                        if($timeIn > $sendingTime)
                        {
                            $lunchConfirmFlag = 0;
                        }
                    }
                    else
                    {
                        $lunchConfirmFlag = 0;
                    }

                    if(in_array($_SESSION['idNumber'], Array('0412','0280'))) $lunchConfirmFlag = 1;
                }
            }

            echo "<div class='row'>";
                echo "<div class='col-md-12 w3-padding w3-round-large w3-card-2' style='background: linear-gradient(to right,white, #056291);'>";
                    echo "<div class='row'>";
                        echo "<div class='col-md-2 col-xs-12 col-sm-12'>";
                            $apiLogo = "<img src='/".v."/Common Data/Templates/images/arkLogo_crop.png' class='w3-padding-top'>";
                            if($_GET['country'] == 1) $apiLogo = "<img src='/".v."/Common Data/Responsive Dashboard/image/Zeus6.png' class='w3-padding' style = 'width: 170px; height: 75px;'>";
                            echo $apiLogo;
                        echo "</div>";
                        echo "<div class='col-md-4 col-xs-12 col-sm-12'>";
                            if($lunchConfirmFlag == 1)
                            {
                                echo "<div class='row'>";
                                    echo "<div class='col-md-2'>";
                                        echo "<img class='w3-card-2 w3-round' src='/".v."/11-8 Attendance Board/img/lunchbox.png' style='width:60px; height:65px;' />";
                                    echo "</div>";
                                    echo "<div class='col-md-7'>";
                                        echo "<label class='w3-medium' style='position:absolute;'>Have you ordered the obentou?<br>お弁当を注文しましたか?<br>".$totalBox."個注文してください</label>";
                                    echo "</div>";
                                    echo "<div class='col-md-3'>";
                                        echo "<button id='btnYes' class='w3-btn w3-round w3-indigo btnShake'><b>YES はい</b></button>&emsp;";
                                    echo "</div>";
                                echo "</div>";
                            }
                            if($logintype == 1)
                            {
                                $nameUser = "Barcode ID";
                                ?>
                                <div class="w3-container w3-round w3-padding w3-white w3-card-2">
                                    <form id = "formLogin" action="raymond_PMSLogin.php?do=login&redirect=1" method="post"></form>
                                    <input type = "hidden" value="" id = "loginUserName" name = "loginUserName" class = "w3-input w3-border w3-card-4 w3-pale-yellow" form = "formLogin">
                                    <input type = "hidden" value="" id = "loginPassword" name = "loginPassword" class = "w3-input w3-border w3-card-4 w3-pale-yellow" form = "formLogin">
                                    <input type = "hidden" value="1" name = "loginSubmit" class = "w3-input w3-border w3-card-4 w3-pale-yellow" form = "formLogin">
                                    <label style = "font-family: Roboto; font-weight: bold;"><i class = "glyphicon glyphicon-user" style = "font-size: 17.5px;"></i> <?php echo $nameUser; ?></label>
                                    <input type = "text" value="" name = "barcodeId" id = "barcodeId" class = "w3-input w3-border w3-pale-yellow" placeholder = "Enter ID Barcode" form = "formLogin" autocomplete='off' autofocus required>
                                </div>
                                <?php
                            }
                        echo "</div>";
                        echo "<div class='col-md-6 col-xs-12 col-sm-12'>";
                            echo "<div class='w3-right w3-padding-12'>";
                                echo "<ul class='list-inline'>";
                                    echo "<li>";
                                        $apiLogoPH = "<img style='width:70px; height:23px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logo.png' id='philLogo'>";
                                        $apiLogoJP = "<img style='width:70px; height:14px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logoJP.png' id='japanLogo'>";
                                        echo "&emsp;&emsp;<span style='padding:5px;' class='w3-round w3-light-grey'>";
                                            echo $apiLogoPH;
                                        echo "</span>&emsp;";
                                        echo "<span style='padding:5px;' class='w3-round w3-light-grey'>";
                                            echo $apiLogoJP;
                                        echo "</span>";
                                    echo "</li>";
                                    echo "<li>";
                                        echo "<button class='w3-tiny w3-btn w3-pink w3-round' onclick=\"softwareMemo();\"><i class='fa fa-comment w3-small'></i>&emsp;<b style='text-transform: uppercase;'>".displayText('L1305')."</b></button>&emsp;";
                                    echo "</li>";
                                    $imageId = "";
                                    if(in_array($_SESSION['idNumber'], Array("0500","0412","0346","0280","0276","0793")))
                                    {
                                        $imageId = "logOut";
                                        echo "<input type='hidden' id='loginUserNameData' value='".$_SESSION['userID']."'>";
                                        echo "<input type='hidden' id='loginPasswordData' value='".$_SESSION['password']."'>";
                                    }
                                    else
                                    {
                                        echo "<input type='hidden' id='loginUserNameData' value='".$_SESSION['userID']."'>";
                                        echo "<input type='hidden' id='loginPasswordData' value='".$_SESSION['password']."'>";
                                    }

                                    if($_SESSION['idNumber'] != '' AND $_SESSION['userType'] != 12)
                                    {
                                        echo "<li>";
                                            echo "<form id='languageForm' method='POST' action='".$_SERVER['PHP_SELF']."'></form>";
                                            echo "<img id='americanFlag' src = 'Common Data/Templates/images/americanFlag.gif' height = '35' width = '35' style='cursor:pointer;'>&emsp;";
                                        echo "</li>";
                                        echo "<li>";
                                            echo "<img id='japaneseFlag' src = 'Common Data/Templates/images/japaneseFlag.gif' height = '35' width = '35' style='cursor:pointer;'>&nbsp;";
                                            echo "<input type='hidden' id='languageFlag' name='languageFlag' value='1' form='languageForm'>";
                                        echo "</li>";
                                    }

                                    echo "<li>";
                                        $notificationView = "id='notification'";
                                        if($_SESSION['idNumber'] == "") $notificationView = "id='notificationView'";
                                        
                                        $onClickNotification = "";
                                        if($countNotification > 0)
                                        {
                                            $onClickNotification = "TINY.box.show ({url: '/".v."/Common Software/Notification Software/paul_viewNotification.php', width: 'auto' , height: 'auto', opacity: 10 ,topsplit: 6 , left:0,animate: false ,close:true, openjs:function(){customFunction()}})";
                                            // $onClickNotification = "TINY.box.show ({url: 'Common Software/Notification Software/paul_viewNotification.php', width: 'auto' , height: 'auto', opacity: 10 ,topsplit: 6 ,animate: false ,close:true})";                 
                                        }
                                        
                                        echo "<a ".$notificationView." title = 'Notification' onclick = \"".$onClickNotification."\">";
                                            if($countNotification > 0) echo "<span class='notify-badge'><b>".$countNotification."</b></span>";
                                            echo "<img src='/".v."/Common Data/Responsive Dashboard/image/noti.png' height = '35' width = '35' style='cursor:pointer;'>";
                                        echo "</a>"; 
                                        if($_GET['country'] == 1) echo "&emsp;&emsp;&emsp;";
                                    echo "</li>";
                                    if($_GET['country'] == 2)
                                    {
                                        echo "<li><img id='".$imageId."' title='".$fullName."' src='/".v."/11-A Employee List V2/profilepictures/".$picture."' class='w3-round-xxlarge' height = '35' width = '35' style='cursor:pointer;'></li>";
                                        echo "<li><span class='w3-text-white hidden-xs'><b>".$fullName."&nbsp;(".$departmentName1.")</b></span>&emsp;&emsp;</li>";
                                        // echo "<li><img id='signOut' title='Logout' src='Common Data/Responsive Dashboard/image/logout.png' class='w3-round-xxlarge' height = '35' width = '35' style='cursor:pointer;'></li>";
                                        echo "<li><img  title='Logout' onclick = \"location.href='index.php'\" src='Common Data/Responsive Dashboard/image/logout.png' class='w3-round-xxlarge' height = '35' width = '35' style='cursor:pointer;'></li>";
                                    }
                                echo "</ul>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        }
        else
        {
            ?>
            <link rel="stylesheet" href="/<?php echo v; ?>/Common Data/Templates/Bootstrap/w3css/w3.css">
            <link rel="stylesheet" href="/<?php echo v; ?>/Common Data/Templates/Bootstrap/Bootstrap 3.3.7/css/bootstrap.css">
            <link rel="stylesheet" href="/<?php echo v; ?>/Common Data/Templates/Bootstrap/Font Awesome/css/font-awesome.css">
            <link rel="stylesheet" href="/<?php echo v; ?>/Common Data/Templates/Bootstrap/Bootstrap 3.3.7/Roboto Font/roboto.css">
            <script src="/<?php echo v; ?>/Common Data/Libraries/Javascript/jQuery 3.1.1/jquery-3.1.1.js"></script>
            <script src="/<?php echo v; ?>/Common Data/Templates/Bootstrap/js/bootstrap.min.js"></script>
            <link href="/<?php echo v; ?>/Common Data/Templates/Bootstrap/js/bootstrap-toggle.min.css" rel="stylesheet">
            <script src="/<?php echo v; ?>/Common Data/Templates/Bootstrap/js/bootstrap-toggle.min.js"></script>
            <style>
                body
                {
                    font-size: 11px;
                    font-family: Roboto;
                    margin:0px;
                    padding:0px;
                    background-color:whitesmoke;
                }

                #containerIncharge {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                    /* min-height: 80vh; */
                    margin: 0 auto;
                    flex-wrap: wrap;
                    box-sizing: border-box;
                }

                .innerIncharge {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: flex-start;
                    /* max-width: 750px; */
                    margin: 0 auto;
                    flex-wrap: wrap;
                }
                
                .btn-real-dent {
                    /*周りの凹み*/
                    display: inline-block;
                    position: relative;
                    text-decoration: none;
                    color: rgba(3, 169, 244, 0.54);
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    text-align: center;
                    background: <?php echo $color1; ?>;
                    box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.08);
                }

                .btn-real-dent1x i {
                    /*ボタン自体*/
                    position: relative;
                    content: '';
                    width: 30px;
                    height: 30px;
                    line-height: 30px;
                    left: 0px;
                    top: 2px;
                    border-radius: 50%;
                    font-size: 24px;
                    background-image: linear-gradient(<?php echo $color2; ?> 0%, <?php echo $color3; ?> 100%);
                    text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.66);
                    box-shadow: inset 0 2px 0 rgba(255,255,255,0.5), 0 2px 2px rgba(0, 0, 0, 0.19);
                    border-bottom: solid 2px #b5b5b5;
                }

                .btn-real-dent .fa:active {
                    box-shadow: inset 0 1px 0 rgba(255,255,255,0.5), 0 2px 2px rgba(0, 0, 0, 0.19);
                    border-bottom: solid 2px #d8d8d8;
                }

                .btn-real-back i {
                    /*ボタン自体*/
                    position: relative;
                    content: '';
                    width: 30px;
                    height: 30px;
                    line-height: 30px;
                    left: 0px;
                    top: 2px;
                    border-radius: 50%;
                    font-size: 20px;
                    background-image: linear-gradient(<?php echo $color2; ?> 0%, <?php echo $color3; ?> 100%);
                    text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.66);
                    box-shadow: inset 0 2px 0 rgba(255,255,255,0.5), 0 2px 2px rgba(0, 0, 0, 0.19);
                    border-bottom: solid 2px #b5b5b5;
                }
            </style>
            <?php
            $apiLogoPH = "<img style='width:70px; height:23px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logo.png' id='philLogo'>";
            $apiLogoJP = "<img style='width:60px; height:14px; cursor:pointer;' src='/".v."/Common Data/Responsive Dashboard/image/logoJP.png' id='japanLogo'>";
            # displayText($displayId, ='utf8', $viewType=0, $placeholder=0, $characterCase=0, $textType='');
            $checkedON = ($_SESSION['switcher'] == 'on') ? 'checked' : '';
            echo "<div class='container-fluid'>";
                echo "<div class='row'>";
                    echo "<div class='w3-container w3-indigo w3-padding w3-card-2 hidden-sm hidden-xs'>";
                        echo "<div class='col-md-7 hidden-sm hidden-xs'>";
                            if($homeIcon == 0) echo "<span style='cursor: pointer;' class='btn-real-dent btn-real-dent1x homeBtnIcon'><i class='fa fa-home' style='color:".$iconColor.";'></i></span>";
                            // if($homeIcon == 0) echo "<span style='cursor: pointer;' onclick=\"window.location.href='/".v."/dashboard.php'\"><i class='fa fa-home w3-xlarge' ></i></span>";
                            if($previousLink != "")
                            {
                                if($displayId == "76" AND $_SESSION['idNumber'] == "0412")
                                {
                                    echo "&emsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back previousBtn'><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                }
                                else if($previousLink == "close")
                                {
                                    echo "&emsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back' onclick=\"window.close();\"><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                }
                                else
                                {
                                    echo "&emsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back' onclick=\"window.location.href='".$previousLink."'\"><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                }
                            }
                            if(in_array($_SESSION['idNumber'], Array('0001', '0280', '0276', '0346', '0412', '0735')))
                            {
                                /*echo "&emsp;&emsp;<span style='padding:5px;' class='w3-round w3-light-grey'>";
                                    echo $apiLogoPH;
                                echo "</span>&emsp;";
                                echo "<span style='padding:5px;' class='w3-round w3-light-grey'>";
                                    echo $apiLogoJP;
                                echo "</span>";*/
                            }
                            echo "<div class='w3-right'>";
                                echo "<label class='w3-xlarge' style='padding-top:4px;'><b style='text-transform: uppercase;'>".(displayText($displayId, 'utf8', 0, 2, 1))."</b>&nbsp;<b>".$version."</b></label>";
                            echo "</div>";
                        echo "</div>";
                        echo "<div class='col-md-5 hidden-sm hidden-xs'>";
                            echo "<div class='w3-right'>";
                                echo "<ul class='list-inline'>";
                                    echo "<li>";
                                        echo $inchargeData;
                                    echo "</li>";
                                    echo "<li>";
                                        echo "&emsp;";
                                    echo "</li>";
                                    echo "<li>";
                                        echo "<img class='languageFlag' data-value = '1' id='americanFlag' src = '/".v."/Common Data/Templates/images/americanFlag.gif' height = '32' width = '32' style='cursor:pointer;'>&emsp;&nbsp;";
                                        echo "<img class='languageFlag' data-value = '2' id='japaneseFlag' src = '/".v."/Common Data/Templates/images/japaneseFlag.gif' height = '32' width = '32' style='cursor:pointer;'>";
                                    echo "</li>";
                                    echo "<li>";
                                        echo "&nbsp;<button class='w3-tiny w3-btn w3-pink w3-round' onclick=\"softwareComment('".$displayId."');\"><i class='fa fa-comment w3-small'></i>&emsp;<b style='text-transform: uppercase;'>".displayText('L1305')."</b></button>";
                                    echo "</li>";
                                    echo "<li>";
                                        echo "&nbsp;<button class='w3-tiny w3-btn w3-green w3-round' onclick=\"softwareHelp('".$displayId."');\"><i class='fa fa-question-circle-o w3-small'></i>&emsp;<b style='text-transform: uppercase;'>".displayText('L3586')."</b></button>";
                                    echo "</li>";
                                    if($_SESSION['userType'] == 0)
                                    {
                                        echo "<li>";
                                            echo "&emsp;<label style='text-transform: uppercase;'>".(displayText('L243'))." : </label>&nbsp;<input onchange=\"toggleSwitch(this.value);\" type='checkbox' class='switcher' id='switcher' name='switcher' ".$checkedON." data-onstyle='danger' data-toggle='toggle' data-size='mini' value='on'>";
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                    echo "<div class='w3-container w3-padding w3-indigo w3-card-2 hidden-md hidden-lg'>";
                        echo "<div class='row'>";
                            echo "<div class='col-xs-2 col-sm-2'>";
                                if($homeIcon == 0) echo "<span style='cursor: pointer;' class='btn-real-dent btn-real-dent1x homeBtnIcon'><i class='fa fa-home w3-text-black'></i></span>";
                                // if($homeIcon == 0) echo "<span style='cursor: pointer;' onclick=\"window.location.href='/".v."/dashboard.php'\"><i class='fa fa-home w3-xlarge' ></i></span>";
                                if($previousLink != "")
                                {
                                    if($displayId == "76" AND $_SESSION['idNumber'] == "0412")
                                    {
                                        echo "&nbsp;&nbsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back previousBtn'><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                    }
                                    else if($previousLink == "close")
                                    {
                                        echo "&nbsp;&nbsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back' onclick=\"window.close();\"><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                    }
                                    else
                                    {
                                        echo "&nbsp;&nbsp;<span style='cursor: pointer;' class='btn-real-dent btn-real-back' onclick=\"window.location.href='".$previousLink."'\"><i class='fa fa-backward' style='color:".$iconColor.";'></i></span>";
                                    }
                                }
                            echo "</div>";
                            echo "<div class='col-xs-8 col-sm-8 w3-center'>";
                                echo "<label class='w3-medium' style='padding-top:6px;'><b style='text-transform: uppercase;'>".(displayText($displayId, 'utf8', 0, 2, 1))."</b>&nbsp;<b>".$version."</b></label>";
                            echo "</div>";
                            echo "<div class='col-xs-2 col-sm-2'>";
                                echo "<div class='w3-dropdown-hover w3-right'>";
                                    echo "<i class='fa fa-list w3-xlarge' style='padding-top:5px;'></i>";
                                    echo "<div class='w3-dropdown-content w3-padding w3-bar-block w3-border' style='right:0; z-index:99999;'>";
                                        echo "<div class='w3-center'>";
                                            echo "<img class='languageFlag' data-value = '1' id='americanFlag' src = '/".v."/Common Data/Templates/images/americanFlag.gif' height = '32' width = '32' style='cursor:pointer;'>&emsp;&nbsp;";
                                            echo "<img class='languageFlag' data-value = '2' id='japaneseFlag' src = '/".v."/Common Data/Templates/images/japaneseFlag.gif' height = '32' width = '32' style='cursor:pointer;'>";
                                        echo "</div>";
                                        echo "<div class='w3-padding-top'></div>";
                                        echo "<button class='w3-tiny w3-btn w3-pink w3-round w3-btn-block' onclick=\"softwareComment('".$displayId."');\"><i class='fa fa-comment w3-small'></i>&emsp;<b style='text-transform: uppercase;'>".displayText('L1305')."</b></button>";
                                        echo "<div class='w3-padding-top'></div>";
                                        echo "<button class='w3-tiny w3-btn w3-green w3-round w3-btn-block' onclick=\"softwareHelp('".$displayId."');\"><i class='fa fa-question-circle-o w3-small'></i>&emsp;<b style='text-transform: uppercase;'>".displayText('L3586')."</b></button>";
                                        if($_SESSION['userType'] == 0)
                                        {
                                            echo "<div class='w3-padding-top'></div>";
                                            echo "<div class='w3-center'>";
                                                echo "<label style='text-transform: uppercase;'>".(displayText('L243'))." : </label>&nbsp;<input onchange=\"toggleSwitch2(this.value);\" type='checkbox' class='switcher' id='switcher2' name='switcher' ".$checkedON." data-onstyle='danger' data-toggle='toggle' data-size='mini' value='on'>";
                                            echo "</div>";
                                        }
                                    echo "</div>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div id='modal-izi-comment'><span class='izimodal-content-comment'></span></div>";
                echo "<div id='modal-izi-help'><span class='izimodal-content-help'></span></div>";
            echo "</div>";
        }
            ?>
        <link rel="stylesheet" href="/<?php echo v; ?>/Common Data/Libraries/Javascript/iziModal-master/css/iziModal.css" />
        <script src="/<?php echo v; ?>/Common Data/Libraries/Javascript/iziModal-master/js/iziModal.js"></script>
        <script src="/<?php echo v; ?>/Common Data/Libraries/Javascript/Interact JS/interact.js"></script>
        <script src="/<?php echo v; ?>/Common Data/Libraries/Javascript/Custom JS/accounting.js"></script>
        <script>
        function dragMoveListener (event) {
            var target = event.target
            // keep the dragged position in the data-x/data-y attributes
            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

            // translate the element
            target.style.webkitTransform =
                target.style.transform =
                'translate(' + x + 'px, ' + y + 'px)'

            // update the posiion attributes
            target.setAttribute('data-x', x)
            target.setAttribute('data-y', y)
        }

        function toggleSwitch(val)
        {
            if($("#switcher").is(":checked"))
            {
                var typeData = 2;
            }
            else
            {
                var typeData = 3;
            }

            $.ajax({
                url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_changeLanguage.php',
                type    : 'POST',
                data    : {
                            type   : typeData
                },
                success : function(data){
                            location.reload();
                }
            });
        }

        function toggleSwitch2(val)
        {
            if($("#switcher2").is(":checked"))
            {
                var typeData = 2;
            }
            else
            {
                var typeData = 3;
            }

            $.ajax({
                url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_changeLanguage.php',
                type    : 'POST',
                data    : {
                            type   : typeData
                },
                success : function(data){
                            location.reload();
                }
            });
        }
        
        function softwareHelp(displayId)
        {
            var session = "<?php echo $_SESSION['idNumber']; ?>";
            if(session != "")
            {
                var mapForm = document.createElement("form");
                mapForm.target = "Map";
                mapForm.method = "POST"; // or "post" if appropriate
                mapForm.action = "/<?php echo v; ?>/Common Data/PHP Modules/raymond_softwareHelp.php";

                var mapSoftwareId = document.createElement("input");
                mapSoftwareId.type = "hidden";
                mapSoftwareId.name = "softwareId";
                mapSoftwareId.value = displayId;
                mapForm.appendChild(mapSoftwareId);

                document.body.appendChild(mapForm);

                var w = 700;
                var h = 600;

                var left = (screen.width/2)-(w/2);
                var top = (screen.height/2)-(h/2);
                map = window.open("", "Map", ', top='+top+', left='+left+',width='+w+',height='+h);

                if (map) 
                {
                    mapForm.submit();
                }
                else 
                {
                    alert('You must allow popups for this map to work.');
                }
            }
            else
            {
                $("#modal-izi-help").iziModal({
                    title                   : '<i class="fa fa-question-circle-o"></i> <b style="text-transform: uppercase;"><?php echo displayText("L3586", "utf8", 0, 1, 1); ?></b>',
                    headerColor             : '#1F4788',
                    subtitle                : '<b><?php echo strtoupper(date('F d, Y'));?></b>',
                    width                   : 800,
                    fullscreen              : false,
                    transitionIn            : 'comingIn',
                    transitionOut           : 'comingOut',
                    padding                 : 20,
                    radius                  : 0,
                    top                     : 10,
                    restoreDefaultContent   : true,
                    closeOnEscape           : true,
                    closeButton             : true,
                    overlayClose            : false,
                    onOpening               : function(modal){
                                                modal.startLoading();
                                                $.post("/<?php echo v; ?>/Common Data/PHP Modules/raymond_softwareHelp.php", {
                                                    softwareId     : displayId
                                                }, function( data ) {
                                                    $( ".izimodal-content-help" ).html(data);
                                                    modal.stopLoading();
                                                });
                                            },
                        onClosed            : function(modal){
                                                $("#modal-izi-help").iziModal("destroy");
                                }
                });

                $("#modal-izi-help").iziModal("open");
            }
        }

        function softwareComment(displayId)
        {
            var session = "<?php echo $_SESSION['idNumber']; ?>";

            var linkData = "raymond_softwareMemo.php";
            if(session == '0412' || session == '0280' || session == '0001') var linkData = "raymond_softwareMemo.php";
            $("#modal-izi-comment").iziModal({
                title                   : '<i class="fa fa-comment"></i> <b style="text-transform: uppercase;"><?php echo displayText("L4132", "utf8", 0, 1, 1); ?></b>',
                headerColor             : '#1F4788',
                subtitle                : '<b><?php echo strtoupper(date('F d, Y'));?></b>',
                width                   : 1400,
                fullscreen              : false,
                transitionIn            : 'comingIn',
                transitionOut           : 'comingOut',
                padding                 : 20,
                radius                  : 0,
                top                     : 10,
                restoreDefaultContent   : true,
                closeOnEscape           : true,
                closeButton             : true,
                overlayClose            : false,
                onOpening               : function(modal){
                                            modal.startLoading();
                                            $.get("/<?php echo v; ?>/27 User Interface Parameter List V2/"+linkData, {
                                                    softwareId     : displayId
                                            }, function( data ) {
                                                $( ".izimodal-content-comment" ).html(data);
                                                modal.stopLoading();
                                            });
                                        },
                onClosed                : function(modal){
                                            $("#modal-izi-comment").iziModal("destroy");
                                        }
            });

            $("#modal-izi-comment").iziModal("open");
        }


        var idleTime = 0;
        $(document).ready(function(){
            // $(document).click(function(event) {
            //     var text = $(event.target).text();
            //     console.log(text);
            // });

            var timers = 1320000;
            // var idleInterval = setInterval(timerIncrement, 1320000); // 22 mins
            // var idleInterval = setInterval(timerIncrement, 960000); // 22 mins
            var idleInterval = setInterval(timerIncrement, timers); // 22 mins
            
            $(this).mousemove(function (e) {
                resetSession();
                idleTime = 0;
                clearInterval(idleInterval);
                idleInterval = setInterval(timerIncrement, timers); 
            });

            $(this).keypress(function (e) {
                idleTime = 0;
                resetSession();
                clearInterval(idleInterval);
                idleInterval = setInterval(timerIncrement, timers); 
            });

            $(this).click(function(e) {
                idleTime = 0;
                // console.log("clicked");
                resetSession();
                clearInterval(idleInterval);
                idleInterval = setInterval(timerIncrement, timers); 
            });

            var displayId = "<?php echo $displayId; ?>";
            if(displayId == "76")
            {
                $(".previousBtn").click(function(){
                    var content = $("#iframeValue").contents().find("#postCount");
                    var previousLink = "<?php echo $previousLink; ?>";
                    if (content.val() > 0)
                    {
                        window.location.reload();
                    }
                    else
                    {
                        window.location.href = previousLink;
                    }
                });
            }

            interact('#modal-izi-help').draggable({
                inertia: false,
                // restriction: "#editorSpace",
                restrict: {
                    restriction: "",
                    endOnly: true,
                    elementRect: { top: 1, left: 1, bottom: 1, right: 1 }
                },	
                autoScroll: true,
                onmove: dragMoveListener,
                onend: function (event) {
                        
                }
            });

            $(".languageFlag").click(function(){
                var flagValue = $(this).attr('data-value');

                $.ajax({
                    url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_changeLanguage.php',
                    type    : 'POST',
                    data    : {
                                type 		: 1,
                                flagValue   : flagValue
                    },
                    success : function(data){
                                // location.href = '';
                                window.location.reload();
                    }
                });
            });

            var country = "<?php echo $_GET['country']; ?>";
            
            if(country == 1) var logoId = "japanLogo";
            if(country == 2) var logoId = "philLogo";

            $("#"+logoId).click(function(){
                var idNumber = "<?php echo $_SESSION['idNumber']; ?>";
                var displayId = "<?php echo $displayId; ?>";

                var datalink = location.href;
                var link = "";
                if(datalink.indexOf("php?") !== -1)
                {
                    var currentLink = datalink.split(".php?");
                    var getData = "";
                    if(currentLink[1] != "")
                    {
                        var newData = currentLink[1].split("&");
                        var getData = newData.join(",");
                        var link = "&link="+getData;
                    }
                }

                if(country == 1)
                {
                    location.href='http://180.16.208.152/<?php echo v; ?>/Common Data/PHP Modules/raymond_redirectSoftware.php?idNumber='+idNumber+'&displayId='+displayId+link;
                }
                else if(country == 2)
                {
                    location.href='http://203.177.14.250/<?php echo v; ?>/Common Data/PHP Modules/raymond_redirectSoftware.php?idNumber='+idNumber+'&displayId='+displayId+link;
                }
            });

            $(".homeBtnIcon").click(function(){
                var idNumber = "<?php echo $_SESSION['idNumber']; ?>";
                var displayId = "<?php echo $displayId; ?>";
                $.ajax({
                    url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_templatefunctions.php',
                    type    : 'POST',
                    data    : {
                                accessType 		: 'accessLog',
                                displayId       : displayId
                    },
                    success : function(data){
                            location.href = '/<?php echo v; ?>/dashboard.php';
                    }
                });;
            });
        });

        function timerIncrement() {
            idleTime = idleTime + 1;
            if (idleTime > 0) {

                $.ajax({
                    url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_sessionCheck.php?type=ajax',
                    type    : 'POST',
                    data    : {
                                reaload : 1
                    },
                    success : function(data){
                                if(data != "")
                                {
                                    console.log(data);
                                    window.location.reload();
                                }
                                else
                                {
                                    // alert("You are now logged out.");
                                    location.href = '/<?php echo v; ?>/raymond_accountsv2.php';
                                }
                    }
                });
            }
        }

        function resetSession(){
            $.ajax({
                url     : '/<?php echo v; ?>/Common Data/PHP Modules/raymond_sessionCheck.php?type=resetcounter',
                type    : 'POST',
                data    : {
                            reaload : 1
                },
                success : function(data){
                            console.log(data);
                }
            });
        }
        </script>
        <?php
    }
}

function getShiftTime($idNumber, $shiftDate, $returnType=0)
{
    include('Templates/mysqliConnection.php');

    $sql = "SELECT employeeId FROM hr_employee WHERE idNumber = '".$idNumber."'";
    $queryEmployee = $db->query($sql);
    if($queryEmployee AND $queryEmployee->num_rows > 0)
    {
        $resultEmployee = $queryEmployee->fetch_assoc();
        $employeeId = $resultEmployee['employeeId'];

        $sql = "SELECT shiftId FROM hr_shiftcalendar WHERE shiftDate = '".$shiftDate."' AND employeeId = ".$employeeId;
        $queryShiftId = $db->query($sql);
        if($queryShiftId AND $queryShiftId->num_rows > 0)
        {
            $resultShiftId = $queryShiftId->fetch_assoc();
            $shiftId = $resultShiftId['shiftId'];

            $sql = "SELECT shiftIn, shiftOut, shiftColor FROM hr_shift WHERE shiftId = ".$shiftId;
            $queryShifts = $db->query($sql);
            if($queryShifts AND $queryShifts->num_rows > 0)
            {
                $resultShifts = $queryShifts->fetch_assoc();
                $shiftIn = $resultShifts['shiftIn'];
                $shiftOut = $resultShifts['shiftOut'];
                $shiftColor = $resultShifts['shiftColor'];

                if($returnType == 0) return $shiftIn;
                if($returnType == 1) return $shiftOut;
                if($returnType == 2) return Array($shiftIn, $shiftOut, $shiftColor);
            }
            else
            {
                return "NO SHIFT";
            }
        }
        else
        {
            return "NO SHIFT";
        }
    }
    else
    {
        return "There was an error on your parameter input.";
    }
}

function getDTR($idNumber, $dtrDate, $returnType=0)
{
    include('Templates/mysqliConnection.php');

    $sql = "SELECT timeIn FROM hr_dtr WHERE employeeId = '".$idNumber."' AND timeIn LIKE '".$dtrDate."%' ORDER BY dtrId ASC LIMIT 1";
    $queryDTR = $db->query($sql);
    if($queryDTR AND $queryDTR->num_rows > 0)
    {
        $resultDTR = $queryDTR->fetch_assoc();
        $timeIn = $resultDTR['timeIn'];
        $timeInDTR = explode(" ",$timeIn);
        $timeInData = date("H:i:s A",strtotime($timeInDTR[1]));

        $sql = "SELECT timeOut FROM hr_dtr WHERE employeeId = '".$idNumber."' AND timeIn LIKE '".$dtrDate."%' ORDER BY dtrId DESC LIMIT 1";
        $queryDTROut = $db->query($sql);
        if($queryDTROut AND $queryDTROut->num_rows > 0)
        {
            $resultDTROut = $queryDTROut->fetch_assoc();
            $timeOut = $resultDTROut['timeOut'];
            $timeOutDTR = explode(" ",$timeOut);
            $timeOutData = date("H:i:s A",strtotime($timeOutDTR[1]));
            if($timeOut == "0000-00-00 00:00:00") $timeOutData = "N/A";
        }

        if($returnType == 0) return $timeIn;
        if($returnType == 1) return $timeOut;
        if($returnType == 2) return $timeInData;
        if($returnType == 3) return $timeOutData;
        if($returnType == 4) return Array ($timeIn, $timeOut);
        if($returnType == 5) return Array ($timeInData, $timeOutData);
    }
    else
    {
        return "NO TIME IN";
    }
}

function getWTR($idNumber, $wtrDate, $returnType=0)
{
    include('Templates/mysqliConnection.php');

    $sql = "SELECT timeIn FROM hr_wtr WHERE employeeId = '".$idNumber."' AND timeIn LIKE '".$wtrDate."%' ORDER BY dtrId ASC LIMIT 1";
    $queryWTR = $db->query($sql);
    if($queryWTR AND $queryWTR->num_rows > 0)
    {
        $resultWTR = $queryWTR->fetch_assoc();
        $timeIn = $resultWTR['timeIn'];
        $timeInWTR = explode(" ",$timeIn);
        $timeInData = date("H:i:s A",strtotime($timeInWTR[1]));

        $sql = "SELECT timeOut FROM hr_wtr WHERE employeeId = '".$idNumber."' AND timeIn LIKE '".$wtrDate."%' ORDER BY dtrId DESC LIMIT 1";
        $queryWTROut = $db->query($sql);
        if($queryWTROut AND $queryWTROut->num_rows > 0)
        {
            $resultDTROut = $queryWTROut->fetch_assoc();
            $timeOut = $resultDTROut['timeOut'];
            $timeOutWTR = explode(" ",$timeOut);
            $timeOutData = date("H:i:s A",strtotime($timeOutWTR[1]));
            if($timeOut == "0000-00-00 00:00:00") $timeOutData = "ONGOING";
        }

        if($returnType == 0) return $timeIn;
        if($returnType == 1) return $timeOut;
        if($returnType == 2) return $timeInData;
        if($returnType == 3) return $timeOutData;
        if($returnType == 4) return Array ($timeIn, $timeOut);
        if($returnType == 5) return Array ($timeInData, $timeOutData);
    }
    else
    {
        return "NO LOG IN";
    }
}

function getEmployeeScheduleST($idNumber, $dateSchedule, $sectionId, $returnType=0)
{
    include('Templates/mysqliConnection.php');

    $standardTime = $standardTimeFinish = $standardTimeUnfinish = 0;
    $sql = "SELECT workScheduleId FROM system_machineWorkschedule WHERE idNumber = '".$idNumber."' AND inputDate = '".$dateSchedule."' AND sectionId = ".$sectionId;
    $queryWorkSched = $db->query($sql);
    if($queryWorkSched AND $queryWorkSched->num_rows > 0)
    {
        while($resultWorkSched = $queryWorkSched->fetch_assoc())
        {
            $workScheduleId = $resultWorkSched['workScheduleId'];

            $sql = "SELECT lotNumber, processSection, processCode, status FROM ppic_workschedule WHERE id = ".$workScheduleId;
            $querySchedule = $db->query($sql);
            if($querySchedule AND $querySchedule->num_rows > 0)
            {
                $resultSchedule = $querySchedule->fetch_assoc();
                $lotNumber = $resultSchedule['lotNumber'];
                $processCode = $resultSchedule['processCode'];
                $processSection = $resultSchedule['processSection'];
                $status = $resultSchedule['status'];

                $sql = "SELECT partId, workingQuantity FROM ppic_lotlist WHERE lotNumber = '".$lotNumber."'";
                $queryLotList = $db->query($sql);
                if($queryLotList AND $queryLotList->num_rows > 0)
                {
                    $resultLotList = $queryLotList->fetch_assoc();
                    $partId = $resultLotList['partId'];
                    $workingQuantity = $resultLotList['workingQuantity'];

                    $standardTime = getStandardTime($partId,$processCode,$workingQuantity,$processSection,$lotNumber);

                    if($status == 1) $standardTimeFinish += $standardTime;
                    if($status == 0) $standardTimeUnfinish += $standardTime;
                }
            }
        }
    }

    $totalST = $standardTimeFinish + $standardTimeUnfinish;
    
    if($returnType == 0) return $totalST;
    if($returnType == 1) return $standardTimeFinish;
    if($returnType == 2) return $standardTimeUnfinish;
    if($returnType == 3) return Array ($totalST, $standardTimeFinish, $standardTimeUnfinish);
}

function getEmployee($id, $languageFlag=0, $returnType=0)
{
    include('Templates/mysqliConnection.php');
    if($id == "*")
    {
        $idNumberArray = $fullNameArray = $employeeIdArray = Array();
        $sql = "SELECT * FROM hr_employee WHERE status = 1 ORDER BY surName";
        $queryEmployee = $db->query($sql);
        if($queryEmployee AND $queryEmployee->num_rows > 0)
        {
            while($resultEmployee = $queryEmployee->fetch_assoc())
            {
                $employeeId = $resultEmployee['employeeId'];
                $idNumber = $resultEmployee['idNumber'];
                $randomId = $resultEmployee['randomId'];
                $firstName = trim($resultEmployee['firstName']);
                $surName = trim($resultEmployee['surName']);
                $firstNameJP = trim($resultEmployee['firstNameJP']);
                $surNameJP = trim($resultEmployee['surNameJP']);

                if($languageFlag == 0) $fullName = $surName." ".$firstName;
                if($languageFlag == 1) $fullName = $firstNameJP." ".$surNameJP;

                $idNumberArray[] = $idNumber;
                $fullNameArray[$idNumber] = $fullName;
                $employeeIdArray[$idNumber] = $employeeId;
            }

            return Array($idNumberArray, $fullNameArray, $employeeIdArray);
        }
    }
    else
    {
        $sql = "SELECT * FROM hr_employee WHERE (employeeId = '".$id."' OR idNumber = '".$id."' OR randomId = '".$id."') AND status = 1";
        $queryEmployee = $db->query($sql);
        if($queryEmployee AND $queryEmployee->num_rows > 0)
        {
            $resultEmployee = $queryEmployee->fetch_assoc();
            $employeeId = $resultEmployee['employeeId'];
            $idNumber = $resultEmployee['idNumber'];
            $randomId = $resultEmployee['randomId'];
            $firstName = trim($resultEmployee['firstName']);
            $surName = trim($resultEmployee['surName']);
            $firstNameJP = trim($resultEmployee['firstNameJP']);
            $surNameJP = trim($resultEmployee['surNameJP']);

            if($languageFlag == 0) $fullName = $firstName." ".$surName;
            if($languageFlag == 1) $fullName = $firstNameJP." ".$surNameJP;

            return $fullName;
        }
    }
}

function finishNotification($notificationKey = "", $notificationId = 0)
{
    include('Templates/mysqliConnection.php');

    if($notificationKey != "" AND $notificationId == 0)
    {
        $sql = "SELECT notificationId FROM system_notificationdetails WHERE notificationKey = '".$notificationKey."'";
        $queryDetails = $db->query($sql);
        if($queryDetails AND $queryDetails->num_rows > 0)
        {
            $resultDetails = $queryDetails->fetch_assoc();
            $notificationId = $resultDetails['notificationId'];

            $sql = "UPDATE system_notification SET notificationStatus = 1 WHERE notificationStatus = 0 AND notificationId = ".$notificationId;
            $queryUpdate = $db->query($sql);
        }
    }
    else
    {
        $sql = "UPDATE system_notification SET notificationStatus = 1 WHERE notificationStatus = 0 AND notificationId = ".$notificationId;
        $queryUpdate = $db->query($sql);
    }
}

function setNotification($notificationKey, $notificationType, $notificationDetails, $notificationLink, $targetArray = Array(), $targetType)
{
    include('Templates/mysqliConnection.php');
    
    if($targetArray != NULL)
    {
        $sql = "INSERT INTO `system_notificationdetails`(
                                                            `notificationDetail`, 
                                                            `notificationKey`, 
                                                            `notificationLink`, 
                                                            `notificationType`
                                                        ) 
                                                VALUES (
                                                            '".$notificationDetails."',
                                                            '".$notificationKey."',
                                                            '".$notificationLink."',
                                                            30
                                                        ) ";
        $queryInsert = $db->query($sql);

        $sql = "SELECT notificationId FROM system_notificationdetails WHERE notificationKey = '".$notificationKey."' AND notificationType = ".$notificationType." ORDER BY notificationId DESC LIMIT 1";
        $queryId = $db->query($sql);
        if($queryId AND $queryId->num_rows > 0)
        {
            $resultId = $queryId->fetch_assoc();
            $notificationId = $resultId['notificationId'];

            foreach ($targetArray as $key ) 
            {
                $sql = "INSERT INTO `system_notification`(
                                                            `notificationId`, 
                                                            `notificationTarget`, 
                                                            `targetType`
                                                        ) 
                                                    VALUES (
                                                            '".$notificationId."',
                                                            '".$key."',
                                                            '".$targetType."'
                                                    )";
                $queryInsert = $db->query($sql);
            }
        }
    }
    else
    {
        return "ERROR!!";
    }
}

class PMSDatabase
{
    private $sqlQuery;
    private $DBServer;
    private $DBUser;
    private $DBPass; 
    private $DBName; 
    private $db; 

    private $tableName; 
    private $fields; 
    private $values; 

    public function __construct()
    {
        if($_GET['country'] == 1)
        {
            $this->DBServer = "localhost";
            $this->DBUser = "root";
            $this->DBPass = "arktechdb";
            $this->DBName = "arktechdatabase";
            $this->dbConnect();
        }
        else
        {
            $this->DBServer = "localhost";
            $this->DBUser = "it";
            $this->DBPass = "";
            $this->DBName = "arktechdatabase";
            $this->dbConnect();
        }
    }

    private function dbConnect()
    {
        $this->db = new mysqli($this->DBServer, $this->DBUser, $this->DBPass, $this->DBName);
        $this->db->set_charset("utf8");
        if ($this->db->connect_error)
        {
            trigger_error('Database connection failed: '  . $this->db->connect_error, E_USER_ERROR);
        }
    }

    public function setTableName($table)
    {
        $this->tableName = $table;
    }

    public function setFieldsValues($field, $value)
    {
        $this->fields[] = $field;
        $this->values[] = $this->db->real_escape_string($value);
    }

    public function insert()
    {
        $sqlDataQuery = $this->sqlQuery;
        
        if($sqlDataQuery != "")
        {
            $sql = "INSERT INTO ".$this->tableName." (".implode(", ",$this->fields).") VALUES ({$sqlDataQuery})";
            $queryInsert = $this->db->query($sql);
            if(!$queryInsert)
            {
                return $this->db->error;
            }
        }
        else
        {
            $sql = "INSERT INTO ".$this->tableName." (".implode(", ",$this->fields).") VALUES ('".implode("', '",$this->values)."')";
            $queryInsertAgain = $this->db->query($sql);
            if(!$queryInsertAgain)
            {
                return $this->db->error;
            }
        }

        $this->sqlQuery = "";
        $this->fields = Array ();
        $this->values = Array ();
    }

    public function update($whereQuery)
    {
        $x = 0;
        $valuesArray = Array();
        foreach ($this->fields AS $key) 
        {
            $valuesArray[] = $key."='".$this->values[$x]."'";
        }

        $sql = "UPDATE ".$tableName." SET ".implode(", ", $valuesArray)." WHERE ".$whereQuery;
        $queryUpdate = $this->db->query($sql);
        if(!$queryUpdate)
        {
            return $this->db->error;
        }
        else
        {
            return $this->db->affected_rows;
        }

        $this->fields = Array ();
        $this->values = Array ();
    }

    public function insertRecords($tableName, $fieldsArray = Array(), $valuesArray = Array())
    {
        $sqlDataQuery = $this->sqlQuery;

        if($sqlDataQuery != "")
        {
            $sql = "INSERT INTO ".$tableName." (".implode(", ",$fieldsArray).") VALUES ({$sqlDataQuery})";
            $queryInsert = $this->db->query($sql);
            if(!$queryInsert)
            {
                return $this->db->error;
            }
        }
        else
        {
            if($this->fields != NULL)
            {

            }
            else
            {
                $sql = "INSERT INTO ".$tableName." (".implode(", ",$fieldsArray).") VALUES (".implode(", ",$valuesArray).")";
                $queryInsert = $this->db->query($sql);
                if(!$queryInsert)
                {
                    $sql = "INSERT INTO ".$tableName." (".implode(", ",$fieldsArray).") VALUES ('".implode("', '",$valuesArray)."')";
                    $queryInsertAgain = $this->db->query($sql);
                    if(!$queryInsertAgain)
                    {
                        return $this->db->error;
                    }
                }
            }
        }

        $this->sqlQuery = "";
        $this->fields = Array ();
        $this->values = Array ();
    }

    public function updateRecords($tableName, $valuesArray = Array(), $whereQuery)
    {
        $sql = "UPDATE ".$tableName." SET ".implode(", ", $valuesArray)." WHERE ".$whereQuery;
        $queryUpdate = $this->db->query($sql);
        if(!$queryUpdate)
        {
            return $this->db->error;
        }
        else
        {
            return $this->db->affected_rows;
        }
    }

    public function deleteRecords($tableName, $whereQuery)
    {
        $sql = "DELETE FROM ".$tableName." WHERE ".$whereQuery;
        $queryDelete = $this->db->query($sql);
        if(!$queryDelete)
        {
            return $this->db->error;
        }
        else
        {
            return $this->db->affected_rows;
        }
    }
    
    public function getLastRecords($tableName, $keyValue, $whereQuery)
    {
        $id = "ERROR FOUND ON YOUR QUERY";
        $sql = "SELECT ".$keyValue." FROM ".$tableName." ".$whereQuery;
        $queryLastId = $this->db->query($sql);
        if(!$queryLastId)
        {
            return $this->db->error;
        }
        else
        {
            if($queryLastId AND $queryLastId->num_rows > 0)
            {
                $resultLastId = $queryLastId->fetch_assoc();
                $id = $resultLastId[$keyValue];
            }

            return $id;
        }
    }

    public function getRecords()
    {
        $results = Array ();
        $sql = $this->sqlQuery;
        $queryRecords = $this->db->query($sql);
        if($queryRecords AND $queryRecords->num_rows > 0)
        {
            while($resultRecords = $queryRecords->fetch_assoc())
            {
                $results[] = $resultRecords;
            }
        }

        $this->sqlQuery = "";
        return $results;
    }

    public function setSQLQuery($sqlData)
    {
        $this->sqlQuery = $sqlData;
    }
}

class PMSTemplates extends PMSDatabase
{
    private $attributes;
    private $selectOptions;
    private $elem;
    private $icon;
    private $dataValue;
    private $newClass;
    private $newCss;
    private $listName;
    private $rows;
    private $language = 1;

    public function includeHeader($title="")
    {
        $ver = v;
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title><?php echo $title; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Templates/Bootstrap/w3css/w3.css">
            <link rel="stylesheet" type="text/css" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Super Quick Table/datatables.min.css">
            <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Templates/Bootstrap/Bootstrap 3.3.7/css/bootstrap.css">
            <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Templates/Bootstrap/Font Awesome/css/font-awesome.css">
            <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Templates/Bootstrap/Bootstrap 3.3.7/Roboto Font/roboto.css">
            <script type="text/javascript" src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Tiny Box/tinybox.js"></script>
            <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Tiny Box/stylebox.css" />
            <style>
                body
                {
                    font-size: 12px !important;
                    font-family: Roboto;
                    background-color:whitesmoke;
                }

                .dataTables_wrapper .dataTables_filter {
                    position: absolute;
                    text-align: right;
                    visibility: hidden;
                }
            </style>
        </head>
        <body>
        <?php
    }

    public function includeFooter()
    {
        $ver = v;
        ?>
        </body>
        </html>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jQuery 3.1.1/jquery-3.1.1.js"></script>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jQuery 3.1.1/jquery-ui.js"></script>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jQuery 3.1.1/bootstrap.min.js"></script>
        <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/sweetAlert2/dist/sweetalert2.min.css">
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/sweetAlert2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jquery-date-range-picker-master/dist/daterangepicker.min.css">
        <script type="text/javascript" src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jquery-date-range-picker-master/moment.min.js"></script>
        <script type="text/javascript" src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jquery-date-range-picker-master/dist/jquery.daterangepicker.min.js"></script>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Super Quick Table/datatables.min.js"></script>
        <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Bootstrap Multi-Select JS/dist/css/bootstrap-multiselect.css" type="text/css" media="all" />
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Bootstrap Multi-Select JS/dist/js/bootstrap-multiselect.js"></script>
        <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/iziModal-master/css/iziModal.css" />
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/iziModal-master/js/iziModal.js"></script>
        <link rel="stylesheet" href="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/iziToast-master/dist/css/iziToast.css" />
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/iziToast-master/dist/js/iziToast.js"></script>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/Interact JS/js/interact.js"></script>
        <script src="/<?php echo $ver; ?>/Common Data/Libraries/Javascript/jQuery Balloon/jquery.balloon.js"></script>
        <?php
    }

    public function setLanguage($lang = 1)
    {
        $this->language = $lang;
    }

    public function setAttribute($attrib, $values)
    {
        $this->attributes[] = $attrib."=\"{$values}\"";
    }

    public function setOptions($value, $data, $selected="")
    {
        $this->selectOptions[] = "<option {$selected} value='{$value}'>{$data}</option>";
    }

    public function setIcon($iconValue="")
    {
        $this->icon = $iconValue;
    }

    public function setDataValue($value)
    {
        $this->dataValue = $value;
    }

    public function setDDName($name)
    {
        $this->listName = $name;
    }

    protected function getText($value="")
    {
        $textValue = $value;
        $sql = "SELECT displayTextOne, displayTextTwo FROM system_software WHERE displayId LIKE '".$value."' ORDER BY displayId DESC LIMIT 1";
        PARENT::setSQLQuery($sql);
        $resultData = PARENT::getRecords();
        if($resultData != NULL)
        {
            $displayTextOne = $resultData[0]['displayTextOne'];
            $displayTextTwo = $resultData[0]['displayTextTwo'];

            $lang = 1;
            if(isset($_SESSION['idNumber']))
            {
                $sql = "SELECT * FROM hr_employee WHERE idNumber LIKE '{$_SESSION['idNumber']}' AND status = 1";
                PARENT::setSQLQuery($sql);
                $languageData = PARENT::getRecords();
                $lang = $languageData[0]['languageFlag'];
            }

            $textValue = ($lang == 1) ? $displayTextOne : $displayTextTwo;
        }

        return $textValue;
    }

    public function addClass($newClassValue)
    {
        $this->newClass[] = $newClassValue;
    }

    public function addCss($prop, $cssValue)
    {
        $this->newCss[] = $prop.":".$cssValue.";";
    }

    public function render()
    {
        $nameValue = $this->getText($this->listName);
        if($this->attributes != NULL) $attr = implode(" ",$this->attributes);
        $this->rows[] = "<a {$attr} class='w3-bar-item w3-button w3-medium w3-border w3-text-black' style='cursor:pointer;'><i class='fa fa-chevron-right'></i>&emsp;<b>{$nameValue}</b></a>";
        $this->attributes = Array();
    }

    public function createElement($element)
    {
        $this->elem = $element;
    }
    
    private function appendAttribute($key, $attr, $addValue)
    {
        $checkKeys = strpos($attr, $key);
        if($checkKeys !== false)
        {
            $counterLength = strlen($key) + 1;
            
            $stringPositionOne = strpos($attr, $key) + $counterLength;
            $checkStringOne = substr($attr, $stringPositionOne, -1);    
            $stringPositionTwo = strpos($checkStringOne, "'");
            $checkStringTwo = substr($attr, $stringPositionOne, $stringPositionTwo);    

            if($stringPositionTwo === false)
            {
                $stringPositionTwo = -1;
                $addValue = $addValue." ".$checkStringOne;
            }
            else
            {
                $addValue = $addValue." ".$checkStringTwo;
            }

            $newAttribute = substr_replace($attr, $addValue, $stringPositionOne, $stringPositionTwo);
        }
        else
        {
            $newAttribute = $attr." ".$key."'{$addValue}'";
        }

        return $newAttribute;
    }

    public function createButton($iconOnly = 0)
    {   
        if($this->attributes != NULL) $attr = implode(" ",$this->attributes);
        $tag = $this->elem;
        $icon = $this->icon;
        $dataValue = $this->dataValue;
        
        $leftIcons = "";
        if($iconOnly == 0) $leftIcons = "position:relative; float:left; top:1px;";

        $spacer = "";
        if($icon != "") 
        {
            $icon = "<i style='{$leftIcons}' class='btnIcon {$icon}'></i>";
            $spacer = "&emsp;";
        }

        if(!in_array($tag, Array("button", "a"))) $tag = "button";

        $sql = "SELECT displayTextOne, displayTextTwo FROM system_software WHERE displayId LIKE '".$dataValue."' ORDER BY displayId DESC LIMIT 1";
        PARENT::setSQLQuery($sql);
        $resultData = PARENT::getRecords();
        if($resultData != NULL)
        {
            $displayTextOne = $resultData[0]['displayTextOne'];
            $displayTextTwo = $resultData[0]['displayTextTwo'];

            $lang = $this->language;
            if(isset($_SESSION['idNumber']))
            {
                $sql = "SELECT * FROM hr_employee WHERE idNumber LIKE '{$_SESSION['idNumber']}' AND status = 1";
                PARENT::setSQLQuery($sql);
                $languageData = PARENT::getRecords();
                $lang = $languageData[0]['languageFlag'];
            }

            $dataValue = ($lang == 1) ? $displayTextOne : $displayTextTwo;

            $fixAttr = "";
            $sql = "SELECT * FROM system_templates WHERE templateName LIKE '{$displayTextOne}' AND templateType = 0";
            PARENT::setSQLQuery($sql);
            $records = PARENT::getRecords();
            if($records != NULL)
            {
                $displayId = $records[0]['displayId'];
                $templateName = $records[0]['templateName'];
                $templateColor = $records[0]['templateColor'];
                $templateWidth = $records[0]['templateWidth'];
                $templateIcon = $records[0]['templateIcon'];

                $icon = "<i style='{$leftIcons}' class='btnIcon {$templateIcon}'></i>";
                
                $fixAttrArray = Array();
                if(strtolower($tag) == "a")
                {
                    $fixAttrArray[] = "class='w3-btn w3-round w3-dark-grey'";
                    $templateWidth = "";
                }
                else
                {
                    $fixAttrArray[] = "class='w3-btn w3-round {$templateColor}'";
                }

                $spacer = "&emsp;";
                if($iconOnly == 1) 
                {
                    $dataValue = "";
                    $spacer = "";
                }
                else
                {
                    if($this->newCss != NULL) 
                    {
                        $newCssValue = implode(" ",$this->newCss);

                        if(strpos($newCssValue, "width") === false)
                        {
                            if($lang == 1 OR strlen($displayTextOne) <= 9) $fixAttrArray[] = "style='width:{$templateWidth};'";
                        }
                    }
                    else
                    {
                        if($lang == 1 OR strlen($displayTextOne) <= 9) $fixAttrArray[] = "style='width:{$templateWidth};'";
                    }
                }

                if($fixAttrArray != NULL) 
                {
                    $fixAttr = implode(" ",$fixAttrArray);

                    if($this->newClass != NULL) 
                    {
                        $newClassValue = implode(" ",$this->newClass);
                        $fixAttr = $this->appendAttribute("class=", $fixAttr, $newClassValue);
                    }

                    if($this->newCss != NULL) 
                    {
                        $newCssValue = implode(" ",$this->newCss);
                        $fixAttr = $this->appendAttribute("style=", $fixAttr, $newCssValue);
                    }
                }
            }
            else
            {
                # DEFAULT BUTTON VALUES
                $icon = "<i style='{$leftIcons}' class='btnIcon fa fa-question-circle-o'></i>";
                
                $fixAttrArray = Array();
                if(strtolower($tag) == "a")
                {
                    $fixAttrArray[] = "class='w3-btn w3-round w3-dark-grey'";

                }
                else
                {
                    $fixAttrArray[] = "class='w3-btn w3-round w3-deep-orange'";
                }
                // $fixAttrArray[] = "style='width:130px;'";
                
                $spacer = "&emsp;";
                if($iconOnly == 1) 
                {
                    $dataValue = "";
                    $spacer = "";
                }

                if($fixAttrArray != NULL) 
                {
                    $fixAttr = implode(" ",$fixAttrArray);
                    if($this->newClass != NULL) 
                    {
                        $newClassValue = implode(" ",$this->newClass);
                        $fixAttr = $this->appendAttribute("class=", $fixAttr, $newClassValue);
                    }

                    if($this->newCss != NULL) 
                    {
                        $newCssValue = implode(" ",$this->newCss);
                        $fixAttr = $this->appendAttribute("style=", $fixAttr, $newCssValue);
                    }
                }
            }
        }
        else
        {
            if($iconOnly == 1) 
            {
                $dataValue = "";
                $spacer = "";
            }

            if($this->newClass != NULL) 
            {
                $newClassValue = implode(" ",$this->newClass);
                $attr = $this->appendAttribute("class=", $attr, $newClassValue);
            }

            if($this->newCss != NULL) 
            {
                $newCssValue = implode(" ",$this->newCss);
                $attr = $this->appendAttribute("class=", $attr, $newCssValue);
            }
        }

        if(strtolower($tag) == "a") 
        {
            $icon = "<i style='{$leftIcons}' class='fa fa-link'></i>";
        }

        $html = "&nbsp;<{$tag} {$fixAttr} {$attr}>".$icon.$spacer."<b>".strtoupper($dataValue)."</b></{$tag}>";
        
        $this->attributes = Array();
        $this->newClass = Array();
        $this->newCss = Array();
        $this->dataValue = "";
        $this->icon = "";
        $this->elem = "";
        return $html;
    }

    public function createDropdownButton($iconOnly = 0)
    {
        if($this->rows != NULL )$rows = implode(" ",$this->rows);
        $button = $this->createButton();

        $dropdown = "<div class='w3-dropdown-hover'>";
            $dropdown .= $button;
            $dropdown .= "<div class='w3-dropdown-content w3-bar-block w3-border' style='width:280px;z-index:500;'>";
            $dropdown .= $rows;
            $dropdown .= "</div>";
        $dropdown .= "</div>";

        $this->rows = Array();
        return $dropdown;
    }

    public function createInput()
    {   
        $attr = implode(" ",$this->attributes);
        $tag = $this->elem;
        $dataValue = $this->dataValue; # IF TEXTAREA

        if(in_array($tag, Array('datalist', 'select')))
        {
            $select = implode("\n",$this->selectOptions);
            $html = "<{$tag} {$attr}><option></option>{$select}</{$tag}>";
        }
        else
        {
            $html = "<{$tag} {$attr}>".$dataValue."</{$tag}>";
        }

        $this->attributes = Array();
        $this->selectOptions = Array();
        $this->newClass = Array();
        
        return $html;
    }
}

class PMSDBController extends PMSDatabase
{
    private function isKanji($str) {
        return preg_match('/[\x{4E00}-\x{9FBF}]/u', $str) > 0;
    }
    
    private function isHiragana($str) {
        return preg_match('/[\x{3040}-\x{309F}]/u', $str) > 0;
    }
    
    private function isKatakana($str) {
        return preg_match('/[\x{30A0}-\x{30FF}]/u', $str) > 0;
    }
    
    private function isJapanese($str) {
        return $this->isKanji($str) || $this->isHiragana($str) || $this->isKatakana($str);
    }

    public function getEmployee($id, $languageFlag=0, $returnType=0)
    {
        if($id == "*")
        {
            $idNumberArray = $fullNameArray = $employeeIdArray = Array();
            $sql = "SELECT * FROM hr_employee WHERE status = 1 ORDER BY surName";
            PARENT::setSQLQuery($sql);
            $queryEmployee = PARENT::getRecords();
            if($queryEmployee != NULL)
            {
                foreach($queryEmployee AS $resultEmployee)
                {
                    $employeeId = $resultEmployee['employeeId'];
                    $idNumber = $resultEmployee['idNumber'];
                    $randomId = $resultEmployee['randomId'];
                    $firstName = trim($resultEmployee['firstName']);
                    $surName = trim($resultEmployee['surName']);
                    $firstNameJP = trim($resultEmployee['firstNameJP']);
                    $surNameJP = trim($resultEmployee['surNameJP']);
    
                    if($languageFlag == 0) $fullName = $firstName." ".$surName;
                    if($languageFlag == 1) $fullName = $surNameJP." ".$firstNameJP;
    
                    $checkString = $this->isJapanese($fullName);
                    if($checkString == true)
                    {
                        $fullName = $surName." ".$firstName;
                    }

                    $idNumberArray[] = $idNumber;
                    $fullNameArray[$idNumber] = $fullName;
                    $employeeIdArray[$idNumber] = $employeeId;
                }
    
                return Array($idNumberArray, $fullNameArray, $employeeIdArray);
            }
        }
        else
        {
            $sql = "SELECT * FROM hr_employee WHERE (employeeId = '".$id."' OR idNumber = '".$id."' OR randomId = '".$id."') AND status = 1";
            PARENT::setSQLQuery($sql);
            $resultEmployee = PARENT::getRecords();
            if($resultEmployee != NULL)
            {
                $employeeId = $resultEmployee[0]['employeeId'];
                $idNumber = $resultEmployee[0]['idNumber'];
                $randomId = $resultEmployee[0]['randomId'];
                $firstName = trim($resultEmployee[0]['firstName']);
                $surName = trim($resultEmployee[0]['surName']);
                $firstNameJP = trim($resultEmployee[0]['firstNameJP']);
                $surNameJP = trim($resultEmployee[0]['surNameJP']);
    
                if($languageFlag == 0) $fullName = $firstName." ".$surName;
                if($languageFlag == 1) $fullName = $surNameJP." ".$firstNameJP;

                $checkString = $this->isJapanese($fullName);
                if($checkString == true)
                {
                    $fullName = $surName." ".$firstName;
                }
    
                return $fullName;
            }
        }
    }
}

class CreateInputClass
{
    protected $attributesDataString = "";
    protected $returnInput = "";
    protected $inputType = "text";
    protected $attributeDataArray = array();
    protected $dataArray = array();
    protected $textareaValue = "";
    
    function CreateInputClass($inputParam='text')
    {
        $this->inputType = $inputParam;
    }
    
    function createInput()
    {
        $this->assignAttr();
        
        if($this->inputType=='text' OR $this->inputType=='number')
        {
            $this->returnInput = "<input ".$this->attributesDataString.">";
        }
        else if($this->inputType=='textarea')
        {
            $this->returnInput = "<textarea ".$this->attributesDataString.">".$this->textareaValue."</textarea>";
        }
        else if($this->inputType=='dropdown' OR $this->inputType=='datalist')
        {
            $dataArray = $this->dataArray;
            $defaultValue = (isset($this->attributeDataArray['value'])) ? $this->attributeDataArray['value'] : '';
            
            $optionData = "";
            if(count($dataArray) > 0)
            {
                foreach($dataArray as $key=>$value)
                {
                    $selected = ($defaultValue==$key) ? 'selected' : '';
                    $optionData .= "<option value=\"{$key}\" {$selected}>{$value}</option>";
                }
            }
            
            if($this->inputType=='dropdown')
            {
                $this->returnInput = "<select ".$this->attributesDataString.">".$optionData."</select>";
            }
            else if($this->inputType=='datalist')
            {
                $this->returnInput = "<input ".$this->attributesDataString."><datalist id='".$this->attributeDataArray['list']."'>".$optionData."</datalist>";
            }
        }
        
        return $this->returnInput;
    }
    
    function attr($attributeData)
    {
        $this->attributeDataArray = $attributeData;
    }
    
    function data($dataArray)
    {
        $this->dataArray = $dataArray;
    }
    
    protected function assignAttr()
    {
        $attributeData = $this->attributeDataArray;
        
        $defaultAttributeDataArray = array(
            'class'=>array('w3-input','w3-border','w3-xlarge'),
            'style'=>array('max-width: 500px;')
        );
        
        if($this->inputType=='text')
        {
            $defaultAttributeDataArray['type'] = 'text';
            $defaultAttributeDataArray['class'][] = 'w3-yellow';
            
            unset($attributeData['type']);
        }
        else if($this->inputType=='number')
        {
            $defaultAttributeDataArray['type'] = 'number';
            $defaultAttributeDataArray['class'][] = 'w3-pale-green';
            
            unset($attributeData['type']);
        }
        else if($this->inputType=='textarea')
        {
            $defaultAttributeDataArray['class'][] = 'w3-pale-yellow';
        }		
        else if($this->inputType=='dropdown')
        {
            $defaultAttributeDataArray['class'][] = 'w3-khaki';
        }
        else if($this->inputType=='datalist')
        {
            $defaultAttributeDataArray['class'][] = 'w3-pale-red';
            $defaultAttributeDataArray['list'] = str_shuffle(date('ymdhis').rand());
            
            unset($attributeData['list']);
        }
        
        if(isset($attributeData['class']))
        {
            $defaultAttributeDataArray['class'] = array_merge($defaultAttributeDataArray['class'],$attributeData['class']);
            unset($attributeData['class']);
        }
        
        $attributeData = array_merge($defaultAttributeDataArray,$attributeData);		
        
        $this->attributeDataArray = $attributeData;
        
        $attributesDataStringArray = array();
        
        if(is_array($attributeData))
        {
            if(count($attributeData) > 0)
            {
                $attributesDataArray = $attributeData;
                
                foreach($attributesDataArray as $key=>$value)
                {
                    $attribute = strtolower(trim($key));
                    
                    if($this->inputType=='textarea')
                    {
                        if($attribute=='value')
                        {
                            $this->textareaValue = $value;
                        }
                    }
                    
                    $attributeValue = "";
                    if(is_bool($value)!==FALSE)
                    {
                        if($value==true)
                        {
                            $attributesDataStringArray[] = $attribute;
                        }
                    }
                    else
                    {
                        if(is_array($value))
                        {
                            if(count($value) > 0)	$attributeValue = implode(" ",$value);
                        }
                        else
                        {
                            $attributeValue = $value;
                        }
                        
                        $attributesDataStringArray[] = "{$attribute}=\"{$attributeValue}\"";
                    }
                }
                $this->attributesDataString = implode(" ",$attributesDataStringArray);
            }
        }
    }
}
?>