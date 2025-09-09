<span class="mx-2 project_wrap">
    <?php /*
   <div class="d-inline-block">
        <label class="d-block" style="font-size: 12px;" for="">Project Type</label>
        <select name="project_type" id="" class="bg-transparent project_type">
            <option value="">Project Type</option>
            <option value="Supply"     {{"Supply" == request()->get('project_type') ? 'selected' : null}}>Supply</option>
            <option value="Service"  {{"Service" == request()->get('project_type') ? 'selected' : null}}>Service</option>
        </select>
   </div>
 */ ?>

        <div class="d-inline-block"  id="project_area">
              @if(request()->get('project_type'))
              @endif
              @php
                  if(request()->get('project_type') == 'Supply'){
                      //$allProject = $Model('Project')::where('type', 'Supply')->get();
                  } else {
                      //$allProject = $Model('Project')::where('type', 'Service')->get();
                  }
                  
                  $allProject = $Model('Project')::whereIn('type', ['Supply', 'Service'])->get();
              @endphp

            <label class="d-block" style="font-size: 12px;" for="">Project </label>
            <form action="{{url()->full()}}" method="get" class="projectFormSubmit">
                @if(request()->get('wh_id'))
                <input type="hidden" name="wh_id" value="{{request()->get('wh_id')}}">
                @endif
                <select name="project" id="" class="bg-transparent projectForm">
                    <option value="">Project Type</option>
                    @foreach($allProject as $value)
                        <option value="{{$value->name}}"
                            {{$value->name == request()->get('project') ? 'selected' : null}}
                        >{{$value->name}} - {{$value->type}}</option>
                    @endforeach
                </select>
            </form>

       </div>

</span>


@section('cusjs')
    @parent
    <script>
        $('.project_type').on('change', function(){
            let data = $(this).find(':selected').val()

            // $('#project_area').load(location.href+'?project_type='+data+ ' #project_area')
            let check = window.location.href.indexOf('?') + 1;
            check = check > 0 ? '&&' : '?'
            check = check+'project_type='+data
            // console.log(check)
            $("#project_area").load("{{url()->full()}}" +check+ " #project_area > *")

        })

        $('.project_wrap').on('change', '.projectForm', function(){
            $('form.projectFormSubmit').submit();
        })
    </script>
@endsection
