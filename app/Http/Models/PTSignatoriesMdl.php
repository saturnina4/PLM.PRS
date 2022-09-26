<?php

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PTSignatoriesMdl extends Model {
    protected $table      = 'vrc_pt_signatories';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
