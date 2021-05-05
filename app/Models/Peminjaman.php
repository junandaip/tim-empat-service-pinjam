<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model

{

    protected $table = 'peminjamans';

    protected $fillable = ['username', 'id_buku'];
}
