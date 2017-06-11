<?php
    $auction = get_auctions('SELECT * FROM auction;')[0];
    $offers = get_offers("SELECT * FROM offer WHERE user='".$_SESSION['username']."' AND auction=".$auction->id.";");
    $offer = count($offers) > 0 ? $offers[0] : null;
?>

<section>
    <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>
    <article>
        <?php if(is_null($offer)): // The user did not place a bid yet ?>
        <p>It looks like you don't have any bid placed yet :(</p>
        <p>Do you want to add a new bid now? <span onclick="show_thri_popup(<?php echo $auction->id ?>);">Click here!</span></p>
        <?php else: ?>
        <p>Your current bid for the auction <?php echo $auction->name; ?> is <?php echo $offer->value; ?>.</p>
        <p>You placed this bid on <?php echo toDate($offer->timestamp, 'long'); ?>.</p>
        <p>Do you want to update it? <span onclick="show_thri_popup(<?php echo $auction->id ?>);">Click here!</span></p>
        <?php endif; ?>
    </article>
</section>