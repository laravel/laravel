<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $today = Carbon::today();

        // Stat cards
        $totalProducts     = Product::count();
        $totalUsers        = User::where('role', '!=', 'admin')->count();
        $totalOrders       = Order::count();
        $totalTransactions = Order::whereIn('status', ['completed', 'confirmed'])->count();

        // Reservation status breakdown
        $pendingCount   = Order::where('status', 'pending')->count();
        $confirmedCount = Order::where('status', 'confirmed')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();

        // Daily sales (today's completed orders)
        $dailySales = Order::where('status', 'completed')
            ->whereDate('updated_at', $today)
            ->sum('total_amount');
$dailyOrderCount = Order::where('status', 'completed')
            ->whereDate('updated_at', $today)
            ->count();
    
        // Monthly sales — last 6 months (para sa chart)
        $monthlySales = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $monthlySales->push([
                'label'  => $month->format('M'),
                'amount' => Order::where('status', 'completed')
    ->whereYear('updated_at', $month->year)
    ->whereMonth('updated_at', $month->month)
    ->sum('total_amount'),
            ]);
        }

        // Low stock — products na <= 10 kg na lang
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Recent users — pinakabagong nag-register
        $recentUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get();

        // Recent reservations/orders
        $recentReservations = Order::with('items.product')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'totalOrders',
            'totalTransactions',
            'pendingCount',
            'confirmedCount',
            'completedCount',
            'cancelledCount',
            'dailySales',
            'dailyOrderCount',
            'monthlySales',
            'lowStockProducts',
            'recentUsers',
            'recentReservations',
        ));
    }
    public function dashboard()
    {
        return $this->index();
    }

    public function users()
    {
        $this->checkAdmin();
        $users = User::where('role', '!=', 'admin')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function deleteUser($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
    public function dashboardCheck(Request $request)
    {
        $lastId        = (int) $request->query('last_id', 0);
$lastUpdatedAt = $request->query('last_updated_at', null);
$latest        = Order::latest('updated_at')->first();

$hasNew = $latest && (
    $latest->id > $lastId ||
    ($lastUpdatedAt && $latest->updated_at->toISOString() !== $lastUpdatedAt)
);

        $today = Carbon::today();

        $recentReservations = Order::with('items.product', 'user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($order) => [
                'id'       => $order->id,
                'customer' => $order->user->name ?? 'N/A',
                'products' => $order->items->map(fn($i) => $i->product->name ?? 'N/A')->join(', '),
                'quantity' => $order->items->sum('quantity'),
                'date'     => $order->created_at->format('M d, Y'),
                'status'   => $order->status,
            ]);

        return response()->json([
            'hasNew'             => true,
            'latestId'           => $latest->id,
            'latestUpdatedAt'    => $latest->updated_at->toISOString(),
            'totalProducts'      => Product::count(),
            'totalUsers'         => User::where('role', '!=', 'admin')->count(),
            'totalOrders'        => Order::count(),
            'totalTransactions'  => Order::whereIn('status', ['completed', 'confirmed'])->count(),
            'dailySales'         => Order::where('status', 'completed')
                                        ->whereDate('updated_at', $today)
                                        ->sum('total_amount'),
            'dailyOrderCount'    => Order::where('status', 'completed')
                                        ->whereDate('updated_at', $today)
                                        ->count(),
            'pendingCount'       => Order::where('status', 'pending')->count(),
            'confirmedCount'     => Order::where('status', 'confirmed')->count(),
            'completedCount'     => Order::where('status', 'completed')->count(),
            'cancelledCount'     => Order::where('status', 'cancelled')->count(),
            'monthlySales'       => $this->getMonthlySales(),
            'recentReservations' => $recentReservations,
        ]);
    }

    private function getMonthlySales()
    {
        $sales = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $sales->push([
                'label'  => $month->format('M'),
                'amount' => Order::where('status', 'completed')
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->sum('total_amount'),
            ]);
        }
        return $sales;
    }
}