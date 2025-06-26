@extends('CustomerPanel.layout')
@section('content')
    


<main id="main" class="main mb-5">

    <div class="pagetitle mb-3">
      <h1>Trade</h1>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
         <div class="col-xl-12">

            <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Trade Now</h5>
    
                  <!-- Multi Columns Form -->
                  <form class="row g-3" action="{{ URL::to('customer/confirm-trade') }}" method="post">
                    @csrf
                    <div class="col-md-6">
                        <label for="inputState" class="form-label"><b>Select Currency</b></label><br>
                        <select id="selectCurrency" class="form-select selectCurrency" onchange="selectCurrency(this.value)" name="from_currency">
                           {{-- <option selected>Choose...</option>  --}}
                           @if(!empty($currencys))
                              @foreach($currencys as $currency)
                                @if($currency->symbol == "BTC")
                                  <option value="{{ $currency->symbol }}" selected>{{ $currency->currency_name }} - {{ $currency->symbol }}</option>

                                @else
                                <option value="{{ $currency->symbol }}">{{ $currency->currency_name }} - {{ $currency->symbol }}</option>
                                @endif
                              @endforeach
                            @endif
                        </select>
                        </div>

                    <div class="col-md-2">
                        <label for="inputState" class="form-label"><b>Trade Currency</b></label><br>

                        <input type="radio" class="btn-check" name="to_currency" id="success-outlined" autocomplete="off" value="usd" checked>
                        <label class="btn btn-outline-success" for="success-outlined">USD</label>
                        
                        <input type="radio" class="btn-check" value="euro" name="to_currency" id="dark-outlined" autocomplete="off">
                        <label class="btn btn-outline-dark" for="dark-outlined">EURO</label>
                      </div>

                      <div class="col-md-3">
                        <label for="inputState" class="form-label"><b>Best Price</b></label><br>
                        <input type="text" value="$ {{ $bestPrice }}" class="form-control" id="bestPrice" name="best_price" readonly>

                      </div>
                    

                    <div class="col-12">
                            <label for="inputState" class="form-label"><b>Want to Trade in?</b></label>
                    </div>
<div class="row">
                    <div class="col-3">
                          <input type="radio" class="btn-check getBinance"  name="want_to_trade_in" id="secondary-outlined" autocomplete="off" value="binance" @if($bestPrice == $binancePrice) checked @endif >
                          <label class="btn btn-outline-secondary getBinance" for="secondary-outlined" title="${{ $binancePrice }}"><img src="{{ asset('binanace.png') }}" style="width: 30px;height:30px" >
                            Binance</label>
                      
                        </div>
                    

                    <div class="col-3">
                        <input type="radio" class="btn-check getCrypto" value="crypto" name="want_to_trade_in" id="danger-outlined" autocomplete="off" @if($bestPrice == $cryptoPrice) checked @endif>
                        <label class="btn btn-outline-danger getCrypto" for="danger-outlined" title="${{ $cryptoPrice }}"><img src="{{ asset('crypto.png')}}" style="width: 30px;height:30px">Crypto</label>
                    
                    </div>

                    <div class="col-3">
                        <input type="radio" class="btn-check getBitfinix" value="bitfinix" name="want_to_trade_in" id="primary-outlined" autocomplete="off" @if($bestPrice == $bitfinixPrice) checked @endif>
                        <label class="btn btn-outline-primary getBitfinix" for="primary-outlined" title="${{ $bitfinixPrice }}"><img src="{{ asset('bitfinex.png')}}" style="width: 30px;height:30px">Bitfinex</label>
                    
                    </div>

                    <div class="col-3">
                        <input type="radio" class="btn-check getKraken" value="kraken" name="want_to_trade_in" id="info-outlined" autocomplete="off" @if($bestPrice == $krakenPrice) checked @endif>
                        <label class="btn btn-outline-info getKraken" for="info-outlined" title="${{ $krakenPrice }}"><img src="{{ asset('kraken.png')}}" style="width: 30px;height:30px"> Kraken</label>
                    
                    </div>
                  </div>
                    <div class="col-md-4">
                      <label for="inputPassword5" class="form-label"><b>Quantity</b></label>
                      <input type="number" class="form-control" name="quantity" id="inputPassword5" value="1" min="1">
                    </div>


                    <div class="col-md-6">
                      <label for="inputState" class="form-label"><b>Wallet</b></label><br>

                      <select name="my_wallet" class="form-control" id="my_wallet" required>
                        <option value="">Select Wallet</option>

                        @if(!empty($wallets))
                            @foreach ($wallets as $wallet)
                              <option value="{{ $wallet->id }}">{{ $wallet->wallet_name }}</option>
                            @endforeach
                        @endif
                      </select>
                    </div>

                    
                    <div class="col-md-4">
                      <label for="inputState" class="form-label"><b>Trade Type</b></label><br>

                      <input type="radio" class="btn-check" name="trade_type" id="warning-outlined" autocomplete="off" value="sell" checked>
                      <label class="btn btn-outline-warning" for="warning-outlined">SELL</label>
                      
                      <input type="radio" class="btn-check" value="buy" name="trade_type" id="link-outlined" autocomplete="off">
                      <label class="btn btn-outline-link" for="link-outlined">BUY</label>
                    </div>
                    <div class="text-left mt-5">
                      <button type="submit" class="btn btn-primary">Trade</button>
                      <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                  </form><!-- End Multi Columns Form -->
    
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

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

  <script>


