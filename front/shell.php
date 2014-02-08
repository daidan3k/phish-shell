<?php
/**
 * Handles the /shell.do url.
 *
 * PHP version 5
 *
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category  Web
 * @package   Phish
 * @author    Sebastian Kreft
 * @author    Google AppEngine
 * @copyright 2014 Sebastian Kreft
 * @copyright 2007 Google Inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 * @link      http://github.com/sk-/phish-shell
 */

require 'vendor/autoload.php';

use \Phish\Phish\Shell;

/**
 * Handler to catch fatal errors (like function not defined) and print them
 * nicely.
 *
 * @return void
 */
function shutdownHandler()
{
    $error = Shell::getFatalError();
    if ($error !== null) {
        echo json_encode(array('r' => $error));
    }
}

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION["shell"])) {
    $_SESSION["shell"] = new Shell();
}

if (isset($_SESSION['token']) && ($_GET['token'] === $_SESSION['token'])) {
    error_reporting(0);
    register_shutdown_function('shutdownHandler');
    echo json_encode(
        array('r' => $_SESSION["shell"]->execute($_GET["statement"]))
    );
} elseif (!isset($_SESSION['token'])) {
    syslog(LOG_ERR, 'Missing session token');
    echo json_encode(
        array('r' => 'Session token missing - Please reset your session.')
    );
} else {
    syslog(LOG_ERR, 'Mismatch session token.');
    echo json_encode(
        array('r' => 'Invalid session token - Please reset your session.')
    );
}
