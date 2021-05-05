<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function cariId($username)
    {
        $pinjaman = Peminjaman::where('username', $username)->first();
        if ($pinjaman) {
            return response()->json([
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

        return response()->json([
            'deleted' => true
        ], 200);
    }
}
