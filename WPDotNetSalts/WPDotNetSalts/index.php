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

function generate_salts() {
    $keys = [
        'AUTH_KEY',
        'AUTH_SALT',
        'LOGGED_IN_KEY',
        'LOGGED_IN_SALT',
        'NONCE_KEY',
        'NONCE_SALT',
        'SECURE_AUTH_KEY',
        'SECURE_AUTH_SALT',
    ];
    $values = [];
    $maxlength = array_reduce($keys, function($carry, $key) {
        $len = strlen($key);
        if ($len > $carry) {
            return $len;
        }
        return $carry;
    }, 0);

    foreach ($keys as $key) {
	    $values[$key] = random_str( 64 );
    }

    echo '"SALT": {' . "\n";
    while ($key = array_shift($keys)) {
        echo '	"' . $key . '":';
        for ($i = strlen($key); $i <= $maxlength; $i++) {
            echo ' ';
        }
        echo '"' . $values[$key] . '"';
        if (0 < count($keys)) {
            echo ',';
        }
        echo "\n";
    }
    echo '}';
}

if (isset($_GET['raw'])) {
    header('Content-Type: text/plain', true, 200);
    generate_salts();
} else {
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
        body {
            display: grid;
            height: 100vh;
            margin: 0;
        }
        body > div {
            align-self: center;
            justify-self: center;
            width: min-content;
        }
        h1 {
            margin: 0 0 3rem;
            text-align: center;
        }
        pre {
            background-color: #f0f0f0;
            border: 0.1rem dashed #cccccc;
            max-width: 80vw;
            overflow-x: auto;
            padding: 1rem;
        }
        pre {
            margin: 3rem 0;
        }
        </style>
    </head>
    <body>
        <div>
            <h1>WP.Net Salts generator</h1>

            <p>by <a href="https://diddledani.com/">Dani Llewellyn</a></p>

            <h2>Your salts and keys for WPdotNet appsettings.json are below:</h2>

            <pre><?php generate_salts(); ?></pre>

            <p>These salts are for use with <a href="https://www.wpdotnet.com/">WPdotNet</a> from <a href="https://www.peachpie.io/">PeachPie PHP Compiler for .NET</a> and <a href="https://www.iolevel.com/">IOLevel</a>. Make your own WordPress site running on .NET using the <a href="https://github.com/iolevel/peachpie-wordpress/">WPdotNet boilerplate at GitHub</a>, which is based on the NuGet packages created from the <a href="https://github.com/iolevel/wpdotnet-sdk">WPdotNet SDK on github</a>.</p>
            <p>The source code for this generator is publically accessible at <a href="https://github.com/diddledani/wpdotnet-salts">GitHub</a>.</p>
        </div>
    </body>
</html>
<?php
}