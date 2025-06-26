@extends('Admin.layout')
@section('content')
    



<main id="main" class="main">

    <div class="pagetitle">
      <h1>QCT Overview</h1>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">

          {{-- <div class="row mb-5">
            <div class="col-md-6">
              <label for="inputState" class="form-label">Filter</label><br>
              <select onchange="document.location.href = '/customer/dashboard?currency=' + this.value" id="inputState" class="form-select">
                 <option selected>Choose...</option> 
                 <option value="">Mobile</option>
                 <option value="">Bike</option>
                  
              </select>
              </div>
          </div> --}}

          <div class="row mt-4">

            <!-- Sales Card -->
            <div class="col-md-3">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Lost Items</h5>

                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h5>{{ $lostItems }}</h5>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            

            <!-- Sales Card -->
            <div class="col-md-3">
                <div class="card info-card sales-card">
  
                  <div class="card-body">
                    <h5 class="card-title">Found Items</h5>
  
                    <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5>{{ $foundItems }}</h5>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div><!-- End Sales Card -->

             <!-- Sales Card -->
             <div class="col-md-3">
                <div class="card info-card sales-card">
  
                  <div class="card-body">
                    <h5 class="card-title">Pending Items </h5>
  
                    <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5>{{ $pendingItems }}</h5>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div><!-- End Sales Card -->

               <!-- Sales Card -->
            <div class="col-md-3">
                <div class="card info-card sales-card">
  
                  <div class="card-body">
                    <h5 class="card-title">Deliver Items</h5>
  
                    <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5>{{ $deliverItems }}</h5>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div><!-- End Sales Card -->

                <!-- Sales Card -->
            <div class="col-md-3">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Users</h5>

                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h5>{{ $users }}</h5>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

          </div>
        </div><!-- End Left side columns -->
        <div class="row">
          
      

          <!-- Website Traffic -->
          
          </div>
         

          {{-- <!-- News & Updates Traffic -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>

              <div class="news">
                <div class="post-item clearfix">
                  <img src="assets/img/news-1.jpg" alt="">
                  <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                  <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-2.jpg" alt="">
                  <h4><a href="#">Quidem autem et impedit</a></h4>
                  <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-3.jpg" alt="">
                  <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                  <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-4.jpg" alt="">
                  <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                  <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-5.jpg" alt="">
                  <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                  <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                </div>

              </div><!-- End sidebar recent posts-->

            </div>
          </div><!-- End News & Updates --> --}}

        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->


@endsection