<?php
require_once './config.php';
require_once './core/PhpImap/__autoload.php';
require_once './options.php';
session_start();
error_reporting(E_ALL);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if ( $action == "delete" ) {
    if($option['ssl'] == "yes") {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/ssl}INBOX', $config['user'], $config['pass'], __DIR__);
    } else {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/novalidate-cert}INBOX', $config['user'], $config['pass'], __DIR__);
    }
    $mailID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $mailID = preg_replace('/[^0-9.]+/', '', $mailID);
    $mail = $mailbox->getMail($mailID);
    $mailAddress = $mail->toString;
    if($mailAddress == $_SESSION['address']) {
        if($mailbox->deleteMail($mailID)) {
            echo "true";
        } else {
            echo "false";
        }
    } else {
        echo "UnAuthorized";
    }
} else if ( $action == "getUser" ) {
    echo $_SESSION['address'];
} else if ( $action == "download" ) {
    if($option['ssl'] == "yes") {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/ssl}INBOX', $config['user'], $config['pass'], __DIR__);
    } else {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/novalidate-cert}INBOX', $config['user'], $config['pass'], __DIR__);
    }
    $mailID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $mailID = preg_replace('/[^0-9.]+/', '', $mailID);
    $mail = $mailbox->getMail($mailID);
    $mailAddress = $mail->toString;
    if($mailAddress == $_SESSION['address']) {
        $filename = "downloads/".$_SESSION['address']."_".$mailID."_mail.eml";
        $mailbox->saveMail($mailID, $filename);
        echo "./".$filename;
    } else {
        echo "UnAuthorized";
    }
} else if ( $action == "refreshRate" ) {
    echo $option['refreshRate'];
} else if ( $action == "pushNotifications" ) {
    if(isset($option['pushNotifications'])) {
        echo $option['pushNotifications'];
    } else {
        echo "no";
    }
} else if ( $action == "changeLang" ) {
    $_SESSION['lang'] = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
} else if ( $action == "getCount" ) {
    if($option['ssl'] == "yes") {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/ssl}INBOX', $config['user'], $config['pass'], __DIR__);
    } else {
        $mailbox = new PhpImap\Mailbox('{'.$config['host'].'/imap/novalidate-cert}INBOX', $config['user'], $config['pass'], __DIR__);
    }
    if(isset($_SESSION["address"])) {
        $address = $_SESSION["address"];
        $toList = "TO ".$address;
        $ccList = "CC ".$address;
        $bccList = "BCC ".$address;
        $mailIdsTo = $mailbox->searchMailbox($toList);
        $mailIdsCc = $mailbox->searchMailbox($ccList);
        $mailIdsBcc = $mailbox->searchMailbox($bccList);
        $mailsIds = array_unique(array_merge($mailIdsTo,$mailIdsCc,$mailIdsBcc));
        if($mailsIds) {
            echo count($mailsIds);
        } else {
            echo "0";
        }
    } else {
        echo "0";   
    }
} else if ( $action == "saveEMails" ) {
    if(setcookie('tmail-emails', serialize($_SESSION["emails"]), time() + (86400 * 7), "/")) {
        echo "1";
    } else {
        echo "0";
    }
} else if ( $action == "clearEMails" ) {
    $_SESSION["emails"] = array();
    if(setcookie('tmail-emails', serialize($_SESSION["emails"]), time() + (86400 * 7), "/")) {
        echo "1";
    } else {
        echo "0";
    }
}
