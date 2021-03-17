<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

declare(strict_types=1);

foreach (
    [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../autoload.php',
    ] as $file
) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}
