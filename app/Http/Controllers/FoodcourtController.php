<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\TransaksiFoodcourt;
use App\Models\TransaksiFoodcourtItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodcourtController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $omsetHariIni = (float) TransaksiFoodcourt::whereDate('tanggal', $today)->sum('total');
        $trxHariIni = TransaksiFoodcourt::whereDate('tanggal', $today)->count();
        $menuAktif = Menu::where('aktif', true)->count();
        $omset30Hari = (float) TransaksiFoodcourt::where('tanggal', '>=', now()->subDays(30)->toDateString())->sum('total');

        $stats = [
            'omset_hari_ini' => $omsetHariIni,
            'trx_hari_ini' => $trxHariIni,
            'menu_aktif' => $menuAktif,
            'omset_30_hari' => $omset30Hari,
        ];

        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $labels = $days->map(fn ($d) => \Carbon\Carbon::parse($d)->format('D'))->toArray();
        $penjualanData = $days->map(fn ($d) => (float) TransaksiFoodcourt::whereDate('tanggal', $d)->sum('total'))->toArray();
        $penjualanHarian = ['labels' => $labels, 'data' => $penjualanData];

        $recentItems = TransaksiFoodcourtItem::with('menu')
            ->whereHas('transaksi', fn ($q) => $q->where('tanggal', '>=', now()->subDays(30)->toDateString()))
            ->get();
        $totalsByMenu = [];
        foreach ($recentItems as $it) {
            $name = $it->menu->nama ?? 'Menu';
            $totalsByMenu[$name] = ($totalsByMenu[$name] ?? 0) + (int) $it->qty;
        }
        arsort($totalsByMenu);
        $menuTerlaris = [
            'labels' => array_keys($totalsByMenu),
            'data' => array_values($totalsByMenu),
        ];

        return view('foodcourt.dashboard', compact('stats', 'penjualanHarian', 'menuTerlaris'));
    }

    public function laporan(Request $request)
    {
        $awal = $request->input('awal', now()->startOfMonth()->toDateString());
        $akhir = $request->input('akhir', now()->toDateString());

        $transaksis = TransaksiFoodcourt::with(['items.menu', 'user'])
            ->whereBetween('tanggal', [$awal.' 00:00:00', $akhir.' 23:59:59'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $total = (float) TransaksiFoodcourt::whereBetween('tanggal', [$awal.' 00:00:00', $akhir.' 23:59:59'])->sum('total');

        return view('foodcourt.laporan', compact('transaksis', 'awal', 'akhir', 'total'));
    }

    public function index()
    {
        $transaksis = TransaksiFoodcourt::with(['items.menu', 'user'])->latest()->paginate(15);
        $menus = Menu::where('aktif', true)->orderBy('nama')->get();

        return view('foodcourt.index', compact('transaksis', 'menus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'metode' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            $transaksi = TransaksiFoodcourt::create([
                'tanggal' => $data['tanggal'],
                'metode' => $data['metode'],
                'total' => 0,
                'user_id' => auth()->id(),
            ]);

            $total = 0;
            foreach ($data['items'] as $it) {
                $menu = Menu::findOrFail($it['menu_id']);
                $subtotal = $menu->harga * $it['qty'];
                $total += $subtotal;
                TransaksiFoodcourtItem::create([
                    'transaksi_foodcourt_id' => $transaksi->id,
                    'menu_id' => $menu->id,
                    'qty' => $it['qty'],
                    'harga_satuan' => $menu->harga,
                    'subtotal' => $subtotal,
                ]);
            }
            $transaksi->update(['total' => $total]);
        });

        return redirect()->route('foodcourt.index')->with('success', 'Transaksi foodcourt berhasil disimpan.');
    }
}
