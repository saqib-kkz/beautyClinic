<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\ClinicProfile;

class ClinicComposer
{
    public function compose(View $view)
    {
        $clinic = ClinicProfile::getClinicInfo();
        $view->with('clinic', $clinic);
    }
}