<?php
require_once './config.php';
require_once './options.php';
session_start();
date_default_timezone_set($option['timezone']);
if (isset($_SESSION['lang'])) {
    if ($_SESSION['lang'] != "") {
        if ($_SESSION['lang'] == "hi") {
            require_once './lang/hi.php';
        } else if ($_SESSION['lang'] == "fr") {
            require_once './lang/fr.php';
        } else if ($_SESSION['lang'] == "ch") {
            require_once './lang/ch.php';
        } else if ($_SESSION['lang'] == "ar") {
            require_once './lang/ar.php';
        } else if ($_SESSION['lang'] == "sp") {
            require_once './lang/sp.php';
        } else if ($_SESSION['lang'] == "ru") {
            require_once './lang/ru.php';
        } else if ($_SESSION['lang'] == "de") {
            require_once './lang/de.php';
        } else if ($_SESSION['lang'] == "pl") {
            require_once './lang/pl.php';
        } else {
            require_once './lang/en.php';
        }
    } else {
        if (isset($option['defaultlanguage']) && $option['defaultlanguage'] != "" && $option['defaultlanguage'] != null) {
            require_once './lang/' . $option['defaultlanguage'] . '.php';
            $_SESSION['lang'] = $option['defaultlanguage'];
        } else {
            require_once './lang/en.php';
            $_SESSION['lang'] = "en";
        }
    }
} else {
    if (isset($option['defaultlanguage']) && $option['defaultlanguage'] != "" && $option['defaultlanguage'] != null) {
        require_once './lang/' . $option['defaultlanguage'] . '.php';
        $_SESSION['lang'] = $option['defaultlanguage'];
    } else {
        require_once './lang/en.php';
        $_SESSION['lang'] = "en";
    }
}
if (isset($_COOKIE['tmail-emails'])) {
    $emailList = unserialize($_COOKIE['tmail-emails']);
    $_SESSION["emails"] = $emailList;
}
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title><?php echo $config['title']; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- TMail Styles -->
        <link rel="stylesheet" href="css/styles.css">
        <!-- Vendor Styles -->
        <link href="core/JqueryScrollbar/jquery.custom-scrollbar.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
              crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Vendor JS -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <script src="core/signals.js"></script>
        <script src="core/hasher.min.js"></script> 
        <script src="core/JqueryScrollbar/jquery.custom-scrollbar.min.js"></script> 
    </head>

    <body <?php if($option['layout'] == 'full') { echo "style='padding: 0;'"; } ?>>
        <div class="container-fluid tmail-container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="tmail-header" <?php if($option['layout'] == 'full') { echo "style='border-radius: 0;'"; } ?>>
                        <div class="tmail-mobile-menu">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                            <i class="fa fa-times" aria-hidden="true"></i>
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="tmail-logo">
                            <a href="./"><img src="images/logo.png" height="50"></a>
                        </div>
                        <div class="tmail-language-switcher">
                            <select class="setLang" name="lang" onchange="setLang()">
                                <option value="en" <?php if ( $_SESSION['lang'] == "en") { echo "selected"; } ?>>English</option>
                                <option value="hi" <?php if ( $_SESSION['lang'] == "hi") { echo "selected"; } ?>>हिंदी</option>
                                <option value="fr" <?php if ( $_SESSION['lang'] == "fr") { echo "selected"; } ?>>Français</option>
                                <option value="ch" <?php if ( $_SESSION['lang'] == "ch") { echo "selected"; } ?>>中文</option>
                                <option value="ar" <?php if ( $_SESSION['lang'] == "ar") { echo "selected"; } ?>>عربى</option>
                                <option value="sp" <?php if ( $_SESSION['lang'] == "sp") { echo "selected"; } ?>>Español</option>
                                <option value="ru" <?php if ( $_SESSION['lang'] == "ru") { echo "selected"; } ?>>русский</option>
                                <option value="de" <?php if ( $_SESSION['lang'] == "de") { echo "selected"; } ?>>Deutsch</option>
                                <option value="pl" <?php if ( $_SESSION['lang'] == "pl") { echo "selected"; } ?>>Polskie</option>
                            </select>
                        </div>
                    </div>
                    <div class="container-fluid tmail-main">
                        <div class="row tmail-loader">
                            <div class="inner-loader"><span><?php echo $lang['loading']; ?></span> <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div> </div>
                        </div>
                        <div class="row tmail-homepage">
                            <div class="tmail-main-inner" align="center">
                                <input onKeyPress="checkEnter(event, this)" class="tmail-input-set-email" type="text" name="email" placeholder="<?php echo $lang['setid']; ?>">
                                <select class="tmail-input-set-domain" name="domain">
                                    <?php
                                    foreach ($config['domains'] as $value) {
                                        ?><option value="@<?php echo $value; ?>">@<?php echo $value; ?></option><?php
                                    }
                                    ?>
                                </select>
                                <div class="tmail-generate-custom">
                                    <a href="#" onclick="setNewID()">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i> <?php echo $lang['create']; ?> 
                                    </a>
                                </div>
                                <div style="font-size: 18px;"><?php echo $lang['or']; ?></div>
                                <div class="tmail-generate-random">
                                    <a href="#" onclick="createUser('')">
                                        <i class="fa fa-random" aria-hidden="true"></i><?php echo $lang['generaterandom']; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid tmail-body">
                        <div class="row">
                            <div class="col-lg-2 tmail-sidebar p-0">
                                <div class="menu-title"><?php echo $lang['menu']; ?></div>
                                <ul class="tmail-main-menu p-0">
                                    <?php
                                    $i = 0;
                                    foreach ($option['linksTitle'] as $linksTitle) {
                                    ?>
                                    <a target="_blank" href="<?php echo $option["linksValue"][$i] ?>">
                                        <li>
                                            <i class="fa <?php echo $option["linksIcon"][$i]; ?>" aria-hidden="true"></i>
                                            <span><?php echo $linksTitle; ?></span>
                                        </li>
                                    </a>
                                    <?php
                                    $i++;
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-lg-4 tmail-list p-0">
                                <div class="tmail-current-id clearfix" onclick="copyToClipboard('#current-tmail-id')">
                                    <div class="tmail-current-id-icon float-left">
                                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                                    </div>
                                    <div class="tmail-current-id-info float-left">
                                        <div id="current-tmail-id">Your EMail ID</div>
                                        <div class="tmail-current-id-info-text"><?php echo $lang['yourcurrent']; ?></div>
                                    </div>
                                </div>
                                <div class="input-group tmail-search-input">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="tmail-search" class="form-control" placeholder="<?php echo $lang['search']; ?>">
                                </div>
                                <ul id="tmail-data" class="tmail-list-ul">
                                </ul>
                                <div class="tmail-list-placeholder">
                                    <span><?php echo $lang['youremailshere']; ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 tmail-email-body">
                                <div class="tmail-email-body-placeholder">
                                    <span><?php echo $lang['noselected']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tmail-footer clearfix" <?php if($option['layout'] == 'full') { echo "style='border-radius: 0;'"; } ?>>
                        <div class="dropup tmail-switch float-left">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                <?php echo $lang['switch']; ?> &nbsp;
                                <span class="caret"></span>
                            </button>
                            <ul id="tmail-switcher-list-ul" class="dropdown-menu">
                                <li class="dropdown-create-menu">
                                    <a href="./"><?php echo $lang['getnew']; ?></a>
                                </li>
                                <?php if(isset($_SESSION["emails"])) { ?>
			        <?php foreach ($_SESSION["emails"] as $value) { ?>
                                <li onclick="createUser('<?php echo $value; ?>')">
                                    <a><?php echo $value; ?></a>
                                </li>
                                <?php } } ?>
                            </ul>
                        </div>
                        <div class="tmail-list-clear float-left">
                            <button onclick="clearEMails()" class="btn btn-default" type="button">Clear List</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="snackbar"></div>
        <!-- TMail Scripts -->
        <script src="js/scripts.js"></script>
        <?php echo $option["tracking"]; ?>
    </body>

</html>