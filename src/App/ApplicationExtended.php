<?php

namespace App;

use Illuminate\Foundation\Application;

class ApplicationExtended extends Application
{
    public function path($path = '') : string
    {
        $appPath = $this->appPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'App';

        return $appPath . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }
}
