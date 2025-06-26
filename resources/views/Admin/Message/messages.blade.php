@extends('Admin.layout')
@section('content')
    


<main id="main" class="main mb-5">

    <div class="pagetitle mb-3">
      <h1>View Message</h1>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
         <div class="col-xl-12">

            <div class="card">
                <div class="card-body">
                    @if(Session::has("message"))
                    <p class="bg-success p-2 text-white">{{ Session::get("message") }}</p>
                  @endif
                  <!-- Bordered Table -->
                  <table class="table table-bordered mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Message</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      @if(!empty($messages))
                        @foreach ($messages as $key => $message)
                          <tr>
                            <th scope="row">{{ $key+1 }}</th>
                            <td>{{ $message->name ?? "name" }}</td>
                            <td>{{ $message->email ?? "test@gmail.com" }}</td>
                            <td>{{ $message->message ?? "test@gmail.com" }}</td>
                            <td><span class="text-uppercase">{{ $message->status }}</span></td>
                            <td>
                                <a href="{{ url('admin/delete-message/'.$message->id) }}" class="btn btn-danger">Delete</a>
                                @if($message->status == "pending")
                                    <a href="{{ url('admin/mark-as-reply/'.$message->id) }}" class="btn btn-success">Mark as Replied</a>
                                @else
                                    <a href="{{ url('admin/mark-as-pending/'.$message->id) }}" class="btn btn-success">Mark as Pending</a>
                                @endif
                            </td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                  <!-- End Bordered Table -->
                </div>
              </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <br>
  <br>
  <br> 
  <br>


@endsection
