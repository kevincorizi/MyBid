<aside>
    <ul id="app_actions">
        <?php if(is_logged()): ?>
        <a onclick="location.href='profile.php'"><li>Place an offer</li></a>
        <a onclick="location.href='profile.php'"><li>Notifications</li></a>
        <?php endif; ?>
    </ul>
    <ul id="user_actions">
        <?php if(!is_logged()): ?>
        <a onclick="location.href='auth.php'"><li>Login</li></a>
        <?php else: ?>
        <a onclick="location.href='logout.php'"><li>Logout</li></a>
        <?php endif; ?>
    </ul>
</aside>