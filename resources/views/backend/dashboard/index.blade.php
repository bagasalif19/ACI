@extends('backend.layout.app')
@section('title', 'Dashboard')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar  d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">Overview</li>
                </ul>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <a href="#" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">
                    Create </a>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row g-5 mb-10">

    {{-- ===== Rekap Status ===== --}}
    <div class="col-12">
        <div class="card card-flush border border-gray-300">
            <div class="card-header pt-5">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Rekap Laporan per Status</h3>
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary">Total: {{ $totalLaporan ?? 0 }}</span>
                </div>
            </div>
            <div class="card-body pt-0">
                @php
                    $labelMap = [
                        'pengajuan' => 'Pengajuan',
                        'diterima' => 'Diterima',
                        'diverifikasi' => 'Diverifikasi',
                        'dalam_penanganan' => 'Dalam Penanganan',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ];
                @endphp

                @if(empty($rekapStatus))
                    <div class="text-muted">Data status belum tersedia (cek koneksi API / endpoint rekap).</div>
                @else
                    <div class="row g-4">
                        @foreach($rekapStatus as $key => $val)
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fw-semibold">{{ $labelMap[$key] ?? $key }}</div>
                                    <div class="fs-2 fw-bold mt-1">{{ (int)$val }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Count per Kecamatan (Tabel) ===== --}}
    <div class="col-12 col-lg-5">
        <div class="card card-flush border border-gray-300 h-100">
            <div class="card-header pt-5">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Count Laporan per Kecamatan</h3>
                </div>
                <div class="card-toolbar">
                    <span class="text-muted">Urut terbesar</span>
                </div>
            </div>
            <div class="card-body pt-0">
                @if(empty($kecamatanCounts))
                    <div class="text-muted">Data kecamatan belum tersedia (cek endpoint wilayah-kecamatan / rekap-kecamatan).</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle">
                            <thead>
                                <tr class="text-muted fw-bold">
                                    <th>Kecamatan</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($kecamatanCounts, 0, 10) as $row)
                                    <tr>
                                        <td class="fw-semibold">{{ $row['nama'] }}</td>
                                        <td class="text-end fw-bold">{{ (int)$row['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-muted fs-8">Menampilkan Top 10 kecamatan.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Grafik Count Kecamatan ===== --}}
    <div class="col-12 col-lg-7">
        <div class="card card-flush border border-gray-300 h-100">
            <div class="card-header pt-5">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Grafik Laporan per Kecamatan (Top 10)</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <canvas id="chartKecamatan" height="120"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const el = document.getElementById('chartKecamatan');
            if(!el) return;

            const labels = @json($chartLabels ?? []);
            const values = @json($chartValues ?? []);

            if(labels.length === 0) return;

            new Chart(el, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: values
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        })();
    </script>
@endpush

        <div class="row gx-5 gx-xl-10 mb-xl-10">
    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-10">
        
<!--begin::Card widget 16-->
<div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-center border-0 h-md-50 mb-5 mb-xl-10" style="background-color: #080655">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <div class="card-title d-flex flex-column">   
            <!--begin::Amount-->
            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">69</span>
            <!--end::Amount-->

            <!--begin::Subtitle-->
            <span class="text-white opacity-50 pt-1 fw-semibold fs-6">Active Projects</span>             
            <!--end::Subtitle--> 
        </div>
        <!--end::Title-->         
    </div>
    <!--end::Header-->

    <!--begin::Card body-->
    <div class="card-body d-flex align-items-end pt-0">
        <!--begin::Progress-->
        <div class="d-flex align-items-center flex-column mt-3 w-100">
            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-50 w-100 mt-auto mb-2">
                <span>43 Pending</span>
                <span>72%</span>
            </div>

            <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                <div class="bg-danger rounded h-8px" role="progressbar" style="width: 72%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <!--end::Progress-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card widget 16-->
        
<!--begin::Card widget 7-->
<div class="card card-flush h-md-50 mb-5 mb-xl-10 border border-gray-300">
    <!--begin::Header-->
    <div class="card-header pt-5 ">
        <!--begin::Title-->
        <div class="card-title d-flex flex-column">                
            <!--begin::Amount-->
            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">357</span>
            <!--end::Amount-->           

            <!--begin::Subtitle-->
            <span class="text-gray-500 pt-1 fw-semibold fs-6">Professionals</span>
            <!--end::Subtitle--> 
        </div>
        <!--end::Title-->           
    </div>
    <!--end::Header-->

    <!--begin::Card body-->
    <div class="card-body d-flex flex-column justify-content-end pe-0">
        <!--begin::Title-->
        <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Today’s Heroes</span>
        <!--end::Title-->

        <!--begin::Users group-->
        <div class="symbol-group symbol-hover flex-nowrap">
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="Alan Warden" data-kt-initialized="1">
                                            <span class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                    </div>
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" aria-label="Michael Eberon" data-bs-original-title="Michael Eberon" data-kt-initialized="1">
                                            <img alt="Pic" src="/assets/media/avatars/300-11.jpg">
                                    </div>
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="Susan Redwood" data-kt-initialized="1">
                                            <span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                    </div>
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" aria-label="Melody Macy" data-bs-original-title="Melody Macy" data-kt-initialized="1">
                                            <img alt="Pic" src="/assets/media/avatars/300-2.jpg">
                                    </div>
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="Perry Matthew" data-kt-initialized="1">
                                            <span class="symbol-label bg-danger text-inverse-danger fw-bold">P</span>
                                    </div>
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" aria-label="Barry Walter" data-bs-original-title="Barry Walter" data-kt-initialized="1">
                                            <img alt="Pic" src="/assets/media/avatars/300-12.jpg">
                                    </div>
                        <a href="#" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                <span class="symbol-label bg-dark text-gray-300 fs-8 fw-bold">+42</span>
            </a>
        </div>
        <!--end::Users group-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card widget 7-->    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-10">
        
<!--begin::Card widget 17-->
<div class="card card-flush h-md-50 mb-5 mb-xl-10 border border-gray-300">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <div class="card-title d-flex flex-column">   
            <!--begin::Info--> 
            <div class="d-flex align-items-center">
                <!--begin::Currency-->
                <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start">$</span>
                <!--end::Currency-->

                <!--begin::Amount-->
                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">69,700</span>
                <!--end::Amount-->

                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">                                
                    <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                    2.2%
                </span>            
                <!--end::Badge--> 
            </div>
            <!--end::Info--> 

            <!--begin::Subtitle-->
            <span class="text-gray-500 pt-1 fw-semibold fs-6">Projects Earnings in April</span>
            <!--end::Subtitle--> 
        </div>
        <!--end::Title-->      
    </div>
    <!--end::Header-->

    <!--begin::Card body-->
    <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
        <!--begin::Chart-->
        <div class="d-flex flex-center me-5 pt-2">
            <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11">
            <span></span><canvas height="70" width="70"></canvas></div>
        </div>
        <!--end::Chart-->
        
        <!--begin::Labels-->
        <div class="d-flex flex-column content-justify-center flex-row-fluid">
            <!--begin::Label-->
            <div class="d-flex fw-semibold align-items-center">
                <!--begin::Bullet-->
                <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                <!--end::Bullet-->

                <!--begin::Label-->
                <div class="text-gray-500 flex-grow-1 me-4">Leaf CRM</div>
                <!--end::Label-->

                <!--begin::Stats-->
                <div class="fw-bolder text-gray-700 text-xxl-end">$7,660</div>
                <!--end::Stats-->
            </div>
            <!--end::Label-->

            <!--begin::Label-->
            <div class="d-flex fw-semibold align-items-center my-3">
                <!--begin::Bullet-->
                <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                <!--end::Bullet-->

                <!--begin::Label-->
                <div class="text-gray-500 flex-grow-1 me-4">Mivy App</div>
                <!--end::Label-->

                <!--begin::Stats-->
                <div class="fw-bolder text-gray-700 text-xxl-end">$2,820</div>
                <!--end::Stats-->                    
            </div>
            <!--end::Label-->

            <!--begin::Label-->
            <div class="d-flex fw-semibold align-items-center">
                <!--begin::Bullet-->
                <div class="bullet w-8px h-3px rounded-2 me-3" style="background-color: #E4E6EF"></div>
                <!--end::Bullet-->

                <!--begin::Label-->
                <div class="text-gray-500 flex-grow-1 me-4">Others</div>
                <!--end::Label-->

                <!--begin::Stats-->
                <div class=" fw-bolder text-gray-700 text-xxl-end">$45,257</div>
                <!--end::Stats-->                      
            </div>
            <!--end::Label-->
        </div>
        <!--end::Labels-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card widget 17-->
        
<!--begin::List widget 25-->
<div class="card card-flush h-lg-50 border border-gray-300">  
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <h3 class="card-title text-gray-800">Highlights</h3>
        <!--end::Title-->

        <!--begin::Toolbar-->
        <div class="card-toolbar d-none">          
            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm btn-light d-flex align-items-center px-4" data-kt-initialized="1">           
                <!--begin::Display range-->
                <div class="text-gray-600 fw-bold">4 Nov 2025 - 3 Dec 2025</div>
                <!--end::Display range-->

                <i class="ki-outline ki-calendar-8 fs-1 ms-2 me-0"></i>          
            </div>  
            <!--end::Daterangepicker-->                         
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div class="card-body pt-5">                 
                    <!--begin::Item-->
            <div class="d-flex flex-stack">
                <!--begin::Section-->
                <div class="text-gray-700 fw-semibold fs-6 me-2">Avg. Client Rating</div>                   
                <!--end::Section-->  

                <!--begin::Statistics--> 
                <div class="d-flex align-items-senter">
                                            <i class="ki-outline ki-arrow-up-right fs-2 text-success me-2"></i>  
                                        
                    <!--begin::Number-->           
                    <span class="text-gray-900 fw-bolder fs-6">7.8</span> 
                    <!--end::Number-->  
                    
                                            <span class="text-gray-500 fw-bold fs-6">/10</span> 
                                    </div>  
                <!--end::Statistics-->           
            </div>
            <!--end::Item-->

                            <!--begin::Separator-->
                <div class="separator separator-dashed my-3"></div>
                <!--end::Separator-->
             
                    <!--begin::Item-->
            <div class="d-flex flex-stack">
                <!--begin::Section-->
                <div class="text-gray-700 fw-semibold fs-6 me-2">Avg. Quotes</div>                   
                <!--end::Section-->  

                <!--begin::Statistics--> 
                <div class="d-flex align-items-senter">
                                            <i class="ki-outline ki-arrow-down-right fs-2 text-danger me-2"></i>                 
                                        
                    <!--begin::Number-->           
                    <span class="text-gray-900 fw-bolder fs-6">730</span> 
                    <!--end::Number-->  
                    
                                                             
                                    </div>  
                <!--end::Statistics-->           
            </div>
            <!--end::Item-->

                            <!--begin::Separator-->
                <div class="separator separator-dashed my-3"></div>
                <!--end::Separator-->
             
                    <!--begin::Item-->
            <div class="d-flex flex-stack">
                <!--begin::Section-->
                <div class="text-gray-700 fw-semibold fs-6 me-2">Avg. Agent Earnings</div>                   
                <!--end::Section-->  

                <!--begin::Statistics--> 
                <div class="d-flex align-items-senter">
                                            <i class="ki-outline ki-arrow-up-right fs-2 text-success me-2"></i>  
                                        
                    <!--begin::Number-->           
                    <span class="text-gray-900 fw-bolder fs-6">$2,309</span> 
                    <!--end::Number-->  
                    
                                                             
                                    </div>  
                <!--end::Statistics-->           
            </div>
            <!--end::Item-->

             
            
    </div>
    <!--end::Body-->
</div>
<!--end::LIst widget 25-->


     </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-lg-12 col-xl-12 col-xxl-6 mb-10 mb-xl-0">
        
<!--begin::Timeline widget 3-->
<div class="card h-md-100 border border-gray-300">
    <!--begin::Header-->
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold text-gray-900">What’s up Today</span>

			<span class="text-muted mt-1 fw-semibold fs-7">Total 424,567 deliveries</span>
		</h3>

        <!--begin::Toolbar-->
        <div class="card-toolbar">   
            <a href="#" class="btn btn-sm btn-light">Report Cecnter</a>             
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div class="card-body pt-7 px-0">
        <!--begin::Nav-->             
        <ul class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5" role="tablist">
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_1" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Fr</span>
                        <span class="fs-6 fw-bold">20</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_2" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Sa</span>
                        <span class="fs-6 fw-bold">21</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_3" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Su</span>
                        <span class="fs-6 fw-bold">22</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger active" data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_4" aria-selected="true" role="tab">
                        <span class="fs-7 fw-semibold">Tu</span>
                        <span class="fs-6 fw-bold">23</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_5" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Tu</span>
                        <span class="fs-6 fw-bold">24</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_6" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">We</span>
                        <span class="fs-6 fw-bold">25</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_7" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Th</span>
                        <span class="fs-6 fw-bold">26</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_8" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Fri</span>
                        <span class="fs-6 fw-bold">27</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_9" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Sa</span>
                        <span class="fs-6 fw-bold">28</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_10" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Su</span>
                        <span class="fs-6 fw-bold">29</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                                            <!--begin::Nav item-->
                <li class="nav-item p-0 ms-0" role="presentation">
                    <!--begin::Date-->
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger " data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_11" aria-selected="false" tabindex="-1" role="tab">
                        <span class="fs-7 fw-semibold">Mo</span>
                        <span class="fs-6 fw-bold">30</span>                      
                    </a>             
                    <!--end::Date-->       
                </li>
                <!--end::Nav item-->
                               
        </ul>             
        <!--end::Nav-->

        <!--begin::Tab Content (ishlamayabdi)-->
        <div class="tab-content mb-2 px-9">
                                                        <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_1" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_2" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_3" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-primary"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade show active" id="kt_timeline_widget_3_tab_content_4" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_5" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-danger"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_6" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-primary"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_7" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-danger"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_8" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-danger"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_9" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-primary"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-success"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_10" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-danger"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-warning"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
                                            <!--begin::Tap pane-->
                <div class="tab-pane fade " id="kt_timeline_widget_3_tab_content_11" role="tabpanel">
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-info"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    16:30 - 17:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        PM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Dashboard UI/UX Design Review                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Mark Morris</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-danger"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    10:20 - 11:00
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    Marketing Campaign Discussion                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Peter Marcus</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                     
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Bullet-->
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-primary"></span>
                            <!--end::Bullet-->
                            
                            <!--begin::Info-->
                            <div class="flex-grow-1 me-5">
                                <!--begin::Time-->
                                <div class="text-gray-800 fw-semibold fs-2">
                                    12:00 - 13:40
                                    <span class="text-gray-500 fw-semibold fs-7">
                                        AM                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Description-->
                                <div class="text-gray-700 fw-semibold fs-6">
                                    9 Degree Project Estimation Meeting                                </div>
                                <!--end::Description-->

                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-7">
                                    Lead by 
                                    <!--begin::Name-->
                                    <a href="#" class="text-primary opacity-75-hover fw-semibold">Lead by Bob</a>                  
                                    <!--end::Name-->  
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->                            
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">View</a>                               
                            <!--end::Action-->  
                        </div>
                        <!--end::Wrapper-->
                                    </div>
                <!--end::Tap pane-->    
              
        </div>
        <!--end::Tab Content-->   
        
        <!--begin::Action-->
        <div class="float-end d-none">
            <a href="#" class="btn btn-sm btn-light me-2" data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">Add Lesson</a>

            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app">Call Sick for Today</a>
        </div>
        <!--end::Action-->  
    </div>
    <!--end: Card Body-->
</div>
<!--end::Timeline widget 3--> 

 


<!--begin::Timeline widget 3-->
<div class="card card-flush d-none h-md-100">
    <!--begin::Card header-->
    <div class="card-header mt-6">
        <!--begin::Card title-->
        <div class="card-title flex-column">
            <h3 class="fw-bold mb-1">What's on the road?</h3>

            <div class="fs-6 text-gray-500">Total 482 participants</div>
        </div>
        <!--end::Card title-->

        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Select-->
            <select name="status" data-control="select2" data-hide-search="true" class="form-select form-select-solid form-select-sm fw-bold w-100px select2-hidden-accessible" data-select2-id="select2-data-3-eo9b" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                <option value="1" selected="" data-select2-id="select2-data-5-vsyy">Options</option>
                <option value="2">Option 1</option>
                <option value="3">Option 2</option>
                <option value="4">Option 3</option>
            </select><span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-4-xnhf" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-solid form-select-sm fw-bold w-100px" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-status-5v-container" aria-controls="select2-status-5v-container"><span class="select2-selection__rendered" id="select2-status-5v-container" role="textbox" aria-readonly="true" title="Options">Options</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
            <!--end::Select-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body p-0">
        <!--begin::Dates-->
        <ul class="nav nav-pills d-flex flex-nowrap hover-scroll-x py-2 ms-4" role="tablist">
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_0" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Fr</span>
                        <span class="fs-6 text-gray-800 fw-bold">20</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_1" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Sa</span>
                        <span class="fs-6 text-gray-800 fw-bold">21</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_2" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Su</span>
                        <span class="fs-6 text-gray-800 fw-bold">22</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger active" data-bs-toggle="tab" href="#kt_schedule_day_3" aria-selected="true" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Mo</span>
                        <span class="fs-6 text-gray-800 fw-bold">23</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_4" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Tu</span>
                        <span class="fs-6 text-gray-800 fw-bold">24</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_5" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">We</span>
                        <span class="fs-6 text-gray-800 fw-bold">25</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_6" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Th</span>
                        <span class="fs-6 text-gray-800 fw-bold">26</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_7" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Fr</span>
                        <span class="fs-6 text-gray-800 fw-bold">27</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_8" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Sa</span>
                        <span class="fs-6 text-gray-800 fw-bold">28</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_9" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Su</span>
                        <span class="fs-6 text-gray-800 fw-bold">29</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_10" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Mo</span>
                        <span class="fs-6 text-gray-800 fw-bold">30</span>
                    </a>
                </li>
                <!--end::Date-->
                            
                <!--begin::Date-->
                <li class="nav-item me-1" role="presentation">
                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px me-2 py-4 px-3 btn-color-active-white btn-active-danger " data-bs-toggle="tab" href="#kt_schedule_day_11" aria-selected="false" tabindex="-1" role="tab">
                        
                        <span class="text-gray-500 fs-7 fw-semibold">Tu</span>
                        <span class="fs-6 text-gray-800 fw-bold">31</span>
                    </a>
                </li>
                <!--end::Date-->
                    </ul>
        <!--end::Dates-->

        <!--begin::Tab Content-->
        <div class="tab-content px-9">
                            <!--begin::Day-->
                <div id="kt_schedule_day_0" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    13:00 - 14:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Yannis Gloverson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Weekly Team Stand-Up                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Bob Harris</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Marketing Campaign Discussion                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Mark Randall</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_1" class="tab-pane fade show active" role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    16:30 - 17:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Committee Review Approvals                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Peter Marcus</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Creative Content Initiative                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Walter White</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Marketing Campaign Discussion                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_2" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Development Team Capacity Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Karina Clarke</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Bob Harris</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_3" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    16:30 - 17:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Sales Pitch Proposal                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Yannis Gloverson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Committee Review Approvals                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">David Stevenson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_4" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    16:30 - 17:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Development Team Capacity Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">David Stevenson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Development Team Capacity Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Mark Randall</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_5" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    13:00 - 14:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">David Stevenson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    10:00 - 11:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Committee Review Approvals                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Caleb Donaldson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_6" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Mark Randall</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    11:00 - 11:45 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Weekly Team Stand-Up                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Karina Clarke</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_7" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Committee Review Approvals                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Peter Marcus</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Team Backlog Grooming Session                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Bob Harris</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    13:00 - 14:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Mark Randall</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_8" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    11:00 - 11:45 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Naomi Hayabusa</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    11:00 - 11:45 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Development Team Capacity Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Terry Robins</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Weekly Team Stand-Up                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Terry Robins</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_9" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    16:30 - 17:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Terry Robins</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    14:30 - 15:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Terry Robins</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Sales Pitch Proposal                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Peter Marcus</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_10" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    9 Degree Project Estimation Meeting                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Walter White</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    16:30 - 17:30 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Project Review &amp; Testing                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Michael Walters</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    13:00 - 14:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    9 Degree Project Estimation Meeting                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Caleb Donaldson</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                            <!--begin::Day-->
                <div id="kt_schedule_day_11" class="tab-pane fade show " role="tabpanel">
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Creative Content Initiative                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Sean Bean</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    9:00 - 10:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        am                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Weekly Team Stand-Up                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Bob Harris</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                                                    <!--begin::Time-->
                        <div class="d-flex flex-stack position-relative mt-8">
                            <!--begin::Bar-->
                            <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                            <!--end::Bar-->

                            <!--begin::Info-->
                            <div class="fw-semibold ms-5 text-gray-600">
                                <!--begin::Time-->
                                <div class="fs-5">
                                    12:00 - 13:00 

                                    <span class="fs-7 text-gray-500 text-uppercase">
                                        pm                                    </span>
                                </div>
                                <!--end::Time-->

                                <!--begin::Title-->
                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                    Dashboard UI/UX Design Review                                </a>
                                <!--end::Title-->

                                <!--begin::User-->
                                <div class="text-gray-500">
                                    Lead by <a href="#">Terry Robins</a>
                                </div>
                                <!--end::User-->
                            </div>
                            <!--end::Info-->

                            <!--begin::Action-->
                            <a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm">View</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Time-->
                                    </div>
                <!--end::Day-->
                    </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Timeline widget-3-->    </div>
    <!--end::Col--> 
</div>
    </div>
    @push('scripts')
      
    @endpush


@endsection
