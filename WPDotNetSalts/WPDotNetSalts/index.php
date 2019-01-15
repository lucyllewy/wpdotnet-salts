<?php

header('Cache-control: private, max-age=0, no-store');

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 *
 * Based on code by Scott Arciszewski
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,./;#[]!%^*()-=_+{}:@~?')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

$keys = ['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'];
$values = [];

foreach ($keys as $key) {
	$values[$key] = random_str( 64 );
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>WP.Net Salts generator</h1>

<p>Your salts and keys for appsettings.json are below:</p>

<pre>
"SALT": {
<?php while ($key = array_shift($keys)) : ?>
	"<?php echo $key; ?>": "<?php echo $values[$key]; ?>"<?php if (0 < count($keys)) { echo ','; } ?>

<?php endwhile; ?>
}
</pre>

<p>These salts are for use with <a href="https://www.wpdotnet.com/">WPdotNet</a> from <a href="https://www.peachpie.io/">PeachPie PHP Compiler for .NET</a> and <a href="http://www.iolevel.com/">IOLevel</a>. Make your own WordPress site running on .NET using the <a href="https://github.com/iolevel/peachpie-wordpress/">WPdotNet boilerplate at GitHub</a>, which is based on the NuGet packages created from the <a href="https://github.com/iolevel/wpdotnet-sdk">WPdotNet SDK on github</a>.

</body>
</html>
