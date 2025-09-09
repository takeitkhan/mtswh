<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';
    protected $fillable = ['name', 'phone', 'email', 'address', 'license_no',
    'contact_person', 'contact_person_no', 'vat_type', 'vat_percent', 'tax_type', 'tax_percent', 'note'];
}
