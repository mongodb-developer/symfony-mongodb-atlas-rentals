<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';
date_default_timezone_set('GMT');

return static function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
