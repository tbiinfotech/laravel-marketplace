<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\models\RetailCategory;
use App\models\City;
use App\models\Brand;
use DB;
use Illuminate\Routing\Route;
use Session;
use Response;

class BrandsController extends Controller {

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct(Route $route, Request $request) {

        $this->viewName = 'brands';
        $this->modelTitle = 'Brands';
        $this->model = new Brand;

        $currentAction = $route->getActionName();
        list($controller, $method) = explode('@', $currentAction);
        $this->controllerName = preg_replace('/.*\\\/', '', $controller);
        $this->actionName = preg_replace('/.*\\\/', '', $method);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function admin_add($id = null) {

        $modelTitle = $this->modelTitle;
        $actionName = $this->actionName;
        $viewName = $this->viewName;
        $controllerName = $this->controllerName;

        $categories = RetailCategory::select('id', 'name', 'pid')->where(['status' => 1, 'is_sellable' => 1])
            ->where("pid", "<>", 0)
            ->whereHas("categoryChilds")->pluck('name', 'id');

        return view('admin/' . $this->viewName . '/add', compact('categories', 'modelTitle', 'controllerName', 'actionName', 'viewName'));
    }

    /**
     * Admin edit
     * function for edit any attribute
     * @return void
     * @access public
     */
    public function admin_edit($id = null) {

        $modelTitle = $this->modelTitle;
        $actionName = $this->actionName;
        $viewName = $this->viewName;
        $controllerName = $this->controllerName;

        $result = $this->model->whereId($id)->with('categories')->first();
        $categories = RetailCategory::select('id', 'name', 'pid')->where(['status' => 1, 'is_sellable' => 1])
            ->where("pid", "<>", 0)
            ->whereHas("categoryChilds")->pluck('name', 'id');
        return view('admin/' . $this->viewName . '/edit', compact('categories', 'modelTitle', 'controllerName', 'actionName', 'result', 'viewName'));
    }

    /**
     * Admin edit
     * function for delete any attribute
     * @return void
     * @access public
     */
    public function admin_delete() {

        $data = Input::all();
        $id = $data['id'];

        if (!empty($id)) {

            $brands = $this->model::where('id', $id)->firstOrFail();
            $brands->categories()->detach();
            $brands->delete();

            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'Successfully Deleted.');
            return response()->json(['status' => true, 'url' => 'brands']);
        }
    }

    /**
     * Admin edit
     * function for update attribute status
     * @return void
     * @access public
     */
    public function admin_showvalue(Request $request) {

        if ($request->id) {
            if ($request->state == 1) {
                $updated_data["show_type"] = 1;
            } else {
                $updated_data["show_type"] = 2;
            }
            \DB::table('attributes')->where("id", $request->id)->update($updated_data);
        }
    }

    /**
     * get attribute
     * function for get any attribute by name
     * @return void
     * @access public
     */
    public function getIdByName($name, $tableName, $columnName, $returnId) {

        $data = DB::table($tableName)->where([$columnName => $name])->first();
        $returnArray = ['error' => true];
        if (!empty($data)) {
            $returnArray['id'] = $data->$returnId;
            $returnArray['error'] = false;
        }
        return $returnArray;
    }

    /**
     * get location
     * function for get location
     * @return $response
     * @access public
     */
    public function getDetailLocation($address = null) {

        $curl = curl_init();
        $finalAddress = str_replace(' ', ',', $address);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://maps.googleapis.com/maps/api/geocode/json?address=$finalAddress",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: a762c1f0-232c-5a43-2477-4df7142a3b7f"
            ),
        ));

        $responseData = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($responseData);
        $addArrRturn = ['state' => '', 'city' => '', 'pincode' => '', 'lat' => '', 'lng' => ''];
        if (empty($response->results)) {
            return $addArrRturn['status'] = false;
        }

        foreach ($response->results[0]->address_components as $key => $value) {
            $addressType = $value->types[0];

            if ($addressType == 'country' && ($value->long_name != 'Australia')) {
                return $addArrRturn['status'] = false;
            }

            if ($addressType == 'administrative_area_level_1') {
                $addArrRturn['state'] = $value->long_name;
            }

            if ($addressType == 'locality') {
                $addArrRturn['city'] = $value->long_name;
            }

            if ($addressType == 'postal_code') {
                $addArrRturn['pincode'] = $value->short_name;
            }
        }
        $addArrRturn['lat'] = $response->results[0]->geometry->location->lat;
        $addArrRturn['lng'] = $response->results[0]->geometry->location->lng;

        if (($addArrRturn['state'] == '') || ($addArrRturn['city'] == '') || ($addArrRturn['pincode'] == '') || ($addArrRturn['lat'] == '') || ($addArrRturn['lng'] == '')) {
            return $addArrRturn['status'] = false;
        }

        $stateDb = DB::table('state')->where(['name' => $addArrRturn['state'], 'country_id' => 14])->first();

        $addArrRturn['state'] = '';
        $addArrRturn['status'] = false;
        if ($stateDb != null) {
            $addArrRturn['state'] = $stateDb->id;
            $addArrRturn['status'] = true;
        }

        $cityDb = DB::table('cities')->where(['City' => $addArrRturn['city'], 'CountryID' => 14])->first();
        if ($cityDb == null) {
            $city = ['CountryID' => 14, 'City' => $addArrRturn['city'], 'Latitude' => $addArrRturn['lat'], 'Longitude' => $addArrRturn['lng']];
            $result = City::create($city);
            $addArrRturn['city'] = $result->id;
            $addArrRturn['status'] = true;
        } else {
            $addArrRturn['status'] = true;
            $addArrRturn['city'] = $cityDb->CityId;
        }

        if (($addArrRturn['state'] == '') || ($addArrRturn['city'] == '') || ($addArrRturn['pincode'] == '') || ($addArrRturn['lat'] == '') || ($addArrRturn['lng'] == '')) {
            return $addArrRturn['status'] = false;
        }

        if ($err) {
            return false;
        }

        return $addArrRturn;
    }

    /**
     * download csv
     * function for csv
     * @return void
     * @access public
     */
    public function downloadfile($allCsvFields, $filename) {

        $csv_terminated = "\n";
        $csv_separator = ",";
        $csv_enclosed = '"';
        $csv_escaped = "\\";
        $schema_insert = '';

        foreach ($allCsvFields as $heading) {
            $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, stripslashes($heading)) . $csv_enclosed;
            $schema_insert .= $l;
            $schema_insert .= $csv_separator;
        }
        $out = trim(substr($schema_insert, 0, -1));
        $out .= $csv_terminated;

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($out));
        header("Content-type: text/x-csv");
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");
        echo $out;
        exit;
    }
}
