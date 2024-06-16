<?php

namespace App\Filament\Resources\SaleResource\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use KoalaFacade\FilamentAlertBox\Widgets\AlertBoxWidget;

class SaleLimit extends AlertBoxWidget
{
    protected string|\Closure|null $icon = 'heroicon-o-exclamation-triangle';

    public string $type = 'warning';

    protected string|\Closure|Htmlable|null $label = 'Monthly Limit Reached!';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return string|HtmlString|null
     */
    public function getHelperText(): string|HtmlString|null
    {
        return "You've reached your maximum sales for this month. Please <a href='https://www.facebook.com/stockmanageronline' class='underline'>contact us</a> to discuss upgrading your plan for higher limits.";
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return filament()->getTenant()->hasReachedMaxSales();
    }
}
