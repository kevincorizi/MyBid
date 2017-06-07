<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/style.css"/>
</head>
<body>
    <header>
        <div id="logo">
            <img src="x">
            MyBid
        </div>
        <div id="slogan">
            <p>the best bidding platform ever</p>
        </div>
    </header>
    <main>
    <aside>
        <ul id="app_actions">
            <?php
                if(true /* IS_LOGGED */)
                    echo '<li>Your profile</li>'
            ?>
            <li>Place an offer</li>
        </ul>
        <ul id="user_actions">
            <?php
            if(true /* IS_LOGGED */)
                echo '<li>Login</li>';
            else
                echo '<li>Logout</li>';
            ?>
        </ul>
    </aside>
    <section id="best_offer">
        <h1 id="best_offer_title">Current best offer</h1>
        <article>
            <p id="best_offer_value">9.99$</p>
            <p id="best_offer_bidder">from <span class="bold_text">il_kappa</span></p>
        </article>
        <p id="best_offer_message">Care to beat him? <span class="bold_text">Place an offer!</span></p>
    </section>
    </main>
</body>
</html>
