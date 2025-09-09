@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
  <div class="containerx mt-2">
    <div class="card text-white mb-3">
      <div class="text-center bg-secondary card-header fs-4">Today's</div>
      <div class="card-body">
        <div class="row">
          <!-- Today's PPI Created -->
          <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header">Today's PPI Created</div>
              <div class="card-body">
                <h5 class="card-title" id="today-ppi-created">
                    {{ $counts['ppi']['created']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Today's PPI Closed -->
          <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
              <div class="card-header">Today's PPI Closed</div>
              <div class="card-body">
                <h5 class="card-title" id="today-ppi-closed">
                    {{ $counts['ppi']['completed']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Today's PPI Pending -->
          <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
              <div class="card-header">Today's PPI Pending</div>
              <div class="card-body">
                <h5 class="card-title" id="today-ppi-pending">
                    {{ $counts['ppi']['pending']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Today's SPI Created -->
          <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header">Today's SPI Created</div>
              <div class="card-body">
                <h5 class="card-title" id="today-spi-created">
                    {{ $counts['spi']['created']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Today's SPI Closed -->
          <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
              <div class="card-header">Today's SPI Closed</div>
              <div class="card-body">
                <h5 class="card-title" id="today-spi-closed">
                    {{ $counts['spi']['completed']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>


          <!-- Today's SPI Pending -->
          <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
              <div class="card-header">Today's SPI Pending</div>
              <div class="card-body">
                <h5 class="card-title" id="today-spi-pending">
                    {{ $counts['spi']['pending']['today'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>

    <div class="card text-white mb-3">
      <div class="text-center bg-secondary card-header fs-4">Overall</div>
      <div class="card-body">
        <div class="row">
          <!-- Total PPI Created -->
          <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header">Total PPI Created</div>
              <div class="card-body">
                <h5 class="card-title" id="total-ppi-created">
                    {{ $counts['ppi']['created']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Total PPI Closed -->
          <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
              <div class="card-header">Total PPI Closed</div>
              <div class="card-body">
                <h5 class="card-title" id="total-ppi-closed">
                    {{ $counts['ppi']['completed']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Total PPI Pending -->
          <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
              <div class="card-header">Total PPI Pending</div>
              <div class="card-body">
                <h5 class="card-title" id="total-ppi-pending">
                    {{ $counts['ppi']['pending']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Total SPI Created -->
          <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header">Total SPI Created</div>
              <div class="card-body">
                <h5 class="card-title" id="total-spi-created">
                    {{ $counts['spi']['created']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Total SPI Closed -->
          <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
              <div class="card-header">Total SPI Closed</div>
              <div class="card-body">
                <h5 class="card-title" id="total-spi-closed">
                    {{ $counts['spi']['completed']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>

          <!-- Total SPI Pending -->
          <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
              <div class="card-header">Total SPI Pending</div>
              <div class="card-body">
                <h5 class="card-title" id="total-spi-pending">
                    {{ $counts['spi']['pending']['overall'] }}
                </h5>
                <p>All warehouse</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection