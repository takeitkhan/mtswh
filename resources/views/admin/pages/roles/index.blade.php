@extends('admin.layouts.master')

@section('title', 'All Role')

@section('content')

<div class="content-wrapper p-0">
    <div class="table-wrapper desktop-view mobile-view">
        <table id="table_id">
            <thead style="position: sticky;top:-1px;">
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Type</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>
                            {!! $ButtonSet::delete('role_destroy', $role->id) !!}
                            {!! $ButtonSet::edit('role_edit', $role->id) !!}
                        </td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->code }}</td>
                        <td>{{ $role->type }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
@endsection
