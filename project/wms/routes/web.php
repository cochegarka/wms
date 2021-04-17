<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/report', function() {
    return view('report', ['from' => '2021-01-01', 'to' => '2021-01-31']);
});


Route::get('/excel/incomes/{id}', function($id) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path()."/sheets/m4.xls");

    $sheet = $spreadsheet->getActiveSheet();

    $income = \App\Models\Income::where('id', $id)->first();
    $organization = \App\Models\Organization::where('id', $income->organization_id)->first();
    $provider = \App\Models\Organization::where('id', $income->provider_id)->first();
    $subdivision = \App\Models\Subdivision::where('organization_id', $organization->id)->first();
    $okpo = \App\Models\Okpo::where('id', $organization->okpo_id)->first();
    $provider_okpo = \App\Models\Okpo::where('id', $provider->okpo_id)->first();

    $sheet->setCellValue('BN5', $income->number);
    $sheet->setCellValue('BV13', $income->cor_id);
    $sheet->setCellValue('CN13', $income->pay_id);
    $sheet->setCellValue('M8', $organization->name);
    $sheet->setCellValue('Z9', $subdivision->name);
    $sheet->setCellValue('A13', $income->creation_date);
    $sheet->setCellValue('CT8', $okpo->code);
    $sheet->setCellValue('AK13', $provider_okpo->code);
    $sheet->setCellValue('Y13', $provider->name);

    $accept_pos = \App\Models\EmplPosition::where('id', $income->accept_pos_id)->first();
    $spreadsheet->getSheet(1)->setCellValue('J23', $accept_pos->name);
    $accept_empl = \App\Models\Employee::where('id', $income->accept_empl_id)->first();
    $spreadsheet->getSheet(1)->setCellValue('AN23', $accept_empl->fio);

    $items = \App\Models\IncomeRow::where('income_id', $income->id)->get();
    foreach ($items as $i => $item) {
        $pos = 18 + $i;
        $nomenclature = \App\Models\Nomenclature::where('id', $item->nomenclature_id)->first();
        $unit = \App\Models\Unit::where('id', $nomenclature->unit_id)->first();
        
        $sheet->setCellValue("A$pos", $nomenclature->name);
        $sheet->setCellValue("R$pos", $nomenclature->number);
        $sheet->setCellValue("AA$pos", $unit->code);
        $sheet->setCellValue("AG$pos", $unit->name);
        $sheet->setCellValue("AT$pos", $item->real_quantity);
        $sheet->setCellValue("BB$pos", $item->doc_quantity);

        $price = \App\Models\Price::where('nomenclature_id', $item->nomenclature_id)->where('created_at', '>=', $income->creation_date)->orderBy('created_at', 'desc')->first();
        $nds = \App\Models\Nd::where('id', $price->nds_id)->first();
        $sheet->setCellValue("BJ$pos", $price->price);
        $sum = $price->price * $item->real_quantity;
        $sheet->setCellValue("BR$pos", $sum);
        $nds_sum = ($price->price * $item->real_quantity) * ($nds->rate / 10.0);
        $sheet->setCellValue("CA$pos", $nds_sum);
        $sheet->setCellValue("CA$pos", $nds_sum);
        $sheet->setCellValue("CJ$pos", $sum + $nds_sum);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
    $writer->save(public_path()."/sheets/tmp.xls");

    return response()->download(public_path()."/sheets/tmp.xls");
});

Route::get('/excel/outcomes/{id}', function($id) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path()."/sheets/m15.xls");

    $sheet = $spreadsheet->getActiveSheet();

    $outcome = \App\Models\Outcome::where('id', $id)->first();
    $organization = \App\Models\Organization::where('id', $outcome->organization_id)->first();
    $subdivision = \App\Models\Subdivision::where('organization_id', $organization->id)->first();
    $okpo = \App\Models\Okpo::where('id', $organization->okpo_id)->first();

    $sheet->setCellValue('BN5', $outcome->number);
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
    $writer->save(public_path()."/sheets/tmp.xls");

    return response()->download(public_path()."/sheets/tmp.xls");
});

Route::get('/excel/inventarizations/{id}', function($id) {
    return 'TODO';
});

Route::post('/report', function(Request $request) {
    $from = $request->input('from');
    $to = $request->input('to');
    return view('report', ['from' => $from, 'to' => $to]);
});

Route::group(['prefix' => '/'], function () {
    Voyager::routes();
});
