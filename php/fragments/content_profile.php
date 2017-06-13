<?php
    $auction = get_auctions('SELECT * FROM auction;')[0];
    $offers = get_offers("SELECT * FROM offer WHERE user='".$_SESSION['username']."' AND auction=".$auction->id.";");
    $offer = count($offers) > 0 ? $offers[0] : null;
    $notifications = get_notifications("SELECT * FROM notifications WHERE user='".$_SESSION['username']."'");
?>

<section>
    <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>
    <article>
        <?php if(is_null($offer)): // The user did not place a bid yet ?>
        <p>It looks like you don't have any bid placed yet :(</p>
        <p>Do you want to add a new bid now?</p>
        <?php else: ?>
        <p>Your current bid for the auction <?php echo $auction->name; ?> is <span id="current_thri_value"><?php echo $offer->value; ?></span>€.</p>
        <p>You placed this bid on <span id="current_thri_date"><?php echo toDate($offer->timestamp, 'long'); ?></span>.</p>
        <p>Do you want to update it?</p>
        <?php endif; ?>
        <button id="show_thri_popup" class="button large_button">Click here!</button>
    </article>
    <article>
    <?php if(count($notifications) == 1): ?>
        <h2>You have 1 notification</h2>
    <?php else: ?>
        <h2>You have <?php echo count($notifications); ?> notifications</h2>
    <?php endif; ?>
    <?php foreach($notifications as $notification): ?>
        <div class="message_container error_message_container">
            <p class="message_header"><?php $notification->type ?></p>
            <p class="message_text"><?php $notification->message ?></p>
            <button type="button" class="button message_close"></button>
        </div>
    <?php endforeach; ?>
    </article>
</section>

<div class="overlay">
    <div id="new_thri_form" title="New bid">
        <p class="validate_tips">Please enter your new bid.</p>
        <form name="auction_<?php echo $auction->id; ?>">
            <fieldset>
                <label for="thri_value">Value</label>
                <input type="number" name="thri_value" id="thri_value" value="<?php echo $auction->bid + 0.01 ?>" step="0.01">
				
				<div class="message_container confirm_message_container">
					<p class="message_header">sdfbg</p>
					<p class="message_text">wer</p>
				</div>
				
                <button id="update_thri_button" type="button" class="button large_button">Confirm</button>
                <button id="cancel_thri_button" type="button" class="button large_button">Close</button>
            </fieldset>
        </form>
    </div>
</div>