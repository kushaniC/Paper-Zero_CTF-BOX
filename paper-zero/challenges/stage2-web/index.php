<?php
require __DIR__ . "/config.php.bak";
$flag = "LST{w3b_4dm1n_l34k3d_cr3ds}";
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["u"] ?? "";
    $pass = $_POST["p"] ?? "";
    if (hash_equals($admin_user, $user) && hash_equals($admin_pass, $pass)) {
        $message = "<p class=\"pz-message success\">Login successful. Flag: <code>" . htmlspecialchars($flag, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "</code></p>";
    } else {
        $message = "<p class=\"pz-message error\">Invalid username or password.</p>";
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Legacy Staff Portal | Paper Zero</title><link rel="stylesheet" href="paper-zero.css"></head>
<body><main class="pz-shell"><div class="pz-topline"><span>Paper Zero // Internal Systems</span><span class="pz-status"><i class="pz-dot"></i> Legacy endpoint</span></div><section class="pz-panel"><p class="pz-kicker">Stage 02 · Web Security</p><h1>Legacy Staff Portal</h1><p class="pz-copy">This retired portal is still connected to the staff system. Investigate the exposed interface and forgotten deployment artifacts.</p><hr class="pz-rule"><form class="pz-form" method="post" action=""><label class="pz-field">Username<input name="u" autocomplete="username" required></label><label class="pz-field">Password<input name="p" type="password" autocomplete="current-password" required></label><div><button type="submit">Authenticate</button></div></form><?= $message ?><div class="pz-evidence"><span class="pz-evidence-label">System artifact</span><a href="config.php.bak">Inspect legacy configuration backup</a></div></section><p class="pz-foot">Restricted system // Access attempts are logged</p></main></body></html>