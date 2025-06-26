@extends('layout')
@section("content")

  <div class="container-fluid p-0" style="height: 500px;">
    <!-- Carousel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background:linear-gradient(rgba(0, 0, 0, 0.51),rgba(0, 0, 0, 0.51)), url('3.png'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
                <div class="row p-0">
                    <div class="col-12 text-center">
                        <h1 class="text-center text-white mt-5">Nous avons créé 142 017 perdants heureux !</h1>
                        <h4 class="text-center text-white mt-3">QCT réunit les gens avec les objets perdus et trouvés</h4>
                        <p class="text-center text-white mt-3">Rejoignez notre communauté et retrouvez vos objets perdus ou aidez à réunir des personnes avec leurs biens précieux.</p>
                        <form action="" method="post">
                            <div class="row justify-content-center" style="margin-top: 100px">
                                <div class="col-auto text-center">
                                    <div class="input-group text-center">
                                        {{-- <a href="{{ url('add-item') }}" style="border: 2px solid #4153f1d2;color: white;padding: 10px;border-radius: 5px;">J'ai perdu...</a>
                                        <a href="{{ url('add-found-item') }}" class="btn btn-success ml-5 text-center">J'ai retrouvé...</a> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="background:linear-gradient(rgba(0, 0, 0, 0.51),rgba(0, 0, 0, 0.51)), url('2.png'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
                <div class="row p-0">
                    <div class="col-12 text-center">
                        <h1 class="text-center text-white mt-5">Retrouvez vos objets perdus facilement !</h1>
                        <h4 class="text-center text-white mt-3">Notre plateforme vous aide à retrouver vos biens précieux</h4>
                        <p class="text-center text-white mt-3">Que vous ayez perdu un téléphone, une tablette ou tout autre objet, nous sommes là pour vous aider à le retrouver.</p>
                        <form action="" method="post">
                            <div class="row justify-content-center" style="margin-top: 140px">
                                <div class="col-auto text-center">
                                    <div class="input-group text-center">
                                        {{-- <a href="{{ url('add-item') }}" style="border: 2px solid #4153f1d2;color: white;padding: 10px;border-radius: 5px;">J'ai perdu...</a>
                                        <a href="{{ url('add-found-item') }}" class="btn btn-success ml-5 text-center">J'ai retrouvé...</a> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style=" background:linear-gradient(rgba(0, 0, 0, 0.51),rgba(0, 0, 0, 0.51)), url('1.png'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
                <div class="row p-0">
                    <div class="col-12 text-center">
                        <h1 class="text-center text-white mt-5">Aidez à réunir les objets trouvés avec leurs propriétaires !</h1>
                        <h4 class="text-center text-white mt-3">Votre aide peut faire une grande différence</h4>
                        <p class="text-center text-white mt-3">Si vous avez trouvé un objet, publiez-le sur notre plateforme pour aider à réunir les gens avec leurs biens perdus.</p>
                        <form action="" method="post">
                            <div class="row justify-content-center" style="margin-top: 140px">
                                <div class="col-auto text-center">
                                    <div class="input-group text-center">
                                        {{-- <a href="{{ url('add-item') }}" style="border: 2px solid #4153f1d2;color: white;padding: 10px;border-radius: 5px;">J'ai perdu...</a>
                                        <a href="{{ url('add-found-item') }}" class="btn btn-success ml-5 text-center">J'ai retrouvé...</a> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <!-- End of Carousel -->

   
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 mb-5">
            <h1 class="text-center">Comment fonctionne <span class="text-primary">QCT</span></h1>
            <p class="text-center">QCT est une plateforme dédiée à aider les gens à retrouver leurs objets perdus et à réunir les objets trouvés avec leurs propriétaires. Nous croyons en la puissance de la communauté pour faire une différence.</p>
            <div class="text-center">
                <img src="{{asset('2.png')}}" alt="Comment fonctionne QCT" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="album py-5 bg-light">
  <div class="container">
    <div class="row">
      <div class="col-12 mb-5">
        <h2 class="text-center">Personnes Disparues</h2>
        <p class="text-center">Découvrez les personnes disparues récemment signalées. Votre aide peut faire une grande différence pour les familles et les amis à la recherche de leurs proches.</p>
      </div>
      @foreach ($persons as $key=>$person)
        @if ($key < 6)
          <div class="col-md-4">
            <div class="card mb-4 box-shadow">
              <img class="card-img-top w-100" src="{{ asset('images/'.explode(',', $person->images)[0]) }}" alt="Image de la carte" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <span class="badge bg-danger text-white">Disparu</span>
                <h5 class="card-title">{{ $person->item_name }}</h5>
                <p class="card-text">{{ Str::limit($person->description, 20) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="{{ url('item-detail', $person->id) }}" class="btn btn-success">Détail</a>
                  </div>
                  <small class="text-muted">{{ $person->created_at->diffForHumans(['locale' => 'fr']) }}</small>
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>
    <div class="row">
      <div class="col-12 text-center">
        <a href="{{ url('/all-items?category=personne') }}" class="btn btn-success">Voir plus</a>
      </div>
    </div>
  </div>
</div>

<div class="album py-5 bg-light">
  <div class="container">
    <div class="row">
      <div class="col-12 mb-5">
        <h2 class="text-center">Objets Perdus et Retrouvés</h2>
        <p class="text-center">Parcourez les objets récemment perdus et retrouvés. Si vous avez trouvé un objet ou si vous cherchez quelque chose que vous avez perdu, vous êtes au bon endroit.</p>
      </div>
      @foreach ($items as $key=>$item)
        <div class="col-md-4">
          <div class="card mb-4 box-shadow">
            <img class="card-img-top w-100" src="{{ asset('images/'.explode(',', $item->images)[0]) }}" alt="Image de la carte" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <span class="badge {{ $item->status == 'lost' ? 'bg-danger' : 'bg-success' }} text-white">
                {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
              </span>
              <h5 class="card-title">{{ $item->item_name }}</h5>
              <p class="card-text">{{ Str::limit($item->description, 20) }}</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="{{ url('item-detail', $item->id) }}" class="btn btn-success">Détail</a>
                </div>
                <small class="text-muted">{{ $item->created_at->diffForHumans(['locale' => 'fr']) }}</small>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-8 offset-2 text-center p-5">
            <h3 class="text-center">Soutenez la plateforme en faisant des dons.</h3>
            <p class="text-center">Votre soutien nous aide à maintenir et à améliorer notre service pour aider plus de gens à retrouver leurs objets perdus et à réunir les objets trouvés avec leurs propriétaires.</p>
            <form id="cinetpay-form" action="javascript:void(0);" method="POST">
                @csrf
                <div class="form-group">
                    <input type="number" class="form-control" name="amount" id="amount" placeholder="Montant du don" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="customer_surname" id="customer_surname" placeholder="Votre prénom" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="customer_email" id="customer_email" placeholder="Votre email" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="customer_phone_number" id="customer_phone_number" placeholder="Votre numéro de téléphone" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="customer_address" id="customer_address" placeholder="Votre adresse" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="customer_city" id="customer_city" placeholder="Votre ville" required>
                </div>
                <div class="form-group">
                  <select class="form-control" name="customer_country" id="customer_country" required>
                    <option value="CI">Cote d'ivoire </option>
                  </select>
                </div>
                <button type="submit" class="btn btn-success mt-3" onclick="checkout()">Faire un don</button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card shadow-lg p-4 rounded-lg bg-light">
              <h3 class="text-center mb-4 text-primary">Contactez-nous</h3>
              
              @if (Session::has('messages'))
                  <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('messages') }}</p>
              @endif
              
              <form action="{{ url('contact-us') }}" method="POST">
                  @csrf
                  <div class="row mb-3">
                      <div class="col-12 col-md-6">
                          <input type="text" class="form-control rounded-pill shadow-sm" name="name" placeholder="Votre nom" required>
                      </div>
                      <div class="col-12 col-md-6">
                          <input type="email" class="form-control rounded-pill shadow-sm" name="email" placeholder="Votre email" required>
                      </div>
                  </div>
                  
                  <div class="row mb-3">
                      <div class="col-12">
                          <textarea class="form-control rounded-3 shadow-sm" name="message" placeholder="Votre message" rows="4" required></textarea>
                      </div>
                  </div>
                  
                  <div class="row justify-content-center mt-3">
                      <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">Envoyer le message</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script>
  function checkout() {
    var amount = document.getElementById('amount').value;
    var customer_name = document.getElementById('customer_name').value;
    var customer_surname = document.getElementById('customer_surname').value;
    var customer_email = document.getElementById('customer_email').value;
    var customer_phone_number = document.getElementById('customer_phone_number').value;
    var customer_address = document.getElementById('customer_address').value;
    var customer_city = document.getElementById('customer_city').value;
    var customer_country = document.getElementById('customer_country').value;
    CinetPay.setConfig({
        apikey: '{{ env('CINETPAY_API_KEY') }}',
        site_id: '{{ env('CINETPAY_SITE_ID') }}',
        notify_url: 'http://127.0.0.1:8000/add-item',
        mode: 'PRODUCTION'
    });
    CinetPay.getCheckout({
        "transaction_id": Math.floor(Math.random() * 100000000).toString(),
        "amount": amount,
        "currency": "XOF",
        "description": "Donation",
        "customer_id": "123",
        "customer_name": customer_name,
        "customer_surname": customer_surname,
        "customer_email": customer_email,
        "customer_phone_number": customer_phone_number,
        "customer_address": customer_address,
        "customer_city": customer_city,
        "customer_country": customer_country,
        "customer_state": "CM",
        "customer_zip_code": "",
        "channels": "ALL",
    });
    CinetPay.waitResponse(function(data) {
        if (data.status == "REFUSED") {
            if (alert("Votre paiement a échoué")) {
                window.location.reload();
            }
        } else if (data.status == "ACCEPTED") {
            if (alert("Votre paiement a été effectué avec succès")) {
                window.location.reload();
            }
        }
    });
    CinetPay.onError(function(data) {
        console.log(data);
    });
  }
</script>

@endsection