@extends('voyager::master')

@section('content')
    <div class="page-content">
        @include('voyager::alerts')
        <div class="container">
        <h2>Остатки</h2>
        </div>
        <hr>
        <div class="container">
        <form class="form-inline" method="POST">
        @csrf
        <div class="form-group mb-2">
        <h4>Период:</h4>
        </div>
            <div class="form-group mb-2">
            <input type="date" class="form-control" name="from" value="{{$from}}">
            </div>
            <div class="form-group mb-2">
        -
        </div>
            <div class="form-group mb-2">
                <input type="date" class="form-control" name="to" value="{{$to}}">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Сформировать</button>
        </form>
        </div>
        <hr>

<?php
    $inventarizations = DB::select('SELECT * from inventarizations WHERE inventarizations.creation_date BETWEEN ? AND ?', [$from, $to]);

    $latest_inventarization = NULL;
    $m = "1970-01-01";
    foreach ($inventarizations as $i) {
        if ($i->creation_date > $m) {
            $m = $i->creation_date;
            $latest_inventarization = $i;
        }
    }

    $nomenclatures = DB::select('SELECT * from nomenclatures');
    $incomes = DB::select('SELECT * from nomenclatures, income_rows, incomes WHERE nomenclatures.id = income_rows.nomenclature_id AND incomes.id = income_rows.income_id AND (incomes.creation_date BETWEEN ? AND ?)', [$m, $to]);
    $outcomes = DB::select('SELECT * from nomenclatures, outcome_rows, outcomes WHERE nomenclatures.id = outcome_rows.nomenclature_id AND outcomes.id = outcome_rows.outcome_id AND (outcomes.creation_date BETWEEN ? AND ?)', [$m, $to]);
    

    $inventarization_table = !is_null($latest_inventarization) ? DB::select('SELECT * from inventarization_rows, nomenclatures WHERE nomenclatures.id = inventarization_rows.nomenclature_id AND inventarization_rows.inventarization_id = ?', [$latest_inventarization->id]) : [];

    $leftovers = [];
    foreach ($nomenclatures as $n) {
        $leftovers[$n->name] = 0;
    }
    foreach ($inventarization_table as $row) {
        $leftovers[$row->name] = $row->fact_quantity;
    }
    foreach ($incomes as $i) {
        $leftovers[$i->name] += $i->real_quantity;
    }
    foreach ($outcomes as $o) {
        $leftovers[$o->name] -= $o->quantity;
    }

    $total = array_sum(array_values($leftovers));
?>
    <div class="container">
    <table class="table table-bordered">
        <thead>
            <th>Номенклатура</th>
            <th>Конечный остаток</th>
        </thead>
        <tbody>
            @foreach ($leftovers as $k => $v)
                <tr>
                    <td>{{$k}}</td>
                    <td>{{$v}}</td>
                </tr>
            @endforeach
            <tr>
                <td><b>Итого</b></td>
                <td><b>{{$total}}</b></td>
            </tr>
        </tbody>
    </table>
    </div>
    </div>
@stop