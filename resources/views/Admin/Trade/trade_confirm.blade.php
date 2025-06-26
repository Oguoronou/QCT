@extends('CustomerPanel.layout')
@section('content')
    


<main id="main" class="main">

    <div class="pagetitle mb-3">
      <h1>Confirmer l'échange</h1>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">

        <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Aperçu</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                 <div class="row">
                        <div class="col-6">
                            <h5 class="card-title">Détails de la facture</h5>
    
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label ">Facture #</div>
                              <div class="col-lg-9 col-md-8">{{ $invoice_no ?? "12312" }}</div>
                            </div>
          
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">Échange de</div>
                              <div class="col-lg-9 col-md-8">{{ $from_currency ?? "12312" }}</div>
                            </div>
          
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">Prix en USD</div>
                              <div class="col-lg-9 col-md-8">${{ $price_of_currency_in_usd ?? "12312" }}</div>
                            </div>
                        </div>
    
                        <div class="col-6">
                            <h5 class="card-title">Détails de l'échange</h5>
    
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label ">De la monnaie</div>
                              <div class="col-lg-9 col-md-8">{{ $from_currency ?? "btc" }}</div>
                            </div>
          
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">À la monnaie</div>
                              <div class="col-lg-9 col-md-8">{{ $to_currency ?? "usd" }}</div>
                            </div>
          
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">Quantité</div>
                              <div class="col-lg-9 col-md-8">{{ $quantity ?? "1" }} <small>- {{ $from_currency ?? "BTC" }}</small></div>
                            </div>
          
                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">Prix en USD</div>
                              <div class="col-lg-9 col-md-8">${{ $price_of_currency_in_usd ?? "32432" }}</div>
                            </div>
          
                            <div class="row">
                                <div class="col-lg-3 col-md-4 label">Commission</div>
                                <div class="col-lg-9 col-md-8">${{ $price_of_commission ?? "234" }}</div>
                              </div>

                            <div class="row">
                              <div class="col-lg-3 col-md-4 label">Montant total</div>
                              <div class="col-lg-9 col-md-8">${{ $total_price ?? "3523423" }}</div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form>
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Image de profil</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="{{ asset('customerdesign/assets/img/profile-img.jpg') }}" alt="Profile">
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Télécharger une nouvelle image de profil"><i class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Supprimer mon image de profil"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nom complet</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="Zahid Akbar">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label">À propos</label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quia molestiae nemo, doloribus, eos error ratione corporis facilis culpa quasi natus expedita officia, perspiciatis nobis sed qui placeat rerum officiis autem!</textarea>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Country" class="col-md-4 col-lg-3 col-form-label">Pays</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="country" type="text" class="form-control" id="Country" value="Pakistan">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Adresse</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="Address" value="Ward # 5, Layyah">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Téléphone</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="Phone" value="+923081312527">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email" value="zahidjakhar2370@gmail.com" readonly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Profil Twitter</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="twitter" type="text" class="form-control" id="Twitter" value="https://twitter.com/#">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Profil Facebook</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="facebook" type="text" class="form-control" id="Facebook" value="https://facebook.com/#">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Profil Instagram</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="instagram" type="text" class="form-control" id="Instagram" value="https://instagram.com/#">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Profil Linkedin</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="linkedin" type="text" class="form-control" id="Linkedin" value="https://linkedin.com/#">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Notifications par email</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="changesMade" checked>
                          <label class="form-check-label" for="changesMade">
                            Modifications apportées à votre compte
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="proOffers">
                          <label class="form-check-label" for="proOffers">
                            Offres marketing et promotionnelles
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                          <label class="form-check-label" for="securityNotify">
                            Alertes de sécurité
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                  </form><!-- End settings Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Mot de passe actuel</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Nouveau mot de passe</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Ressaisir le nouveau mot de passe</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->



@endsection