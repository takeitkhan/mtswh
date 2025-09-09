@extends('admin.layouts.master')

@section('title')
    PPI SPI Accumulated Report
@endsection

@section('content')
    @php
       use Illuminate\Support\Facades\DB;
        use Carbon\Carbon;
        use App\Models\PpiSpi;
        
        $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
        $projects = $productStockHelper->getPpiSpisWithProjects();
        
        $selectedProject = request()->get('project_name');
        $selectedRootSource = request()->get('root_source');
        $startDate = request()->get('start_date');
        $endDate = request()->get('end_date');
        
        // Prepare the query
        $queryPpi = PpiSpi::select([
                'ppi_products.product_id',
                'products.name AS product_name',
                DB::raw('GROUP_CONCAT(DATE(ppi_spis.created_at) ORDER BY ppi_spis.created_at) AS created_dates'),
                DB::raw('GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id) AS ppi_ids'),
                DB::raw("(
                    LENGTH(GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id)) 
                    - LENGTH(REPLACE(GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id), ',', '')) 
                    + 1
                ) AS total_count_of_ppi_id"),
                DB::raw('ppi_spi_sources.source_type AS source_type'),
                DB::raw('GROUP_CONCAT(ppi_spi_sources.who_source ORDER BY ppi_spi_sources.id) AS who_source'),
                'ppi_spis.*', 'warehouses.code'
            ])
            ->leftJoin('ppi_products', 'ppi_products.ppi_id', '=', 'ppi_spis.id')
            ->leftJoin('products', 'products.id', '=', 'ppi_products.product_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'ppi_products.warehouse_id')
            ->leftJoin('ppi_spi_sources', 'ppi_spi_sources.ppi_spi_id', '=', 'ppi_spis.id')
            ->groupBy('ppi_products.product_id')
            ->orderBy('ppi_products.product_id');
        
        $queryPpi->where('ppi_spis.action_format', 'Ppi');
        // Apply dynamic filters
        if ($selectedProject) {
            $queryPpi->where('ppi_spis.project', $selectedProject);
        }
        
        if ($selectedRootSource) {
            $queryPpi->where('ppi_spi_sources.who_source', 'like', "%{$selectedRootSource}%");
        }
        
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $start = Carbon::createFromFormat('d/m/Y', $startDate)->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', $endDate)->toDateString();
        
                $queryPpi->whereDate('ppi_spis.created_at', '>=', $start)
                      ->whereDate('ppi_spis.created_at', '<=', $end);
            } catch (\Exception $e) {
                \Log::error('Date parse error: ' . $e->getMessage());
            }
        }
        
        
        // Prepare the query
        $querySpi = PpiSpi::select([
                    'spi_products.product_id',
                    'products.name AS product_name',
                    DB::raw('GROUP_CONCAT(DATE(ppi_spis.created_at) ORDER BY ppi_spis.created_at) AS created_dates'),
                    DB::raw('GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id) AS ppi_ids'),
                    DB::raw("(
                        LENGTH(GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id)) 
                        - LENGTH(REPLACE(GROUP_CONCAT(DISTINCT ppi_spis.id ORDER BY ppi_spis.id), ',', '')) 
                        + 1
                    ) AS total_count_of_ppi_id"),
                    DB::raw('ppi_spi_sources.source_type AS source_type'),
                    DB::raw('GROUP_CONCAT(ppi_spi_sources.who_source ORDER BY ppi_spi_sources.id) AS who_source'),
                    'ppi_spis.*', 'warehouses.code'
                ])
                ->leftJoin('spi_products', 'spi_products.spi_id', '=', 'ppi_spis.id')
                ->leftJoin('products', 'products.id', '=', 'spi_products.product_id')
                ->leftJoin('warehouses', 'warehouses.id', '=', 'spi_products.warehouse_id')
                ->leftJoin('ppi_spi_sources', 'ppi_spi_sources.ppi_spi_id', '=', 'ppi_spis.id')
                ->groupBy('spi_products.product_id')
                ->orderBy('spi_products.product_id');
            
            $querySpi->where('ppi_spis.action_format', 'Spi');
            
            if ($selectedProject) {
                $querySpi->where('ppi_spis.project', $selectedProject);
            }
            
            if ($selectedRootSource) {
                $querySpi->where('ppi_spi_sources.who_source', 'like', "%{$selectedRootSource}%");
            }
            
            if (!empty($startDate) && !empty($endDate)) {
                try {
                    $start = Carbon::createFromFormat('d/m/Y', $startDate)->toDateString();
                    $end = Carbon::createFromFormat('d/m/Y', $endDate)->toDateString();
            
                    $querySpi->whereDate('ppi_spis.created_at', '>=', $start)
                          ->whereDate('ppi_spis.created_at', '<=', $end);
                } catch (\Exception $e) {
                    \Log::error('Date parse error: ' . $e->getMessage());
                }
            }
        
        // Execute query
        $ppiData = $queryPpi->get();
        $spiData = $querySpi->get();
        
        $root_sources = $selectedProject ? $productStockHelper->getLatestRootSourceByProject($selectedProject) : [];
       
    @endphp


    <div class="content-wrapper">
        <div class="row">
            <form action="{{ Request::url() }}" method="get">
                <div class="form-group">
                    <label class="customWidth">Projects</label>
                    <select name="project_name" class="form-control site_select">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->project }}" {{ request()->get('project_name') == $project->project ? 'selected' : '' }}>
                                {{ $project->project }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @if($selectedProject)
                    <div class="form-group">
                        <label class="customWidth">Source</label>
                        <select name="root_source" class="form-control root_source">
                            <option value="">Select a source</option>
                            @foreach($root_sources as $source)
                                <option value="{{ $source->root_source }}" {{ $selectedRootSource == $source->root_source ? 'selected' : '' }}>
                                    {{ $source->root_source }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="form-group">
                    <label class="customWidth extraMethod">Choose Date Range</label>
                    
                    <div class="d-inline-block">
                       <div class="d-flex">
                           <input type="text" name="start_date" id="search_from_date" class="form-control form-control-sm datepicker me-2" placeholder="From date" autocomplete="off" value="{{ request()->get('start_date') }}">
                            <input type="text" name="end_date" id="search_to_date" class="form-control form-control-sm datepicker" placeholder="To date" autocomplete="off"  value="{{ request()->get('end_date') }}">
                            
                            <div class="form-submit_btn">
                                <button type="submit" class="btn blue"><i class="fa fa-search"></i> Search</button>
                            </div>
                       </div>
                    </div>
               </div>
                
            </form>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Project:</strong> {{ $selectedProject ?? 'N/A' }} | 
                <strong>Source:</strong> {{ $selectedRootSource ?? 'N/A' }} | 
                <strong>Date:</strong> {{ $startDate ?? 'N/A' }} - {{ $endDate ?? 'N/A' }}
            </div>

            <div class="col-md-6">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th title="Product ID">PID</th>
                            <th>Product Name</th>
                            <th style="width: 50%;">Total Qty</th>
                            <th style="width: 20%;">Total PPI ID's</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @if(!empty($ppiData) && count($ppiData) > 0)
                        
                            <tr>
                                <td colspan="4">
                                    @php
                                        $allPpiIds = [];
                                    @endphp
                                    @foreach($ppiData as $item)
                                        @if(!empty($item->product_id))
                                            @php
                                                $ids = explode(',', $item->ppi_ids);
                                                $allPpiIds = array_merge($allPpiIds, $ids);
                                            @endphp
                                        @endif
                                    @endforeach
                                    @php
                                        $allPpiIds = array_unique($allPpiIds);
                                    @endphp
                                    @if(!empty(request()->get('root_source')))
                                        @foreach($allPpiIds as $k => $commonUncommon)
                                            <a href="javascript:void(0);"
                                               class="btn btn-info btn-sm"
                                               style="margin-right: 5px;"
                                               target="_blank">
                                                {{ $commonUncommon }}
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            
                            @foreach($ppiData as $item)
                                @if(!empty($item->product_id))
                                <tr>
                                    <td>{{ $item->product_id ?? 'N/A' }}</td>
                                    <td>{{ $item->product_name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $ppiIds = explode(',', $item->ppi_ids);
                                            $totalQty = \App\Models\PpiAccumulated::calculateTotalQty($ppiIds, $item->product_id ?? 0);
                                        @endphp
                                        {{ $totalQty ?? 'N/A' }}<br/>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           class="btn btn-success btn-sm"
                                           onclick="showPpiIdsModal('{{ $item->code }}', {{ json_encode(explode(',', $item->ppi_ids)) }}, 'ppi')">
                                            {{ $item->total_count_of_ppi_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-6">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th title="Product ID">PID</th>
                            <th>Product Name</th>
                            <th style="width: 50%;">Total Qty</th>
                            <th style="width: 20%;">Total SPI ID's</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($spiData) && count($spiData) > 0)
                            
                            <tr>
                                <td colspan="4">
                                    @php
                                        $allPpiIds = [];
                                    @endphp
                                    @foreach($spiData as $item)
                                        @php
                                            $ids = explode(',', $item->ppi_ids);
                                            $allPpiIds = array_merge($allPpiIds, $ids);
                                        @endphp
                                    @endforeach
                                    @php
                                        $allPpiIds = array_unique($allPpiIds);
                                    @endphp
                                    @if(!empty(request()->get('root_source')))
                                        @foreach($allPpiIds as $k => $commonUncommon)
                                            <a href="javascript:void(0);"
                                               class="btn btn-info btn-sm"
                                               style="margin-right: 5px;"
                                               target="_blank">
                                                {{ $commonUncommon }}
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            
                            @foreach($spiData as $item)
                                @if(!empty($item->product_id))
                                <tr>
                                    <td>{{ $item->product_id ?? 'N/A' }}</td>
                                    <td>{{ $item->product_name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $spiIds = explode(',', $item->ppi_ids);
                                            $totalQty = \App\Models\PpiAccumulated::calculateSpiProductTotalQty($spiIds, $item->product_id ?? 0);
                                        @endphp
                                        {{ $totalQty ?? 'N/A' }}<br/>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           class="btn btn-success btn-sm"
                                           onclick="showPpiIdsModal('{{ $item->code }}', {{ json_encode(explode(',', $item->ppi_ids)) }}, 'spi')">
                                            {{ $item->total_count_of_ppi_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @php
                                $allPpiIds = array_unique($allPpiIds);
                            @endphp
                        @else
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="ppiIdsModal" tabindex="-1" role="dialog" aria-labelledby="ppiIdsLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ppiIdsLabel">PPI OR SPI IDs</h5>
            <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
            <!--  <span aria-hidden="true">&times;</span>-->
            <!--</button>-->
          </div>
          <div class="modal-body">
            <div id="ppiIdsContent"></div>
          </div>
        </div>
      </div>
    </div>


@endsection

@section('cusjs')
<style>
    .content-wrapper .form-group label.customWidth {
        width: 15%;
        font-size: 12px;
        font-weight: 500;
    }
    label.extraMethod {
        width: 13% !important;
    }
</style>
<script>
    jQuery(document).ready(function($) {
        $.noConflict();
        $('.site_select').select2({
            placeholder: "Select a project",
            allowClear: true
        });
        
        $('.root_source').select2({
            placeholder: "Select a root source",
            allowClear: true
        });
        
        
        
        // Initialize the datepickers
        $(".datepicker").datepicker({
            dateFormat: "dd/mm/yy",
            changeYear: true
        });
        
        
        window.showPpiIdsModal = function(whCode, ppiIds, type) {
            // Use let to reassign, not const
            type = (type === 'ppi') ? 'ppi' : 'spi';
            
            const contentDiv = document.getElementById('ppiIdsContent');
            contentDiv.innerHTML = '';
        
            if (!ppiIds || ppiIds.length === 0) {
                contentDiv.innerHTML = '<em>No PPI IDs</em>';
            } else {
                const base = whCode ? `/${whCode}` : '';
                const links = ppiIds.map(id => {
                    return `<a href="${base}/${type}/edit/${id}" target="_blank" class="btn btn-sm btn-primary m-1">${id}</a>`;
                }).join(' ');
                contentDiv.innerHTML = links;
            }
        
            $('#ppiIdsModal').modal('show');
        }

        
    });
</script>

@endsection



