<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class OutcomeCard extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = \App\Models\Outcome::count();

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-double-left',
            'title'  => "Накладных на отпуск на сторону: {$count}",
            'text'   => "",
            'button' => [
                'text' => __('Все накладные'),
                'link' => route('voyager.outcomes.index'),
            ],
            'image' => voyager_asset(''),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return true;
    }
}
