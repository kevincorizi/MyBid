<section id="best_offer">
    <h1 id="best_offer_title">Current best offer</h1>
    <article>
        <?php $auction = get_auctions('SELECT * FROM auction')[0]; ?>
        <p id="best_offer_value"><?php echo $auction->bid; ?>€</p>
        <?php if(!is_null($auction->bidder)): ?>
        <p id="best_offer_bidder">from <span class="bold_text"><?php echo $auction->bidder; ?></span></p>
        <?php endif; ?>
    </article>
    <p id="best_offer_message">Care to beat it? <a onclick="location.href='profile.php'"><span class="bold_text">Place an offer!</span></a></p>
</section>