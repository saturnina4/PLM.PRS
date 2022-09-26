<?php

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CPSignatoriesMdl extends Model {
    protected $table      = 'vrc_cp_signatories';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
