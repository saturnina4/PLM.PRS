<?php

namespace MiSAKACHi\VERACiTY\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Http\Models\SalaryTrancheScheduleModel;
use MiSAKACHi\VERACiTY\Http\Requests\SetTrancheRequest;

class SettingsController extends Controller {

    protected $salaryTrancheSchedule;

    public function __construct() {
        $this->salaryTrancheSchedule = new SalaryTrancheScheduleModel;
    }

    public function getSettingsIndex() {
        return view( 'application.Settings.index' );
    }

    public function getViewTranche() {

    }

    public function getSetTranche( Request $request ) {
        try {
            $salaryTrancheSchedule = $this->salaryTrancheSchedule
                ->findOrFail( 1 );

            if( isset( $salaryTrancheSchedule ) ) {
                $activeTranche        = $salaryTrancheSchedule->activeTranche;
                $activeTrancheVersion = $salaryTrancheSchedule->activeVersion;

                return view( 'application.Settings.SetTranche' )
                    ->with( 'activeTranche', $activeTranche )
                    ->with( 'activeTrancheVersion', $activeTrancheVersion );
            }
        } catch ( \Exception $e ) {
            $request->session()->flash( 'errorMessage', "Something went wrong while fetching the tranche records. [Error Code: {$e->getCode()}]" );
        }

        return view( 'application.Settings.SetTranche' );
    }

    public function postSetTranche( SetTrancheRequest $request ) {
        try {
            DB::transaction( function() use( $request ) {
                $salaryTrancheSchedule = $this->salaryTrancheSchedule->findOrFail( 1 );
                $salaryTrancheSchedule->activeTranche = $request->input( 'activeTranche' );
                $salaryTrancheSchedule->activeVersion = $request->input( 'activeTrancheVersion' );
                $salaryTrancheSchedule->save();
            });

            $request->session()->flash( 'successMessage', 'Active Tranche Version & Tranche saved successfully' );
        } catch( \Exception $e ) {
            $request->session()->flash( 'errorMessage', "Error in setting the Active Tranche & Version [Error Code: {$e->getCode()}]" );
        }

        return redirect()->back();
    }

    public function getUploadTranche() {

    }

    public function postUploadTranche() {

    }

}
