<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $receiver_id = (int)$_POST['receiver_id'];
    $text = trim($_POST['message_content']);

    if (!empty($text) && $receiver_id > 0) {
        $safe_text = safe($text);
        $sql_msg = "INSERT INTO MESSAGE (SenderID, ReceiverID, MessageContent) 
        VALUES ({$_SESSION['user_id']}, $receiver_id, '$safe_text')";
        $conn->query($sql_msg);
    }
}

if (!isset($_SESSION['user_id'])) {
?>
<div class='alert-error'>Please login to access messages.</div>
<?php
return;
}
if (!empty($alert_message)) {
    echo $alert_message;
}
?>
<h2>Messages</h2>

<?php
if (isset($_GET['kw'])) {
    $keyword = trim($_GET['kw']);
} else {
    $keyword = '';
}
$sql_contacts = "SELECT UserID, Title, Name, Role FROM USER WHERE UserID != {$_SESSION['user_id']} AND Role != 'Admin'";

if ($keyword != '') {
    $safe_keyword = safe($keyword);
    $sql_contacts .= " AND Name LIKE '%$safe_keyword%'";
}

$sql_contacts .= " ORDER BY Name ASC";
$available_contacts = fetch_rows($conn->query($sql_contacts));

if (isset($_GET['chat_with'])) {
    $contact_id = (int)$_GET['chat_with'];
} else {
    if (isset($available_contacts[0]['UserID'])) {
        $contact_id = $available_contacts[0]['UserID'];
    } else {
        $contact_id = 0;
    }
}

$sql_messages = "
    SELECT m.SenderID, m.ReceiverID, m.MessageContent, u.Name AS SenderName, u.Title AS SenderTitle 
    FROM MESSAGE m 
    JOIN USER u ON m.SenderID = u.UserID 
    WHERE (m.SenderID = {$_SESSION['user_id']} AND m.ReceiverID = $contact_id) 
       OR (m.SenderID = $contact_id AND m.ReceiverID = {$_SESSION['user_id']}) 
    ORDER BY m.Timestamp ASC
";
$messages = fetch_rows($conn->query($sql_messages));
?>

<div class='chat-layout'>
    <div class='chat-sidebar'>
    <h3 style='margin-top:0;'>Contacts</h3>

    <form method='GET' action='' style='display:flex; gap:0.3rem; margin-bottom:0.5rem;'>
      <input type='hidden' name='page' value='messages'>
      <input type='text' name='kw' value='<?= $keyword ?>' placeholder='Search name...' style='font-size:0.85rem; padding:0.35rem;'>
      <button type='submit' class='btn-primary' style='font-size:0.85rem; padding:0.35rem 0.6rem;'>Go</button>
    </form>

    <div class='contact-box-container'>
        <?php if (count($available_contacts) == 0): ?>
            <p style='font-size:0.85rem;'>No contacts found.</p>
        <?php else: ?>
        <?php foreach ($available_contacts as $contact): ?>
            <?php
                $active_class = ($contact['UserID'] == $contact_id) ? 'active-contact' : '';
                $cname = !empty($contact['Title']) ? $contact['Title'] . " " . $contact['Name'] : $contact['Name'];
                ?>
            <a href='?page=messages&chat_with=<?= $contact['UserID'] ?>&kw=<?= urlencode($keyword) ?>' class='contact-card <?= $active_class ?>'>
                <div><?= $cname ?></div>
                <div style='font-size:0.8rem; opacity:0.8;'><?= $contact['Role'] ?></div>
            </a>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
  </div>

  <div class='chat-main'>
    <div class='msg-box'>
        <?php if (empty($messages)): ?>
            <p>No messages yet.</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php
                    $class = ($msg['SenderID'] == $_SESSION['user_id']) ? "msg-sent" : "msg-received";
                    $sender = !empty($msg['SenderTitle']) ? $msg['SenderTitle'] . " " . $msg['SenderName'] : $msg['SenderName'];
                    ?>
                <div class='msg-bubble <?= $class ?>'>
                    <strong><?= $sender ?></strong><br>
                    <?= $msg['MessageContent'] ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method='POST' action='?page=messages&chat_with=<?= $contact_id ?>' style='display:flex; gap:0.5rem;'>
        <input type='hidden' name='action' value='send_message'>
        <input type='hidden' name='receiver_id' value='<?= $contact_id ?>'>
        <input type='text' name='message_content' placeholder='Type message...' required>
        <button type='submit' class='btn-primary'>SEND</button>
    </form>
  </div>
</div>