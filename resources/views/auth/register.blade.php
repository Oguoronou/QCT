@extends('layout')
@section("content")

<div class="container mt-5">
    <div class="row p-3">
        <div class="col-6 card offset-3">
                <div class="card-header bg-white">
                    <h3 class="card-text">Register</h3>
                    @if(Session::has("message"))
                        <p class="bg-warning p-2">{{ Session::get("message") }}</p>
                    @endif
                </div>
                <div class="row p-3">
                <div class="col-12">
                    <form action="{{ URL::to('register') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                        <div class="row">
                          <div class="col-6 mb-2">
                            <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                          </div>
                          <div class="col-6 mb-2">
                            <input type="text" class="form-control" name="mobile_no" placeholder="Mobile #" required>
                          </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-2">
                              <input type="text" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-6 mb-2">
                              <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-6 mb-2">
                              <input type="text" class="form-control" name="city" placeholder="City" required>
                            </div>
                            <div class="col-6 mb-2">
                              <input type="text" class="form-control" name="country" placeholder="Country" required>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-6 mb-2">
                              <input type="text" class="form-control" name="address" placeholder="Address" required>
                            </div>
                            <div class="col-6 mb-2">
                              <input type="file" class="form-control" name="image" placeholder="image" required>
                            </div>
                          </div>
    
                        <div class="row mt-3">
                            <button class="btn btn-primary">Register</button>
                      </div>
                      </form>
                </div>
            </div>
            </div>
        </div>
    </div>



@endsection