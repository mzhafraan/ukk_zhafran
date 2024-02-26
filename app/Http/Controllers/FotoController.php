<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Album;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    public function index()
    {
        $fotos = Foto::with(['album', 'user'])->get(); // Eager load album and user relations
        return view('fotos.index', compact('fotos'));
    }

    // Metode lain akan ditambahkan kemudian
    public function create()
    {
        $albums = Album::all(); // Ambil semua album untuk dropdown
        return view('fotos.create', compact('albums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_foto' => 'required|string|max:255',
            'deskripsi_foto' => 'nullable|string',
            'lokasi_file' => 'required|image|max:2048', // Pastikan Anda telah memvalidasi file gambar
            'album_id' => 'required|exists:gallery_album,album_id',
        ]);


        $path = $request->file('lokasi_file')->store('public/fotos'); // Menyimpan file dan mengambil path

        Foto::create([
            'judul_foto' => $request->judul_foto,
            'deskripsi_foto' => $request->deskripsi_foto,
            'lokasi_file' => $path,
            'album_id' => $request->album_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('foto.index')->with('success', 'Foto has been added');
    }

    public function show($id)
    {
        $foto = Foto::with(['album', 'user', 'komentars.user'])->findOrFail($id); // Eager load relations
        return view('fotos.show', compact('foto'));
    }

    public function destroy($id)
    {
        $foto = Foto::findOrFail($id);
        Storage::delete($foto->lokasi_file);

        $foto->delete();

        return back();
    }



}