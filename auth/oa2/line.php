<?php
/*
    This script handles all stages of LINE authentication.
*/

require_once("../../includes/lib/global.php");
__require("config");
__require("auth");

$service = "line";

if (!Auth::isProviderEnabled($service)) {
    header("HTTP/1.1 307 Temporary Redirect");
    header("Location: ".Config::getEndpointUri("/auth/login.php"));
    exit;
}

/*
    Configure the OAuth2 client with the client ID and secret, as well as a
    redirect URI, authorization URI and token endpoint. Request that the
    provider redirects the user back to this script, where stage II of the
    authentication phase can be performed. Stage II makes an authenticated call
    to `identEndpoint` to identify the user.

    We also add a list of parameters including a CSRF state parameter and
    authorization scopes for the provider.
*/

__require("vendor/oauth2");
$opts = array(
    "clientId"      => Config::get("auth/provider/{$service}/channel-id")->value(),
    "clientSecret"  => Config::get("auth/provider/{$service}/channel-secret")->value(),
    "clientAuth"    => OAuth2\Client::AUTH_TYPE_URI,
    "redirectUri"   => Config::getEndpointUri("/auth/oa2/{$service}.php"),
    "authEndpoint"  => "https://access.line.me/oauth2/v2.1/authorize",
    "tokenEndpoint" => "https://api.line.me/oauth2/v2.1/token",
    "identEndpoint" => "https://api.line.me/v2/profile",
    "params" => array(
        "scope"     => "profile",
        "state"     => $state = bin2hex(openssl_random_pseudo_bytes(16))
    )
);

/*
    Invoke the OAuth2 processing script to do OAuth2 handshake and
    authentication on behalf of this script. The procedure is the same for all
    OAuth2 providers, hence the OAuth2 handshake code is centralized to a single
    "oauth2-proc" module.
*/
include(__DIR__."/../../includes/auth/oauth2-proc.php");

$approved = Auth::setAuthenticatedSession(
    "{$service}:".$user["userId"],
    Config::get("auth/session-length")->value(),
    $user["displayName"],
    $user["displayName"]
);
header("HTTP/1.1 303 See Other");

/*
    Unset cookies as they are no longer required.
*/
setcookie("oa2-{$service}-state", "", time() - 3600, strtok($_SERVER["REQUEST_URI"], "?"));
setcookie("oa2-after-auth", "", time() - 3600, strtok($_SERVER["REQUEST_URI"], "?"));

/*
    Unapproved users should be redirected to a page explaining that
    their account has not yet been approved, and that they should
    contact an administrator to approve their account.
*/
if ($approved) {
    header("Location: ".Config::getEndpointUri($continueUrl));
} else {
    header("Location: ".Config::getEndpointUri("/auth/approval.php"));
}
exit;

?>
