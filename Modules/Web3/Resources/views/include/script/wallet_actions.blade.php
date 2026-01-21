<script>

    function initializeWeb3Modal(onWalletConnected, onWalletDisconnected, callback = false) {
        console.log("initializeWeb3Modal:");
        if (window.initModule) {
            console.log("initModule:");
            $(".conected").hide();
            $(".disconected").show();
            window.initModule((network) => {
                console.log("network changed:", network);

                if (network.chain) {
                    // chequear q este conectado a la red correcta
                    if (network.chain.id.toString() !== 97) {
                        showToastrMessage(
                            "error",
                            "{{ trans('Web3::messages.connect.error.wrongnetwork.title') }}",
                            "{{ trans('Web3::messages.connect.error.wrongnetwork.text', ['network' => config('metadata.cripto.blockchain.id')]) }}",
                            40000
                        );
                    }

                    // ajustar la inerfaz con los datos del usuario
                    onWalletConnected(window.ethereumClient.getAccount().address, 8, callback);
                } else {
                    // obligar a desconectar el usuario porq no existe chain conectada
                    onWalletDisconnected();
                }
            });
        }
        else {
            $(".conected").hide();
            $(".disconected").hide();
        }
    }

    async function onWalletConnected(account, size = 8, callback = false) {
        $(".conected").show();
        $(".disconected").hide();

        $(".wallet-address").html('<i class="bi bi-qr-code"></i>&nbsp;&nbsp;' + summarizeString(account, size,
            true));

        $("#walletAddress").html(summarizeString(account, size, true));
        $("#walletAddress").attr("value", account);

        $("." + account).show();

        console.log('WalletConnected:', account);

        if (callback)
            callback(account);
    }

    function onWalletDisconnected(callback = false) {
        $(".disconected").show();
        $(".conected").hide();

        $(".owneditem").hide();

        $.ajax({
            type: "POST",
            url: "{{ route('logout') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: true,
            dataType: "text",
            success: function (data, status, jqXHR) {
                console.log("logout", data, status, jqXHR);
                if (callback)
                    callback();
            },
            error: function (data, status) { }
        });

    }

    function checkIsRegistered(account, registeredCallback = false, unregisteredCallback = false) {
        $.ajax({
            type: "POST",
            url: '{{ URL::to('wallets/isregistered') }}' + '/' + account,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).done(function (response) {
            console.log('isregistered response', response, 'user loggued IN!');

            if (response.toString() == "1") {
                $('.unregistered').hide();
                $('.registered').show();

                if (registeredCallback)
                    registeredCallback();

            } else {
                $('.registered').hide();
                $('.unregistered').show();


                if (unregisteredCallback)
                    unregisteredCallback();
            }
        });
    }


    function register(account, code, callback = false) {
        $.ajax({
            type: "POST",
            url: '{{ route('wallet.register') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                address: account,
                code: code,
            },
            async: true,
            dataType: "text",
            success: function (data, status, jqXHR) {
                console.log("register", data, status, jqXHR);

                if (callback)
                    callback();
            },
            error: function (data, status) { }
        });
    }
</script>