@extends('CustomerPanel.layout')
@section('content')

<main id="main" class="main mb-5">

    <div class="pagetitle mb-3">
      <h1>Historique des échanges</h1>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
         <div class="col-xl-12">

            <div class="card">
                <div class="card-body">
                  <!-- Bordered Table -->
                  <table class="table table-bordered mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Email</th>
                        <th scope="col">De la devise</th>
                        <th scope="col">À la devise</th>
                        <th scope="col">Prix actuel en USD</th>
                        <th scope="col">Plateforme</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      @if(!empty($trades))
                        @foreach ($trades as $key => $trade)
                          <tr>
                            <th scope="row">{{ $key+1 }}</th>
                            <td>{{ $trade->users->name ?? "Zahid" }}</td>
                            <td>{{ $trade->users->email ?? "customer@gmail.com" }}</td>
                            <td>{{ $trade->from_currency }}</td>
                            <td>{{ $trade->to_currency }}</td>
                            <td>{{ $trade->total_price }}</td>
                            <td>{{ $trade->plateform }}</td>
                            <td><a href="{{ url('customer/trade-detail/'.$trade->id) }}" class="btn btn-primary">Voir le détail</a></td>
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