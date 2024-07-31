<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PemainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemain = Pemain::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar pemain Sepak Bola',
            'data' => $pemain,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemain' => 'required',
            'foto' => 'required|image|mimes:jpg,png,max:2048',
            'tgl_lahir' => 'required',
            'harga_pasar' => 'required|numeric',
            'posisi' => 'required',
            'negara' => 'required',
            'id_klub' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $path = $request->file('foto')->store('public/foto');
            $pemain = new Pemain;
            $pemain->nama_pemain = $request->nama_pemain;
            $pemain->foto = $path;
            $pemain->tgl_lahir = $request->tgl_lahir;
            $pemain->harga_pasar = $request->harga_pasar;
            $pemain->posisi = $request->posisi;
            $pemain->negara = $request->negara;
            $pemain->id_klub = $request->id_klub;
            $pemain->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dibuat',
                'data' => $pemain,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pemain = pemain::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail pemain',
                'data' => $pemain,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemain' => 'required',
            'foto' => 'nullable|image|mimes:jpg,png,max:2048',
            'tgl_lahir' => 'required',
            'harga_pasar' => 'required|numeric',
            'posisi' => 'required',
            'negara' => 'required',
            'id_klub' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pemain = Pemain::findOrFail($id);
            if ($request->hasFile('foto')) {
                // delete foto/ Logo lama
                Storage::delete($pemain->foto);

                $path = $request->file('foto')->store('public/foto');
                $pemain->foto = $path;
            }
            $pemain->nama_pemain = $request->nama_pemain;
            $pemain->tgl_lahir = $request->tgl_lahir;
            $pemain->harga_pasar = $request->harga_pasar;
            $pemain->posisi = $request->posisi;
            $pemain->negara = $request->negara;
            $pemain->id_klub = $request->id_klub;
            $pemain->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $pemain,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pemain = Pemain::findOrFail($id);
            // delete foto/ Logo lama
            Storage::delete($pemain->foto);
            $pemain->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data Pemain ' . $pemain->nama_pemain . ' berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
