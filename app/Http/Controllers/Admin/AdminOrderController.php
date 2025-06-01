<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'orderItems.product'])->latest()->get();

        // Calculate statistics
        $completedOrders = Order::where('status', 'completed')->with('orderItems')->get();
        $totalSales = 0;
        foreach ($completedOrders as $order) {
            foreach ($order->orderItems as $item) {
                $totalSales += $item->quantity * $item->price;
            }
        }
        $completedOrdersCount = $completedOrders->count();

        // Get period from request, default to monthly
        $period = $request->get('period', 'monthly');
        
        // Calculate sales data for chart
        $salesData = [];
        $labels = [];
        
        switch ($period) {
            case 'daily':
                // Get last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $daySales = Order::where('status', 'completed')
                        ->whereDate('created_at', $date)
                        ->with('orderItems')
                        ->get()
                        ->sum(function ($order) {
                            return $order->orderItems->sum(function ($item) {
                                return $item->quantity * $item->price;
                            });
                        });
                    
                    $salesData[] = $daySales;
                    $labels[] = $date->format('d M');
                }
                break;
                
            case 'monthly':
                // Get last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $monthSales = Order::where('status', 'completed')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->with('orderItems')
                        ->get()
                        ->sum(function ($order) {
                            return $order->orderItems->sum(function ($item) {
                                return $item->quantity * $item->price;
                            });
                        });
                    
                    $salesData[] = $monthSales;
                    $labels[] = $date->format('M Y');
                }
                break;
                
            case 'yearly':
                // Get last 5 years
                for ($i = 4; $i >= 0; $i--) {
                    $date = now()->subYears($i);
                    $yearSales = Order::where('status', 'completed')
                        ->whereYear('created_at', $date->year)
                        ->with('orderItems')
                        ->get()
                        ->sum(function ($order) {
                            return $order->orderItems->sum(function ($item) {
                                return $item->quantity * $item->price;
                            });
                        });
                    
                    $salesData[] = $yearSales;
                    $labels[] = $date->year;
                }
                break;
        }

        return view('Admin.orders.index', compact(
            'orders', 
            'totalSales', 
            'completedOrdersCount',
            'salesData',
            'labels',
            'period'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('Admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
