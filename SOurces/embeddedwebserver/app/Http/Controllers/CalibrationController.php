<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Calibration;
use App\GoldilocksGeneric;

class CalibrationController extends Controller
{
    /**
     * Show index page for calibration.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $calibrations = Calibration::all();
        $defaultCalibration = Calibration::where('default', 1)->first();
        if (!$defaultCalibration) {
            $defaultCalibration = -1;
        }
        $genericParameters = GoldilocksGeneric::first()->parameters;
        return view('calibration', compact('calibrations', 'defaultCalibration', 'genericParameters'));
    }

    /**
     * Add a new entry for calibration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get data from request
        $data = $request->json()->all();

        $calibrationName = $data[0];
        $jsonData = json_encode($data[1]);
        $calibratedParams = json_encode($data[2]);
       // error_log($calibratedParam);

        //echo $calibratedParam;
        if(Calibration::where(['name' => $calibrationName])->exists()){
            return json_encode(['status' => 'failure']);
        }
        //echo $calibratedParams;
        //create new calibration in database
        $newC = Calibration::create([
            'name' => $calibrationName,
            'parameters' => $jsonData,
            'calibratedParams' => $calibratedParams
        ]);
        
        $this->setDefaultHelper($newC->id);

        return json_encode(['status' => 'success']);
    }

    /**
     * Update the specified calibration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //get data from request
        $data = $request->json()->all();

        //find program to update using information from request
        $program = Calibration::where('id', $data[0])->first();
       // echo $data[2];
        //change data and save program
        $program->parameters = json_encode($data[1]);
        $program->calibratedParams = json_encode(($data[2]));
        $program->save();

        return json_encode(['status' => 'success']);
    }

    /**
     * Get default calibration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDefault(Request $request)
    {
        return Calibration::where('default', 1)->first();
    }

    /**
     * Change default calibration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setDefault(Request $request)
    {
        // request has new default id
        //get data from request
        $data = $request->json()->all();

        if (!$this->setDefaultHelper($data[0])) {
            return json_encode(['status' => 'failure']);
        }

        return json_encode(['status' => 'success']);
    }

    private function setDefaultHelper($newCalibrationId) {
        // reset old default
        $oldDefault = Calibration::where('default', 1)->first();
        if ($oldDefault) {
            $oldDefault->default = 0;
            $oldDefault->save();
        }

        $newDefault = Calibration::where('id', $newCalibrationId)->first();
        if (!$newDefault) {
            return false;
        }

        $newDefault->default = 1;
        $newDefault->save();
        return true;
    }
}
