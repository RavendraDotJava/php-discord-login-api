<?php

$discord_url = "https://discord.com/api/oauth2/authorize?client_id=1189442130171678761&response_type=code&redirect_uri=https%3A%2F%2Fresolvegroup.in%2Fprojects%2Frust-consolecommunity%2Fprocess-oauth.php&scope=identify+email+guilds";
header("Location: $discord_url");
exit();

?>