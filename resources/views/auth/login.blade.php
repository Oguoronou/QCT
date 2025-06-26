@extends('layout')
@section("content")

<div class="container mt-5">
    <div class="row p-3">
        <div class="col-6 card offset-3">
                <div class="card-header bg-white">
                    <h3 class="card-text">Login</h3>

                    @if(Session::has("message"))
                        <p class="bg-primary p-2">{{ Session::get("message") }}</p>
                    @endif
                </div>
                <div class="row p-3">
                <div class="col-12">
                    <form action="{{ URL::to('login') }}" method="post">
                      @csrf
                        <div class="row">
                          <div class="col-12 mb-2">
                            <input type="text" class="form-control" name="email" placeholder="Email">
                          </div>
                          <div class="col-12 mb-2">
                            <input type="password" class="form-control" name="password" placeholder="Password">
                          </div>
                        </div>
    
                        <div class="row mt-3">
                            <button type="submit" class="btn btn-primary">Login</button>
                      </div>
                      </form>
                </div>
            </div>
            </div>
        </div>
    </div>



@endsection