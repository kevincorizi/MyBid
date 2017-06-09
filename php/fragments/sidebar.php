<?php

?>
<aside>
    <ul id="app_actions">
        <?php if(is_logged()): ?>
        <li>Your profile</li>
        <li>Place an offer</li>
        <?php endif; ?>
    </ul>
    <ul id="user_actions">
        <?php
        if(!is_logged())
            echo '<a onclick="location.href=\'auth.php\'"><li>Login</li></a>';
        else
            echo '<a onclick="location.href=\'logout.php\'"><li>Logout</li></a>';
        ?>
    </ul>
</aside>