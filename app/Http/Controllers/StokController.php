<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokLokasi;
use Illuminate\Http\Request;

class StokController extends Controller
{
    protected array $lokasi = ['daqam', 'food_court', 'kantor'];

    public function index(Request $request)
    {
        $this->syncStokLokasi();

        $search = trim((string) $request->query('q', ''));

        $entries = StokLokasi::with('produk')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama_produk', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('qmas.stok', [
            'entries' => $entries,
            'search' => $search,
            'lokasiList' => $this->lokasi,
        ]);
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'lokasi' => 'required|in:daqam,food_court,kantor',
            'aksi' => 'required|in:tambah,kurang',
            'qty' => 'required|integer|min:1',
        ]);

        $stok = $this->findOrCreate($data['produk_id']);
        $lokasi = $data['lokasi'];

        if ($data['aksi'] === 'tambah') {
            $stok->{$lokasi} += $data['qty'];
        } else {
            $stok->{$lokasi} = max(0, $stok->{$lokasi} - $data['qty']);
        }
        $stok->save();

        $this->syncProdukTotal($stok->produk_id);

        return redirect()->route('qmas.stok.index', $request->only('q'))->with('success', 'Stok berhasil disesuaikan.');
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'asal' => 'required|in:daqam,food_court,kantor',
            'tujuan' => 'required|in:daqam,food_court,kantor|different:asal',
            'qty' => 'required|integer|min:1',
        ]);

        $stok = $this->findOrCreate($data['produk_id']);
        $asal = $data['asal'];
        $tujuan = $data['tujuan'];

        if ($stok->{$asal} < $data['qty']) {
            return redirect()->route('qmas.stok.index', $request->only('q'))
                ->withErrors(['qty' => 'Stok asal tidak mencukupi.']);
        }

        $stok->{$asal} -= $data['qty'];
        $stok->{$tujuan} += $data['qty'];
        $stok->save();

        $this->syncProdukTotal($stok->produk_id);

        return redirect()->route('qmas.stok.index', $request->only('q'))->with('success', 'Stok berhasil dipindahkan.');
    }

    public function inline(Request $request)
    {
        $data = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'lokasi' => 'required|in:daqam,food_court,kantor',
            'value' => 'required|integer|min:0',
        ]);

        $stok = $this->findOrCreate($data['produk_id']);
        $stok->{$data['lokasi']} = $data['value'];
        $stok->save();

        $this->syncProdukTotal($stok->produk_id);

        return response()->json(['ok' => true, 'total' => $stok->total()]);
    }

    protected function findOrCreate(int $produkId): StokLokasi
    {
        return StokLokasi::firstOrCreate(
            ['produk_id' => $produkId],
            ['daqam' => 0, 'food_court' => 0, 'kantor' => 0, 'min_stok' => 10]
        );
    }

    protected function syncStokLokasi(): void
    {
        Produk::query()->each(function (Produk $produk) {
            $this->findOrCreate($produk->id);
        });
    }

    protected function syncProdukTotal(int $produkId): void
    {
        $stok = StokLokasi::where('produk_id', $produkId)->first();
        if ($stok) {
            Produk::where('id', $produkId)->update(['stok' => $stok->total()]);
        }
    }
}
