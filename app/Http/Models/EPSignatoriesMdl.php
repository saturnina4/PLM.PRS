<?php

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EPSignatoriesMdl extends Model {
    protected $table      = 'vrc_ep_signatories';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