$('.selectCurrency').change(
    function(){
      
      var currencyValue = document.getElementById("selectCurrency").value;
      $.ajax({
                url: 'get-selected-currency-data/'+currencyValue,
                type: 'GET',
                // dataType: 'json',
                success: function(data) {
                   document.getElementById("bestPrice").value = "$ " + data.bestPrice
                }
            });
    });

    // $('.changePlateform').change(
    // function(){

    //   var currencyValue = document.getElementById("selectCurrency").value;
    //   $.ajax({
    //             url: 'get-selected-currency-data/'+currencyValue,
    //             type: 'GET',
    //             // dataType: 'json',
    //             success: function(data) {
    //                document.getElementById("bestPrice").value = "$ " + data.binancePrice
    //             }
    //         });
    //   // var binanceValue = {{ $binancePrice }};
    //   //   document.getElementById("bestPrice").value = "$ "+binanceValue;
    // });


      $('.getBinance').change(
    function(){
      var currencyValue = document.getElementById("selectCurrency").value;
      $.ajax({
                url: 'get-selected-currency-data/'+currencyValue,
                type: 'GET',
                // dataType: 'json',
                success: function(data) {
                   document.getElementById("bestPrice").value = "$ " + data.binancePrice
                }
            });
    });

    $('.getCrypto').change(
    function(){
      var currencyValue = document.getElementById("selectCurrency").value;

      $.ajax({
                url: 'get-selected-currency-data/'+currencyValue,
                type: 'GET',
                // dataType: 'json',
                success: function(data) {
                   document.getElementById("bestPrice").value = "$ " + data.cryptoPrice
                }
            });
    });

    $('.getBitfinix').change(
    function(){
      var currencyValue = document.getElementById("selectCurrency").value;

      $.ajax({
                url: 'get-selected-currency-data/'+currencyValue,
                type: 'GET',
                success: function(data) {
                   document.getElementById("bestPrice").value = "$ " + data.bitfinixPrice
                }
            });
    });

    $('.getKraken').change(
    function(){
      var currencyValue = document.getElementById("selectCurrency").value;

      $.ajax({
                url: 'get-selected-currency-data/'+currencyValue,
                type: 'GET',
                success: function(data) {
                   document.getElementById("bestPrice").value = "$ " + data.krakenPrice
                }
            });
          });
  </script>


@endsection