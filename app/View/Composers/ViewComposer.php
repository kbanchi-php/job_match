<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ViewComposer
{
    public function compose(View $view)
    {
        foreach (config('fortify.users') as $guard) {
            if (Auth::guard(Str::plural($guard))->check()) {
                $user = Auth::guard(Str::plural($guard))->user();
                $prefix = $guard . '.';
            }
        }

        // nullだった場合は''を代入
        $user = $user ?? '';
        $prefix = $prefix ?? '';

        return $view->with('user', $user)->with('prefix', $prefix);
    }
}
