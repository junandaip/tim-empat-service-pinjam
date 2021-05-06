<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
// require _DIR_ . '/vendor/autoload.php';
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function showPinjaman($username)
    {
        $pinjaman = Peminjaman::where('username', $username)->first();
        if ($pinjaman) {
            $id = $pinjaman->id_buku;
            $response = Http::get('http://localhost:8000/book/id/' . $id);

            $result = $response->json();

            return response()->json([
                'Buku' => $result,
                $pinjaman
            ], 200);
        } else {
            return response()->json([
                'message' => 'Anda Belum meminjam'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'id_buku' => 'required'
        ]);

        $pinjaman = Peminjaman::create(
            $request->only(['username', 'id_buku'])
        );
        $response = Http::get('http://localhost:8000/book/id/' . $request->id_buku);
        $datajson = json_decode($response, TRUE);
        $data = $datajson['data'];
        $stock = $data['stock'] - 1;
        $kondisi = 0;

        Http::put('http://localhost:8000/book/' . $request->id_buku, [
            'stock' => $stock
        ]);

        Http::put('http://localhost:8090/user/' . $request->username, [
            'kondisi' => $kondisi
        ]);

        return response()->json([
            'created' => true,
            'data' => $pinjaman
        ], 201);
    }


    public function destroy($id)
    {
        try {
            $pinjaman = Peminjaman::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'book not found'
                ]
            ], 404);
        }

        $pinjaman->delete();

        $response = Http::get('http://localhost:8000/book/id/' . $pinjaman->id_buku);
        $datajson = json_decode($response, TRUE);
        $data = $datajson['data'];
        $stock = $data['stock'] + 1;
        $kondisi = 0;

        Http::put('http://localhost:8000/book/' . $pinjaman->id_buku, [
            'stock' => $stock
        ]);

        Http::put('http://localhost:8000/user/' . $pinjaman->username, [
            'kondisi' => $kondisi
        ]);

        return response()->json([
            'deleted' => true
        ], 200);
    }
}
