<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Excluded;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\ComputationsClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPSignatoriesMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\EPEditSignatoriesRqst;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class SignatoriesCtrl extends Controller {
    use GeneratesUuidTrait;

    protected $empDetailsModel, $signatoriesModel;

    public function __construct() {
        $this->empDetailsModel  = new EmployeeDetailsMdl();
        $this->signatoriesModel = new EPSignatoriesMdl();
    }

    public function getAction( Request $request ) {
        $empDetails = $this->empDetailsModel
            ->select( 'employeeNumber', 'fullName' )
            ->orderBy( 'fullName' )
            ->get();

        $signatories = $this->signatoriesModel
            ->join( "{$this->empDetailsModel->table} as ecd", 'empNumber', '=', 'ecd.employeeNumber' )
            ->select( 'employeeNumber', 'position' )
            ->orderBy( 'id', 'asc' )
            ->get();

        return view( 'Application.Features.Excluded.Signatories' )
            ->with( 'empDetails', $empDetails )
            ->with( 'signatories', $signatories );
    }

    public function postAction( EPEditSignatoriesRqst $request ) {
        try {
            DB::transaction( function () use ( $request ) {
                for ( $i = 1; $i <= 5; ++$i ) {
                    $signatories = $this->signatoriesModel->find( $i );
                    $signatories->empNumber = $request->input( "employee{$i}" );
                    $signatories->position  = $request->input( "position{$i}" );
                    $signatories->save();
                }
            } );

            $request->session()->flash( 'successMessage', 'Changes was saved.' );

            return response()->json( [ 'ajaxSuccess' => 'ok' ] );
        } catch( \PDOException $e ) {
            $request->session()->flash( 'errorMessage', 'Critical application error occurred. [Database]' );
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
        } catch( \Throwable $e ) {
            $request->session()->flash( 'errorMessage', 'Critical application error occurred. [Application]' );
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
        }
    }
}
