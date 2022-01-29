<?php
require_once './config.php';
require_once './options.php';
session_start();
if(isset($_SESSION['lang'])) {
    if($_SESSION['lang'] != "") {
        if($_SESSION['lang'] == "hi") {
            require_once './lang/hi.php';
        } else if($_SESSION['lang'] == "fr") {
            require_once './lang/fr.php';
        } else if($_SESSION['lang'] == "ch") {
            require_once './lang/ch.php';
        } else if($_SESSION['lang'] == "ar") {
            require_once './lang/ar.php';
        } else if($_SESSION['lang'] == "sp") {
            require_once './lang/sp.php';
        } else if($_SESSION['lang'] == "ru") {
            require_once './lang/ru.php';
        } else if($_SESSION['lang'] == "de") {
            require_once './lang/de.php';
        } else if($_SESSION['lang'] == "pl") {
            require_once './lang/pl.php';
        } else {
            require_once './lang/en.php';
        }
    } else {
        require_once './lang/en.php';
        $_SESSION['lang'] = "en";
    }
} else {
    require_once './lang/en.php';
    $_SESSION['lang'] = "en";
}
if(isset($_POST["adminPass"])) {
    if(isset($config["admin"])) {
        $adminPass = filter_input(INPUT_POST, "adminPass", FILTER_SANITIZE_STRING);
        if($adminPass == $config["admin"]) {
            $_SESSION["adminEnabled"] = true;
        } else {
            header("location: admin.php?error=Invalid Password! Please try again");
            exit();
        }
    } else {
        $newfile = "<?php\n\$config = array(\n";
        foreach ( $config as $title => $setting ) {
            if($title == "domains" || $title == "forbidemail") {
                $newfile .= "\"$title\"=>array(";
                foreach ( $setting as $subSetting ) {
                    $newfile .= "\"".$subSetting."\",";
                }
                $newfile = rtrim($newfile,",");
                $newfile .= "),\n";
            } else {
                $newfile .= "\"$title\"=>\"$setting\",\n";
            }
	}
        $newfile .= "\"admin\"=>\"tmail123\",\n";
	$newfile .= ");\n?>";
        $input = "./config.php";
	$fpwrite = fopen($input, "w+");
	fputs($fpwrite, $newfile);
        header("location: admin.php?success=Default Password - <strong>tmail123</strong>");
        exit();
    }
}
if(isset($_SESSION["adminEnabled"])) {
    if($_SESSION["adminEnabled"] == true) {
        if(isset($_POST["configuration"])) {
            $config = array(
                "title"=>$_POST["title"],
                "host"=>$_POST["host"],
                "user"=>$_POST["user"],
                "pass"=>$_POST["pass"],
                "domains"=>$_POST["domain"],
                "forbidemail"=>$_POST["forbidemail"],
                "admin"=>$_POST["admin"],
            );
            $newfile = "<?php\n\$config = array(\n";
            foreach ( $config as $title => $setting ) {
                if($title == "domains" || $title == "forbidemail") {
                    $newfile .= "\"$title\"=>array(";
                    foreach ( $setting as $subSetting ) {
                        if($subSetting != null && $subSetting != "") {
                            $newfile .= "\"".trim($subSetting," ")."\",";
                        }
                    }
                    $newfile = rtrim($newfile,",");
                    $newfile .= "),\n";
                } else {
                    $setting = trim($setting," ");
                    $newfile .= "\"$title\"=>\"$setting\",\n";
                }
            }
            $newfile .= ");\n?>";
            $input = "./config.php";
            $fpwrite = fopen($input, "w+");
            fputs($fpwrite, $newfile);
            sleep(3);
            header("location: admin.php?success=Configuration Updated Successfully!");
            exit();
        } else if (isset($_POST["options"])) {
            $trackingParsed = str_replace('"','\"',$_POST["tracking"]);
            $trackingParsed = str_replace('$','\$',$trackingParsed);
            $option = array(
                "deleteDays"=>$_POST["deleteDays"],
                "refreshRate"=>$_POST["refreshRate"],
                "ads"=>str_replace('"','\"',$_POST["ads"]),
                "tracking"=>$trackingParsed,
                "ssl"=>$_POST["ssl"],
                "logs"=>$_POST["logs"],
                "pushNotifications"=>$_POST["pushNotifications"],
                "timezone"=>$_POST["timezone"],
                "defaultlanguage"=>$_POST["defaultlanguage"],
                "linksIcon"=>$_POST["linksIcon"],
                "linksTitle"=>$_POST["linksTitle"],
                "linksValue"=>$_POST["linksValue"],
                "layout"=>$_POST["layout"],
            );
            $newfile = "<?php\n\$option = array(\n";
            foreach ( $option as $title => $setting ) {
                if($title == "linksTitle" || $title == "linksValue" || $title == "linksIcon") {
                    $newfile .= "\"$title\"=>array(";
                    foreach ( $setting as $subSetting ) {
                        if($subSetting != null && $subSetting != "") {
                            $newfile .= "\"".trim($subSetting," ")."\",";
                        }
                    }
                    $newfile = rtrim($newfile,",");
                    $newfile .= "),\n";
                } else {
                    $setting = trim($setting," ");
                    $newfile .= "\"$title\"=>\"$setting\",\n";
                }
            }
            $newfile .= ");\n?>";
            $input = "./options.php";
            $fpwrite = fopen($input, "w+");
            fputs($fpwrite, $newfile);
            sleep(3);
            header("location: admin.php?success=Options Updated Successfully!");
            exit();
        }
    }
}
error_reporting(E_ALL);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - <?php echo $config['title']; ?></title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400" rel="stylesheet"> 
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="core/signals.js"></script>
    <script src="core/hasher.min.js"></script> 
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div id="main" class="container">
        <div class="header">
            <div id="logo" data-placement="left" data-toggle="tooltip" title="<?php echo $config['title']; ?>" ><a href="index.php"><img src="images/logo-inverse.png"></a></div>
            <h3>Admin Panel</h3>
        </div>
        <div class="clearfix"></div>
        <?php if(isset($_GET["success"])) { ?>
        <div class="success"><?php echo $_GET["success"]; ?></div>
        <?php } ?>
        <?php if(isset($_GET["error"])) { ?>
        <div class="error"><?php echo $_GET["error"]; ?></div>
        <?php } ?>
        <?php if((!isset($_SESSION["adminEnabled"])) || $_SESSION["adminEnabled"] == FALSE ) { ?>
        <form id="adminLogin" method="post">
            <input type="password" name="adminPass" placeholder="Enter Admin Password"><br>
            <input type="submit" value="LOGIN">
        </form>
        <?php } else { ?>
        <h2>Configuration</h2>
        <form method="post">
            <div class="text-field">
                <span>Title</span><br><input type="text" name="title" placeholder="Enter your Title" value="<?php echo $config["title"]; ?>">
            </div>
            <div class="text-field">
                <span>Host Name</span><br><input type="text" name="host" placeholder="Enter your Hostname" value="<?php echo $config["host"]; ?>">
            </div>
            <div class="text-field">
                <span>Catch Email</span><br><input type="text" name="user" placeholder="Enter your Catch Email ID" value="<?php echo $config["user"]; ?>">
            </div>
            <div class="text-field">
                <span>Email Password</span><br><input type="text" name="pass" placeholder="Enter your Catch Email ID's Password" value="<?php echo $config["pass"]; ?>">
            </div>
            <div class="text-field">
                <span>Domains</span><br>
                <?php 
                foreach($config["domains"] as $domain) {
                    echo '<input class="inner-fields" type="text" name="domain[]" placeholder="Enter Domain" value="'.$domain.'">';
                }
                ?>
                <div class="addIcons" id="addDomain">+</div>
            </div>
            <div class="text-field">
                <span>Forbiden Email</span><br>
                <?php 
                foreach($config["forbidemail"] as $forbidemail) {
                    echo '<input class="inner-fields" type="text" name="forbidemail[]" placeholder="Enter Forbiden Email" value="'.$forbidemail.'">';
                }
                ?>
                <div class="addIcons" id="addForbidden">+</div>
            </div>
            <div class="text-field">
                <span>Admin Password</span><br><input type="text" name="admin" placeholder="Enter your Admin Password" value="<?php echo $config["admin"]; ?>">
            </div>
            <input type="submit" name="configuration" value="Change Configuration">
        </form>
        <div class="clearfix"></div>
        <h2>Options</h2>        
        <div class="clearfix"></div>
        <form method="post">
            <div class="text-field">
                <span>Number of days after which emails will be deleted (Days)</span><br><input type="text" name="deleteDays" placeholder="Enter your Number of Days" value="<?php echo $option["deleteDays"]; ?>">
            </div>
            <div class="text-field">
                <span>Refresh Rate (Second) </span><br><input type="text" name="refreshRate" placeholder="Enter Refresh Rate" value="<?php echo $option['refreshRate']; ?>">
            </div>
            <div class="text-field">
                <span>Default Timezone </span><br>
                <small><a target="_blank" href="http://php.net/manual/en/timezones.php">List of Timezones</a></small><br>
                <input type="text" name="timezone" placeholder="Enter Default Timezone" value="<?php echo $option['timezone']; ?>">
            </div>
            <div class="text-field">
                <span>Ads</span><br>
                <textarea name="ads" placeholder="Enter your Ad Code"><?php echo $option['ads']; ?></textarea>
            </div>
            <div class="text-field">
                <span>Tracking Code</span><br>
                <small>Useful for Google Analytics, Live Chats, etc</small><br>
                <textarea name="tracking" placeholder="Enter your Ad Code"><?php echo $option['tracking']; ?></textarea>
            </div>
            <div class="text-field">
                <span>Do you want to use SSL while connecting to IMAP?</span><br>
                <select name="ssl">
                    <option value="yes" <?php if($option['ssl'] == "yes") { echo "selected"; } ?>>Yes</option>
                    <option value="no" <?php if($option['ssl'] == "no") { echo "selected"; } ?>>No</option>
                </select>
            </div>
            <div class="text-field">
                <span>Do you want to save Logs?</span><br>
                <select name="logs">
                    <option value="yes" <?php if($option['logs'] == "yes") { echo "selected"; } ?>>Yes</option>
                    <option value="no" <?php if($option['logs'] == "no") { echo "selected"; } ?>>No</option>
                </select>
            </div>
            <div class="text-field">
                <span>Do you want to enable Push Notifcations?</span><br>
                <select name="pushNotifications">
                    <option value="yes" <?php if($option['pushNotifications'] == "yes") { echo "selected"; } ?>>Yes</option>
                    <option value="no" <?php if($option['pushNotifications'] == "no") { echo "selected"; } ?>>No</option>
                </select>
            </div>
            <div class="divided-text-field">
                <span>Links</span><br>
                <small><a target="_blank" href="https://fontawesome.com/v4.7.0/icons/">List of Icons</a></small>
                <br>
                <br>
                <?php 
                $i = 0;
                foreach($option["linksTitle"] as $linksTitle) {
                    echo '<input class="small-inner-fields" type="text" name="linksIcon[]" placeholder="Enter Icon" value="'.$option["linksIcon"][$i].'">';
                    echo '<input class="small-inner-fields" type="text" name="linksTitle[]" placeholder="Enter Title" value="'.$linksTitle.'">';
                    echo '<input class="big-inner-fields" type="text" name="linksValue[]" placeholder="Enter Link" value="'.$option["linksValue"][$i].'">';
                    $i++;
                }
                ?>
                <div class="addIcons" id="addLinks">+</div>
            </div>
            <div class="text-field">
                <span>Website Layout Type</span><br>
                <select name="layout">
                    <option value="full" <?php if($option['layout'] == "full") { echo "selected"; } ?>>Full Width</option>
                    <option value="boxed" <?php if($option['layout'] == "boxed") { echo "selected"; } ?>>Boxed</option>
                </select>
            </div>
            <div class="text-field">
                <span>Default Language </span><br>
                <select name="defaultlanguage">
                <?php if(isset($option['defaultlanguage'])) { ?>
                    <option value="en" <?php if ( $option['defaultlanguage'] == "en") { echo "selected"; } ?>>English</option>
                    <option value="hi" <?php if ( $option['defaultlanguage'] == "hi") { echo "selected"; } ?>>हिंदी</option>
                    <option value="fr" <?php if ( $option['defaultlanguage'] == "fr") { echo "selected"; } ?>>Français</option>
                    <option value="ch" <?php if ( $option['defaultlanguage'] == "ch") { echo "selected"; } ?>>中文</option>
                    <option value="ar" <?php if ( $option['defaultlanguage'] == "ar") { echo "selected"; } ?>>عربى</option>
                    <option value="sp" <?php if ( $option['defaultlanguage'] == "sp") { echo "selected"; } ?>>Español</option>
                    <option value="ru" <?php if ( $option['defaultlanguage'] == "ru") { echo "selected"; } ?>>русский</option>
                    <option value="de" <?php if ( $option['defaultlanguage'] == "de") { echo "selected"; } ?>>Deutsch</option>
                    <option value="pl" <?php if ( $option['defaultlanguage'] == "pl") { echo "selected"; } ?>>Polskie</option>
				<?php } else { ?>
                    <option value="en">English</option>
                    <option value="hi">हिंदी</option>
                    <option value="fr">Français</option>
                    <option value="ch">中文</option>
                    <option value="ar">عربى</option>
                    <option value="sp">Español</option>
                    <option value="ru">русский</option>
                    <option value="de">Deutsch</option>
                    <option value="pl">Polskie</option>
				<?php } ?>
                </select>
            </div>
            <input type="submit" name="options" value="Change Options">
        </form>
        <?php } ?>
        <br><br><br><br><br><br><br>
    </div>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html> 