<?php
    require_once 'config.php';
    require_once DIR_PHP_FUNCTIONS.'session_manager.php';
    require_once DIR_PHP_FUNCTIONS.'db_manager.php';
    require_once DIR_PHP_FUNCTIONS.'lib.php';

    start_session();

    $conn = new DatabaseInterface();
    $auction = get_auctions($conn->query('SELECT * FROM auction'))[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>PoliBid</title>
    <?php require_once('./php/fragments/head_tags.php'); ?>
</head>
<body onload="onload_handler()">
    <?php require_once('./php/fragments/header.php'); ?>
    <main>
        <?php require_once('./php/fragments/sidebar.php'); ?>
        <section id="best_offer">
            <h1 id="best_offer_title">Current best offer</h1>
            <article>
                <p id="best_offer_value"><?php echo $auction->bid; ?>€</p>
                <?php if(!is_null($auction->bidder)): ?>
                    <?php if(isset($_SESSION['username']) && $auction->bidder == $_SESSION['username']): ?>
                        <p id="best_offer_bidder"><span class="bold_text">You are the current winner!</span></p>
                    <?php else: ?>
                        <p id="best_offer_bidder">from <span class="bold_text"><?php echo $auction->bidder; ?></span></p>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
            <?php if(isset($_SESSION['username']) && $auction->bidder != $_SESSION['username']): ?>
                <p id="best_offer_message">Care to beat it? <a onclick="location.href='profile.php'"><span class="bold_text">Place an offer!</span></a></p>
            <?php endif; ?>
        </section>
    </main>
    <?php require_once('./php/fragments/footer.php'); ?>
</body>
</html>