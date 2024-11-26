<?php

namespace App\Http\Controllers\TOOLS;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MakeAccountController extends Controller
{
    public function password($char = 8)
    {
        // membuat password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $password = '';
        for ($i = 0; $i < $char; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $password .= $characters[$index];
        }

        return $password;
    }

    public function email($email)
    {
        // Membagi string berdasarkan tanda koma atau titik
        $array_teks = preg_split('/[,.]/', $email, -1, PREG_SPLIT_NO_EMPTY);
        // Mengambil elemen pertama dari array hasilnya
        $remove_koma = reset($array_teks);
        // hitung karakter yang ada di dalam $remove_koma
        $jumlah_karakter = strlen($remove_koma);
        // jika jumlah karakter kurang dari 4 maka ambil $array_teks selanjutnya
        if ($jumlah_karakter <= 4) {
            $remove_koma = next($array_teks);
        }
        $slug = Str::slug($remove_koma, '_'); // Membuat slug dari nm_petugas (misalnya: john-doe)
        $new_email = Str::finish($slug, '@mail.com'); // Menambahkan "@mail.com" di belakang slug

        return $new_email;
    }
}
