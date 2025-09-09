<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class ProductsImport implements ToCollection, WithHeadingRow
{
    use RemembersRowNumber;
    use Importable;
    private $rows = 0;

    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection  $rows)
    {
        //dd(request()->all());
//        dd($rows);

        if(request()->start_import){
            dd(request()->all());
        }else{
            return redirect()->back()->with('importExcel', $rows);
        }
        return new Product([
            //
        ]);
    }
}
