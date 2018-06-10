<?php if (!empty($_POST)): ?>
        Welcome, <?php echo htmlspecialchars($_POST["name"]); ?>!<br>
        Your email is <?php echo htmlspecialchars($_POST["email"]); ?>.<br>
    <?php else: ?>
        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
            Name: <input type="text" name="name"><br>
            Email: <input type="text" name="email"><br>
            <input type="submit">
        </form>
    <?php endif; ?>