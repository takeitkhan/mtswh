@extends('admin.layouts.master')

@section('title')
    Manage Contact
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <!-- Form -->
        <div class="col-md-4">
            <h6> 
                <div class="title-with-border"> 
                    @if(!empty($contact))
                        <span class="text-primary">Edit Contact Information</span>
                    @else
                        Contact Information
                    @endif
                </div> 
            </h6>
            <form action="{{ !empty($contact) ? route('contact_update') : route('contact_store') }}" method="post">
                @csrf
                @if (!empty($contact))
                    <input type="hidden" name="id" value="{{ $contact->id }}">
                @endif
                <div class="form-group"> 
                    <label for="name">Contact Name: </label>
                    <input type="text" class="form-control" placeholder="Enter contact name" name="name"
                        value="{{ !empty($contact) ? $contact->name : old('name') }}" required>
                </div>

                <div class="form-group"> 
                    <label for="phone">Phone: </label>
                    <input type="text" class="form-control" placeholder="Enter phone" name="phone"
                        value="{{ !empty($contact) ? $contact->phone : old('phone') }}" required>
                </div>

                <div class="form-group"> 
                    <label for="email">Email: </label>
                    <input type="email" class="form-control" placeholder="Enter email" name="email"
                        value="{{ !empty($contact) ? $contact->email : old('email') }}">
                </div>

                <div class="form-group"> 
                    <label for="address">Address: </label>
                    <input type="text" class="form-control" placeholder="Enter address" name="address"
                        value="{{ !empty($contact) ? $contact->address : old('address') }}">
                </div>

                <div class="form-group"> 
                    <label for="license">License no: </label>
                    <input type="text" class="form-control" placeholder="Enter license no" name="license_no"
                        value="{{ !empty($contact) ? $contact->license_no : old('license_no') }}">
                </div>

                <div class="form-group"> 
                    <label for="contact_person">Contact person: </label>
                    <input type="text" class="form-control" placeholder="Enter contact person" name="contact_person"
                        value="{{ !empty($contact) ? $contact->contact_person : old('contact_person') }}">
                </div>

                <div class="form-group"> 
                    <label for="contact_person_no">Contact person phone: </label>
                    <input type="text" class="form-control" placeholder="Enter contact person phone no" name="contact_person_no"
                        value="{{ !empty($contact) ? $contact->contact_person_no : old('contact_person_no') }}">
                </div>

                <div class="form-group">
                    <label for="vat_type">Vat Type: </label>
                    <select name="vat_type" id="vat_type" class="form-select">
                        <option value="">Select</option>
                        <option value="Inclusive" {{ !empty($contact) && $contact->vat_type == 'Inclusive' ? 'selected' : '' }}>Inclusive</option>
                        <option value="Exclusive" {{ !empty($contact) && $contact->vat_type == 'Exclusive' ? 'selected' : '' }}>Exclusive</option>
                    </select>
                </div>

                <div class="form-group d-none" id="vat_percent"> 
                    <label for="vat_percent">Vat percent: </label>
                    <input type="text" class="form-control" placeholder="Enter vat percentage" name="vat_percent"
                        value="{{ !empty($contact) ? $contact->vat_percent : old('vat_percent') }}"> &nbsp;%
                </div>

                <div class="form-group">
                    <label for="tax_type">Tax Type: </label>
                    <select name="tax_type" id="tax_type" class="form-select">
                        <option value="">Select</option>
                        <option value="Inclusive" {{ !empty($contact) && $contact->tax_type == 'Inclusive' ? 'selected' : '' }}>Inclusive</option>
                        <option value="Exclusive" {{ !empty($contact) && $contact->tax_type == 'Exclusive' ? 'selected' : '' }}>Exclusive</option>
                    </select>
                </div>

                <div class="form-group d-none" id="tax_percent"> 
                    <label for="tax_percent">Tax percent: </label>
                    <input type="text" class="form-control" placeholder="Enter tax percentage" name="tax_percent"
                        value="{{ !empty($contact) ? $contact->tax_percent : old('tax_percent') }}">  &nbsp;%
                </div>


                <div class="form-group"> 
                    <label for="note">Note: </label>
                    <textarea class="form-control" name="note">{{ !empty($contact) ? $contact->note : old('note') }}</textarea>
                </div>
                <div class="form-submit_btn">
                    <button type="submit" class="btn blue">Submit</button>
                </div>
            </form>
        </div><!-- ENd Form-->
        <div class="col-md-1"></div>
        <!-- Data -->
        <div class="col-md-7 table-wrapper desktop-view mobile-view">
            <h6> 
                <div class="title-with-border"> 
                    All Contacts
                </div> 
            </h6>
            <table class="">
                <thead>
                    <tr>                        
                        <th></th>                        
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Contact Person</th>
                        <th>VAT/TAX Info</th>
                        <th>Note</th>                        
                    </tr>
                </thead>
                <tbody>
                    @php  $contacts = $Query::accessModel('contact')::get(); @endphp
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>
                                {!! $ButtonSet::delete('contact_destroy', $contact->id) !!}
                                {!! $ButtonSet::edit('contact_edit', $contact->id) !!}
                            </td>
                            <td>
                                {!! $contact->name !!}
                            </td>
                            <td>
                                <small>
                                    {!! $contact->phone !!}<br/>
                                    {!! $contact->email !!}<br/>
                                    {!! $contact->address !!}<br/>                                    
                                </small>
                            </td>
                            <td>
                                <small>
                                    {!! $contact->license_no !!}<br/>
                                    {!! $contact->contact_person !!}<br/>
                                    {!! $contact->contact_person_no !!}
                                </small>
                            </td>
                            <td>
                                {!! $contact->vat_type !!} ({!! $contact->vat_percent !!})<br/>
                                {!! $contact->tax_type !!} ({!! $contact->tax_percent !!})                                    
                            </td>
                            <td>
                                {{$contact->note}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection



@section('cusjs')

<script>
    $("select#vat_type").change(function(){
        let vatType = $(this).val();
        if(vatType == 'Exclusive'){
            $("#vat_percent").removeClass('d-none')
        }else{
            $("#vat_percent").addClass('d-none')
        }
    })


    $("select#tax_type").change(function(){
        let taxType = $(this).val();
        if(taxType == 'Exclusive'){
            $("#tax_percent").removeClass('d-none')
        }else{
            $("#tax_percent").addClass('d-none')
        }
    })
</script>

@endsection