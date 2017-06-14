<aside>
    <ul id="app_actions">
        <?php if(is_logged()): ;?>
            <?php if(explode(".", $_SERVER['PHP_SELF'])[0] != '/profile'): ?>
                <a onclick="location.href='profile.php'">
                    <li>
                        <div class="menu_item">
                            <img src="/assets/icons/account.png">
                            <span class="menu_item_text">Your profile</span>
                        </div>
                    </li>
                </a>
            <?php else: ?>
                <a onclick="location.href='index.php'">
                    <li>
                        <div class="menu_item">
                            <img src="/assets /icons/home.png">
                            <span class="menu_item_text">Home</span>
                        </div>
                    </li>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <ul id="user_actions">
        <?php if(!is_logged()): ?>
        <a onclick="location.href='auth.php'">
            <li>
                <div class="menu_item">
                    <img src="/assets/icons/account.png">
                    <span class="menu_item_text">Login</span>
                </div>
            </li>
        </a>
        <?php else: ?>
        <a onclick="location.href='logout.php'">
            <li><div class="menu_item">
                    <img src="/assets/icons/exit.png">
                    <span class="menu_item_text">Logout</span>
                </div>
            </li>
        </a>
        <?php endif; ?>
    </ul>
</aside>