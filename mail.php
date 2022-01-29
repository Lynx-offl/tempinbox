<?php
date_default_timezone_set("Asia/Calcutta");
require_once './config.php';
require_once './core/PhpImap/__autoload.php';
require_once './functions.php';
require_once './options.php';
if (isset($_SESSION['lang'])) {
    if ($_SESSION['lang'] != "") {
        require_once './lang/' . $_SESSION['lang'] . '.php';
    } else {
        require_once './lang/en.php';
    }
} else {
    require_once './lang/en.php';
}
session_start();
error_reporting(E_ALL);
$unseen = filter_input(INPUT_GET, 'unseen', FILTER_SANITIZE_STRING);
$count = 0;
if ($option['ssl'] == "yes") {
    $mailbox = new PhpImap\Mailbox('{' . $config['host'] . '/imap/ssl}INBOX', $config['user'], $config['pass'], __DIR__);
} else {
    $mailbox = new PhpImap\Mailbox('{' . $config['host'] . '/imap/novalidate-cert}INBOX', $config['user'], $config['pass'], __DIR__);
}
if (!isset($_SESSION["address"])) {
    die("DIE");
}

$ids = $mailbox->searchMailbox('BEFORE ' . date('d-M-Y', strtotime($option['deleteDays'] . " days ago")));
foreach ($ids as $id) {
    $mailbox->deleteMail($id);
}
$mailbox->expungeDeletedMails();
$files = glob('downloads/*');
foreach ($files as $file) {
    if (is_file($file))
        unlink($file);
}
$address = $_SESSION["address"];
$toList = "TO " . $address;
$ccList = "CC " . $address;
$bccList = "BCC " . $address;
$mailIdsTo = $mailbox->searchMailbox($toList);
$mailIdsCc = $mailbox->searchMailbox($ccList);
$mailIdsBcc = $mailbox->searchMailbox($bccList);
$mailsIds = array_reverse(array_unique(array_merge($mailIdsTo, $mailIdsCc, $mailIdsBcc)));
if ($unseen == 1) {
    $unseenIds = $mailbox->searchMailbox("UNSEEN");
    $mailsIds = array_intersect($mailsIds, $unseenIds);
}
foreach ($mailsIds as $mailID) {
    $mail = $mailbox->getMail($mailID);
    $mailTime = DateTime::createFromFormat("Y-m-d H:i:s", $mail->date);
    $timestamp = $mailTime->getTimestamp();
    $yesterdayTime = new DateTime('yesterday');
    $yesterdayTimeStamp = $yesterdayTime->getTimestamp();
    if (date('Ymd') == date('Ymd', $timestamp)) {
        $displayDateTime = $lang['today'];
    } else if (date('Ymd') == date('Ymd', $yesterdayTimeStamp)) {
        $displayDateTime = $lang['yesterday'];
    } else {
        $displayDateTime = date('d M y', $timestamp);
    }
    ?>
    <li class="tmail-email-list-li" id="tmail-email-list-<?php echo $mailID; ?>" onclick="showTMailBody(<?php echo $mailID; ?>)">
        <div class="clearfix">
            <div class="name">
                <?php
                if ($mail->fromName) {
                    echo $mail->fromName;
                } else {
                    echo $mail->fromAddress;
                }
                ?>
            </div>
            <div class="date"><?php echo $displayDateTime; ?></div>
        </div>
        <div class="subject"><?php echo $mail->subject; ?></div>
        <div class="body"><?php echo $mail->textPlain; ?></div>
    </li>
    <-----TMAILCHOPPER----->
    <div class="tmail-email-content-li" id="tmail-email-body-content-<?php echo $mailID; ?>">
        <center class="tmail-ads"><?php echo $option['ads']; ?></center>
        <div class="tmail-email-title"><?php echo $mail->subject ?></div>
        <div class="tmail-email-description clearfix">
            <div class="tmail-email-sender float-left"><?php if ($mail->fromName) {
                echo $mail->fromName . " - ";
            } echo $mail->fromAddress; ?></div>
            <div class="tmail-email-time float-right"><?php echo $mail->date; ?></div>
        </div>
        <div class="tmail-body-delete-download-icons clearfix">
            <div class="tmail-body-download-icon float-left" onclick="downloadMail('<?php echo $mailID; ?>')"><i class="fa fa-download" aria-hidden="true"></i></div>
            <div class="tmail-body-delete-icon float-left" onclick="deleteMail('<?php echo $mailID; ?>')"><i class="fa fa-trash" aria-hidden="true"></i></div>
        </div>
        <div class="tmail-email-body-content">
            <?php if ($mail->textHtml == "") { ?>
                <p><?php echo $mail->textPlain; ?></p>
            <?php } else { ?>
                <p><?php echo $mail->textHtml; ?></p>
    <?php } ?>
        </div>
        <br><br>
    </div>
    <-----TMAILNEXTMAIL----->
    <?php
    $count++;
}
?>