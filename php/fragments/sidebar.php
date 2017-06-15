<aside>
    <ul id="app_actions">
        <?php if (is_logged()): ?>
            <?php if(strpos($_SERVER['SCRIPT_NAME'], 'profile') === false): ?>
                <li onclick="location.href='profile.php'">
                    <div class="menu_item">
                        <img src="./assets/icons/account.png" alt="Profile">
                        <span class="menu_item_text">Your profile</span>
                    </div>
                </li>
            <?php else: ?>
                <li onclick="location.href='index.php'">
                    <div class="menu_item">
                        <img src="./assets/icons/home.png" alt="Home">
                        <span class="menu_item_text">Home</span>
                    </div>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <ul id="user_actions">
        <?php if (!is_logged()): ?>
            <li onclick="location.href='auth.php'">
                <div class="menu_item">
                    <img src="./assets/icons/account.png" alt="Login">
                    <span class="menu_item_text">Login</span>
                </div>
            </li>
        <?php else: ?>
            <li onclick="location.href='logout.php'">
                <div class="menu_item">
                    <img src="./assets/icons/exit.png" alt="Logout">
                    <span class="menu_item_text">Logout</span>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</aside>