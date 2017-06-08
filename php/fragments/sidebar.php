<?php

?>
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
        if(!is_logged())
            echo '<a onclick="location.href=\'auth.php\'"><li>Login</li></a>';
        else
            echo '<li>Logout</li>';
        ?>
    </ul>
</aside>