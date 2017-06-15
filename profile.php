<?php
    require_once 'config.php';
    require_once DIR_PHP_FUNCTIONS . 'force_https.php';
    require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'lib.php';

    start_session();
    if (!is_logged()) {
        redirect('auth.php');
    }

    $conn = new DatabaseInterface();
    $username = $_SESSION['username'];

    $auction = get_auctions($conn->query('SELECT * FROM auction;'))[0];
    $offers = get_offers($conn->query("SELECT * FROM offer WHERE user='$username' AND auction=$auction->id ;"));
    $offer = count($offers) > 0 ? $offers[0] : null;
    $notifications = get_notifications($conn->query("SELECT * FROM notifications WHERE user='$username';"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PoliBid</title>
    <?php require_once('./php/fragments/head_tags.php'); ?>
</head>
<body onload="onload_handler()">
<?php require_once('./php/fragments/header.php'); ?>
<main>
    <?php require_once('./php/fragments/sidebar.php'); ?>
    <section>
        <h1>Hello, <?php echo $username; ?>!</h1>
        <article>
            <?php if (is_null($offer)): // The user did not place a bid yet ?>
                <h2 id="current_thri_value">It looks like you don't have any bid placed yet :(</h2>
                <p>Do you want to add a new bid now?</p>
            <?php else: ?>
                <h2 id="current_thri_value">Your current bid for the auction <?php echo $auction->name; ?> is <?php echo $offer->value; ?>€.</h2>
                <p>You placed this bid on <span
                            id="current_thri_date"><?php echo toDate($offer->timestamp, 'long'); ?></span>.</p>
                <p>Do you want to update it?</p>
            <?php endif; ?>
            <button id="show_thri_popup" class="button large_button">Click here!</button>
        </article>
        <article>
            <?php if (count($notifications) == 1): ?>
                <h2>You have <span id="notification_count">1</span> notification</h2>
            <?php else: ?>
                <h2>You have <span id="notification_count"><?php echo count($notifications); ?></span> notifications</h2>
            <?php endif; ?>
            <?php foreach ($notifications as $notification): ?>
                <div id="notification_<?php echo $notification->id; ?>" class="message_container notification_message_container">
                    <p class="message_header"><?php echo $notification->type ?></p>
                    <p class="message_text"><?php echo $notification->message ?></p>
                    <button type="button" class="button message_close"></button>
                </div>
            <?php endforeach; ?>
        </article>
    </section>

    <div class="overlay">
        <div class="overlay_content" id="new_thri_form" title="New bid">
            <p class="validate_tips">Please enter your new bid.</p>
            <form name="auction_<?php echo $auction->id; ?>">
                <fieldset>
                    <label for="thri_value">Value</label>
                    <input type="number" name="thri_value" id="thri_value" value="<?php echo $auction->bid + 0.01 ?>" step="0.01"
                           onchange="if( this.value.length == 1 ) this.value=this.value + '.00'; if(this.value.split('.')[1].length == 1) this.value = this.value + '0';">
                    <button id="update_thri_button" type="button" class="button large_button">Confirm</button>
                    <button id="cancel_thri_button" type="button" class="button large_button">Close</button>
                </fieldset>
            </form>
        </div>
    </div>
</main>
<?php require_once('./php/fragments/footer.php'); ?>
</body>
</html>