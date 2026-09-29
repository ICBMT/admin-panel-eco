<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard (Spark Admin UI).
     */
    public function dashboard(): View
    {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $customersCount = User::where('is_admin', false)->count();

        $totalRevenue = (float) Order::where('status', '!=', OrderStatus::Cancelled->value)->sum('total');
        $totalCancelled = (float) Order::where('status', OrderStatus::Cancelled->value)->sum('total');

        // Revenue trend: last 7 days compared with the 7 days before that.
        $revenueLast7 = $this->revenueBetween(now()->subDays(6)->startOfDay(), now()->endOfDay());
        $revenuePrev7 = $this->revenueBetween(now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay());
        $revenueTrend = $this->trendPercentage($revenueLast7, $revenuePrev7);

        $cancelledLast7 = $this->cancelledValueBetween(now()->subDays(6)->startOfDay(), now()->endOfDay());
        $cancelledPrev7 = $this->cancelledValueBetween(now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay());
        $cancelledTrend = $this->trendPercentage($cancelledLast7, $cancelledPrev7);

        // Revenue chart: income vs cancelled value over the last 8 days.
        $revenueChart = $this->revenueChartSeries();

        // Sparklines: last 12 days of revenue and cancelled order value.
        $incomeSparkline = $this->dailySeries(now()->subDays(11)->startOfDay(), now()->endOfDay())
            ->map(fn (Collection $orders) => round($this->sumRevenue($orders), 2))
            ->values()
            ->all();

        $returnSparkline = $this->dailySeries(now()->subDays(11)->startOfDay(), now()->endOfDay())
            ->map(fn (Collection $orders) => round($this->sumCancelled($orders), 2))
            ->values()
            ->all();

        // Sales split for the donut chart.
        $codRevenue = (float) Order::where('status', '!=', OrderStatus::Cancelled->value)
            ->where('payment_method', 'cod')
            ->sum('total');
        $cardRevenue = (float) Order::where('status', '!=', OrderStatus::Cancelled->value)
            ->where('payment_method', 'stripe')
            ->sum('total');

        $recentOrders = Order::with('user')->latest()->limit(5)->get();

        // Product overview rows.
        $unitsSold = (int) OrderItem::sum('quantity');
        $unitsInStock = (int) Product::sum('stock');
        $stockedTotal = $unitsSold + $unitsInStock;
        $lowStockCount = Product::whereBetween('stock', [1, 4])->count();
        $outOfStockCount = Product::where('stock', 0)->count();
        $pendingOrders = Order::where('status', OrderStatus::Pending->value)->count();
        $deliveredOrders = Order::where('status', OrderStatus::Delivered->value)->count();

        return view('admin.dashboard', [
            'ordersCount' => $ordersCount,
            'productsCount' => $productsCount,
            'customersCount' => $customersCount,
            'totalRevenue' => $totalRevenue,
            'revenueTrend' => $revenueTrend,
            'totalCancelled' => $totalCancelled,
            'cancelledTrend' => $cancelledTrend,
            'revenueLast7' => $revenueLast7,
            'chart' => $revenueChart,
            'incomeSparkline' => $incomeSparkline,
            'returnSparkline' => $returnSparkline,
            'donut' => [
                'series' => [
                    round($codRevenue, 2),
                    round($cardRevenue, 2),
                    round($totalCancelled, 2),
                ],
                'total' => '$' . number_format($codRevenue + $cardRevenue + $totalCancelled, 0),
            ],
            'recentOrders' => $recentOrders,
            'overview' => [
                [
                    'label' => 'Products Sold',
                    'value' => number_format($unitsSold),
                    'percent' => $this->percentOf($unitsSold, $stockedTotal),
                    'bar' => 'bg-lime-accent',
                ],
                [
                    'label' => 'Units In Stock',
                    'value' => number_format($unitsInStock),
                    'percent' => $this->percentOf($unitsInStock, $stockedTotal),
                    'bar' => 'bg-lime-accent opacity-50',
                ],
                [
                    'label' => 'Low Stock Products',
                    'value' => number_format($lowStockCount),
                    'percent' => $this->percentOf($lowStockCount, $productsCount),
                    'bar' => 'bg-lime-accent',
                ],
                [
                    'label' => 'Out of Stock',
                    'value' => number_format($outOfStockCount),
                    'percent' => $this->percentOf($outOfStockCount, $productsCount),
                    'bar' => 'bg-brand-orange',
                ],
                [
                    'label' => 'Pending Orders',
                    'value' => number_format($pendingOrders),
                    'percent' => $this->percentOf($pendingOrders, $ordersCount),
                    'bar' => 'bg-lime-accent opacity-50',
                ],
                [
                    'label' => 'Delivered Orders',
                    'value' => number_format($deliveredOrders),
                    'percent' => $this->percentOf($deliveredOrders, $ordersCount),
                    'bar' => 'bg-lime-accent',
                ],
            ],
        ]);
    }

    /**
     * Sum of non-cancelled order totals between two dates.
     */
    private function revenueBetween(CarbonInterface $start, CarbonInterface $end): float
    {
        return (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', OrderStatus::Cancelled->value)
            ->sum('total');
    }

    /**
     * Sum of cancelled order totals between two dates.
     */
    private function cancelledValueBetween(CarbonInterface $start, CarbonInterface $end): float
    {
        return (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', OrderStatus::Cancelled->value)
            ->sum('total');
    }

    /**
     * Group orders created between two dates into per-day collections
     * (oldest day first).
     *
     * @return Collection<string, Collection<int, Order>>
     */
    private function dailySeries(CarbonInterface $start, CarbonInterface $end): Collection
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get(['total', 'status', 'created_at']);

        $days = new Collection;

        for ($day = $start->copy()->startOfDay(); $day->lte($end->copy()->startOfDay()); $day = $day->addDay()) {
            $days->put($day->format('Y-m-d'), new Collection);
        }

        foreach ($orders as $order) {
            $key = $order->created_at->format('Y-m-d');

            $days->put(
                $key,
                $days->get($key, new Collection)->push($order),
            );
        }

        return $days;
    }

    /**
     * Revenue chart data for the last 8 days.
     *
     * @return array{categories: list<string>, income: list<float>, cancelled: list<float>}
     */
    private function revenueChartSeries(): array
    {
        $byDay = Order::where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->get(['total', 'status', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'));

        $categories = [];
        $income = [];
        $cancelled = [];

        for ($i = 7; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $orders = $byDay->get($day->format('Y-m-d'), new Collection);

            $categories[] = $day->format('M j');
            $income[] = round($this->sumRevenue($orders), 2);
            $cancelled[] = round($this->sumCancelled($orders), 2);
        }

        return [
            'categories' => $categories,
            'income' => $income,
            'cancelled' => $cancelled,
        ];
    }

    /**
     * Sum non-cancelled revenue from an in-memory set of orders.
     *
     * @param  Collection<int, Order>  $orders
     */
    private function sumRevenue(Collection $orders): float
    {
        return (float) $orders
            ->where('status', '!=', OrderStatus::Cancelled)
            ->sum('total');
    }

    /**
     * Sum cancelled order value from an in-memory set of orders.
     *
     * @param  Collection<int, Order>  $orders
     */
    private function sumCancelled(Collection $orders): float
    {
        return (float) $orders
            ->where('status', OrderStatus::Cancelled)
            ->sum('total');
    }

    /**
     * Percentage change between two periods.
     */
    private function trendPercentage(float $current, float $previous): float
    {
        if ($previous <= 0.0) {
            return $current > 0.0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Safe percentage used by the product overview progress bars.
     */
    private function percentOf(int $value, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return (int) min(100, round(($value / $total) * 100));
    }
}
