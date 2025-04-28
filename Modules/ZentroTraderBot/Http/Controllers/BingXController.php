<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;

class BingXController extends Controller
{
    // The current account places an order on the specified symbol contract
    /*
    paramsMap = {
    "symbol": "NEMS-USDT",
    "side": "SELL",
    "type": "LIMIT",
    "quantity": "115",
    "price": "0.1557",
    "timestamp": "1702720966321"
    }
     */
    public function spotTradeCreateOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/order", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "symbol": "NEMS-USDT",
    "orderId": 1735963671248581000,
    "transactTime": 1702720966528,
    "price": "0.1557",
    "origQty": "115",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "PENDING",
    "type": "LIMIT",
    "side": "SELL"
    }
    }
     */
    }

    /*
    payload = {
    "orderId": "1735964079647111280",
    "symbol": "NEMS-USDT",
    "timestamp": "1702721073626"
    }
     */
    public function spotTradeCancelOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/cancel", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "symbol": "NEMS-USDT",
    "orderId": 1735964079647111200,
    "price": "0.1532",
    "stopPrice": "0.1532",
    "origQty": "126",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL"
    }
    }
     */
    }

    /*
    payload = {
    "orderIds": "1735964997957275648,1735965127519326208",
    "symbol": "GM-USDT",
    "timestamp": "1702721320676"
    }
     */
    public function spotTradeCancelBatchOfOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/cancelOrders", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "orders": [
    {
    "symbol": "GM-USDT",
    "orderId": 1735964997957275600,
    "transactTime": 1702721242701,
    "price": "0.00000398",
    "stopPrice": "0.00000398",
    "origQty": "8061558",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL",
    "clientOrderID": "2most51702721242645506402"
    },
    {
    "symbol": "GM-USDT",
    "orderId": 1735965127519326200,
    "transactTime": 1702721249787,
    "price": "0.00000398",
    "stopPrice": "0.00000398",
    "origQty": "5806281",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL",
    "clientOrderID": "2most51702721249647382871"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "GM-USDT",
    "timestamp": "1702721320676"
    }
     */
    public function spotTradeCancelOrderBySymbol($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/cancelOpenOrders", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "orders": [
    {
    "symbol": "GM-USDT",
    "orderId": 1735964997957275600,
    "transactTime": 1702721242701,
    "price": "0.00000398",
    "stopPrice": "0.00000398",
    "origQty": "8061558",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL",
    "clientOrderID": "2most51702721242645506402"
    },
    {
    "symbol": "GM-USDT",
    "orderId": 1735965127519326200,
    "transactTime": 1702721249787,
    "price": "0.00000398",
    "stopPrice": "0.00000398",
    "origQty": "5806281",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL",
    "clientOrderID": "2most51702721249647382871"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "cancelOrderId": "17543893539094511234",
    "cancelReplaceMode": "ALLOW_FAILURE",
    "side": "BUY",
    "type": "LIMIT",
    "price": 40000,
    "quantity": 1
    }
     */
    public function spotTradeCancelAndPlaceNewOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/order/cancelReplace", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "cancelResult": {
    "code": 100400,
    "msg": " order not exist",
    "result": false
    },
    "openResult": {
    "code": 0,
    "msg": "",
    "result": true
    },
    "orderOpenResponse": {
    "symbol": "BTC-USDT",
    "orderId": 1754389353909452800,
    "transactTime": 1707113991607,
    "price": "40000",
    "stopPrice": "0",
    "origQty": "1",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "PENDING",
    "type": "LIMIT",
    "side": "BUY",
    "clientOrderID": ""
    },
    "orderCancelResponse": {
    "symbol": "",
    "orderId": 0,
    "price": "0",
    "stopPrice": "0",
    "origQty": "0",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "",
    "type": "",
    "side": ""
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BRG-USDT",
    "orderId": "1735965009395131234",
    "timestamp": "1702721583560"
    }
     */
    public function spotTradeQueryOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/query", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "symbol": "BRG-USDT",
    "orderId": 1735965009395131100,
    "price": "0.0005027",
    "StopPrice": "0",
    "origQty": "4038",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "PENDING",
    "type": "LIMIT",
    "side": "BUY",
    "time": 1702721285567,
    "updateTime": 1702721285567,
    "origQuoteOrderQty": "0",
    "fee": "0",
    "feeAsset": "BRG"
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BNB-USDC",
    "timestamp": "1702721719312"
    }
     */
    public function spotTradeQueryOpenOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/openOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "orders": [
    {
    "symbol": "BNB-USDC",
    "orderId": 1735930294290081300,
    "price": "255.27",
    "StopPrice": "0",
    "origQty": "0.16261",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "PENDING",
    "type": "LIMIT",
    "side": "SELL",
    "time": 1702713008841,
    "updateTime": 1702713008841,
    "origQuoteOrderQty": "0",
    "fee": 0
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "endTime": "1702721825418",
    "pageIndex": "1",
    "pageSize": "100",
    "startTime": "1702720925417",
    "symbol": "SWCH-USDT",
    "timestamp": "1702721825418"
    }
     */
    public function spotTradeQueryOrderHistory($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/historyOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "orders": [
    {
    "symbol": "SWCH-USDT",
    "orderId": 1735966927102231300,
    "price": "0.8548",
    "StopPrice": "0",
    "origQty": "5.9",
    "executedQty": "1.1",
    "cummulativeQuoteQty": "0.9402800000000001",
    "status": "CANCELED",
    "type": "LIMIT",
    "side": "SELL",
    "time": 1702721743000,
    "updateTime": 1702721743000,
    "origQuoteOrderQty": "0",
    "fee": 0
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "orderId": 1745362930595004400,
    "limit": 10
    }
     */
    public function spotTradeQueryTransactionDetails($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/myTrades", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "fills": [
    {
    "symbol": "BTC-USDT",
    "id": 36767057,
    "orderId": 1745362930595004400,
    "price": "46820.155",
    "qty": "0.1430254",
    "quoteQty": "6696.471396937",
    "commission": -0.000046483255,
    "commissionAsset": "BTC",
    "time": 1704961925000,
    "isBuyer": true,
    "isMaker": false
    },
    {
    "symbol": "BTC-USDT",
    "id": 36767058,
    "orderId": 1745362930595004400,
    "price": "46820.155",
    "qty": "0.0003844",
    "quoteQty": "17.997667582000002",
    "commission": -1.2493e-7,
    "commissionAsset": "BTC",
    "time": 1704961925000,
    "isBuyer": true,
    "isMaker": false
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "data": "[{\"symbol\": \"ETHS-USDT\", \"side\": \"BUY\", \"type\": \"LIMIT\", \"quantity\": 7.663, \"price\": 12.479, \"newClientOrderId\": \"abc122345\"}]",
    "recvWindow": "60000",
    "timestamp": "1702721964975"
    }
     */
    public function spotTradeBatchPlacingOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/trade/batchOrders", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "orders": [
    {
    "symbol": "ETHS-USDT",
    "orderId": 1735967859282101200,
    "transactTime": 1702721965033,
    "price": "12.479",
    "origQty": "7.663",
    "executedQty": "0",
    "cummulativeQuoteQty": "0",
    "status": "PENDING",
    "type": "LIMIT",
    "side": "BUY",
    "clientOrderID": "abc122345"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "timestamp": "1702720966321"
    }
     */
    public function spotTradeQueryTradingCommissionRate($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/user/commissionRate", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "takerCommissionRate": 0.000325,
    "makerCommissionRate": 0.0001
    }
    }
     */
    }

    /*
    paramsMap = {
    "type": "ACTIVATE",
    "timeOut": 10
    }
     */
    public function spotTradeCancelAllOrdersInCountdown($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/user/cancelAllAfter", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "triggerTime": 1710389137,
    "status": "ACTIVATED",
    "note": "All your spot pending orders will be closed automatically at 2024-03-14 04:05:37 UTC(+0),before that you can cancel the timer, or extend triggerTime time by this request"
    }
    }
     */
    }

    // account -------------------------------

    /*
    paramsMap = {
    "recvWindow": "60000",
    "timestamp": "1702624167523"
    }
     */
    public function spotAccountQueryAssets($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/account/balance", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "balances": [
    {
    "asset": "USDT",
    "free": "566773.193402631",
    "locked": "244.18616265388994"
    },
    {
    "asset": "CHEEMS",
    "free": "294854132046232",
    "locked": "18350553840"
    },
    {
    "asset": "VST",
    "free": "0",
    "locked": "0"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "amount": "10.0",
    "coin": "USDT",
    "userAccount": "16779999",
    "userAccountType": "1",
    "walletType": "1"
    }
     */
    public function spotAccountInternalTransfer($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/capital/innerTransfer/apply", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "timestamp": 1702558152381,
    "data": {
    "id": "12******1"
    }
    }
     */
    }

    // Futures -------------------------------

    // The actual order will not be placed, only the test results will be returned
    // The result is a fake order, and your funds will not be deducted
    // The minimum order quantity can be obtained from the interface /openApi/swap/v2/quote/contracts: tradeMinQuantity, tradeMinUSDT
    /*
    Depending on the order type, certain parameters are mandatory:
    LIMIT: Mandatory Parameters: quantity, price
    MARKET: Mandatory Parameters: quantity
    TRAILING_STOP_MARKET (Tracking Stop Loss Order) or TRAILING_TP_SL (Trailing TakeProfit/StopLoss Order): The price field or priceRate field needs to be filled in
    TRIGGER_LIMIT, STOP, TAKE_PROFIT: Mandatory Parameters: quantity、stopPrice、price
    STOP_MARKET, TAKE_PROFIT_MARKET, TRIGGER_MARKET: Mandatory Parameters: quantity、stopPrice

    paramsMap = {
    "symbol": "BTC-USDT",
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "quantity": 5,
    "takeProfit": "{\"type\": \"TAKE_PROFIT_MARKET\", \"stopPrice\": 31968.0,\"price\": 31968.0,\"workingType\":\"MARK_PRICE\"}"
    }

     */
    public function perpetualTradeOrderTest($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/order/test", "POST", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "BTC-USDT",
    "orderId": 1735950529123455000,
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "clientOrderID": "",
    "workingType": "MARK_PRICE"
    }
    }
    }
     */
    }

    // The minimum order quantity can be obtained from the interface /openApi/swap/v2/quote/contracts: tradeMinQuantity, tradeMinUSDT
    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "quantity": 5,
    "takeProfit": "{\"type\": \"TAKE_PROFIT_MARKET\", \"stopPrice\": 31968.0,\"price\": 31968.0,\"workingType\":\"MARK_PRICE\"}"
    }
     */
    public function perpetualTradeCreateOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/order", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "BTC-USDT",
    "orderId": 1735950529123455000,
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "clientOrderID": "",
    "workingType": "MARK_PRICE"
    }
    }
    }
     */
    }

    // The minimum order quantity can be obtained from the interface /openApi/swap/v2/quote/contracts: tradeMinQuantity, tradeMinUSDT
    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "quantity": 5,
    "takeProfit": "{\"type\": \"TAKE_PROFIT_MARKET\", \"stopPrice\": 31968.0,\"price\": 31968.0,\"workingType\":\"MARK_PRICE\"}"
    }
     */
    public function perpetualTradeCreateOrderInDemo($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api-vst.bingx.com", "/openApi/swap/v2/trade/order", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "BTC-USDT",
    "orderId": 1735950529123455000,
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "clientOrderID": "",
    "workingType": "MARK_PRICE"
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "timestamp": "1702731721672",
    "symbol": "BTC-USDT"
    }
     */
    public function perpetualTradeOneClickCloseAllPositions($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/closeAllPositions", "POST", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "success": [
    1736008778921491200
    ],
    "failed": null
    }
    }
     */
    }

    /*
    paramsMap = {
    "orderId": "1736011869418901234",
    "symbol": "RNDR-USDT",
    "timestamp": "1702732515704"
    }
     */
    public function perpetualTradeCancelOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/order", "DELETE", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "RNDR-USDT",
    "orderId": 1736011869418901200,
    "side": "BUY",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "3",
    "price": "4.5081",
    "executedQty": "0",
    "avgPrice": "0.0000",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0000",
    "commission": "0.000000",
    "status": "CANCELLED",
    "time": 1702732457867,
    "updateTime": 1702732457888,
    "clientOrderId": "lo******7",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": ""
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "recvWindow": "0",
    "symbol": "ATOM-USDT",
    "timestamp": "1702732849363"
    }
     */
    public function perpetualTradeCancelAllOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/allOpenOrders", "DELETE", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "success": [
    {
    "symbol": "ATOM-USDT",
    "orderId": 1736013373487123500,
    "side": "SELL",
    "positionSide": "SHORT",
    "type": "LIMIT",
    "origQty": "3.00",
    "price": "13.044",
    "executedQty": "0.00",
    "avgPrice": "0.000",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0",
    "commission": "0",
    "status": "CANCELLED",
    "time": 1702732816465,
    "updateTime": 1702732816488,
    "clientOrderId": "",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": ""
    },
    {
    "symbol": "ATOM-USDT",
    "orderId": 1736013373487123500,
    "side": "BUY",
    "positionSide": "SHORT",
    "type": "LIMIT",
    "origQty": "3.00",
    "price": "11.292",
    "executedQty": "0.00",
    "avgPrice": "0.000",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0",
    "commission": "0",
    "status": "CANCELLED",
    "time": 1702732816820,
    "updateTime": 1702732816839,
    "clientOrderId": "",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": ""
    }
    ],
    "failed": null
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BTC-USDT",
    "timestamp": "1702733126509"
    }
     */
    public function perpetualTradeQueryAllCurrentPendingOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/openOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "orders": [
    {
    "symbol": "BTC-USDT",
    "orderId": 1733405587011123500,
    "side": "SELL",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "0.0030",
    "price": "44459.6",
    "executedQty": "0.0000",
    "avgPrice": "0.0",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0",
    "commission": "0.0",
    "status": "PENDING",
    "time": 1702256915574,
    "updateTime": 1702256915610,
    "clientOrderId": "",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE"
    },
    {
    "symbol": "BTC-USDT",
    "orderId": 1733405587011123500,
    "side": "SELL",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "0.0030",
    "price": "44454.6",
    "executedQty": "0.0000",
    "avgPrice": "0.0",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0",
    "commission": "0.0",
    "status": "PENDING",
    "time": 1702111071719,
    "updateTime": 1702111071735,
    "clientOrderId": "",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "orderId": "1736012449498123456",
    "symbol": "OP-USDT",
    "timestamp": "1702733255486"
    }
     */
    public function perpetualTradeQueryPendingOrderStatus($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/openOrder", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "OP-USDT",
    "orderId": 1736012449498123500,
    "side": "SELL",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "1.0",
    "price": "2.1710",
    "executedQty": "0.0",
    "avgPrice": "0.0000",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0000",
    "commission": "0.000000",
    "status": "PENDING",
    "time": 1702732596168,
    "updateTime": 1702732596188,
    "clientOrderId": "l*****e",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE"
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "orderId": "1736012449498123456",
    "symbol": "OP-USDT",
    "timestamp": "1702733255486"
    }
     */
    public function perpetualTradeQueryOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/order", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "order": {
    "symbol": "OP-USDT",
    "orderId": 1736012449498123500,
    "side": "SELL",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "1.0",
    "price": "2.1710",
    "executedQty": "0.0",
    "avgPrice": "0.0000",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0000",
    "commission": "0.000000",
    "status": "PENDING",
    "time": 1702732596168,
    "updateTime": 1702732596188,
    "clientOrderId": "l*****e",
    "leverage": "",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE",
    "stopGuaranteed": false,
    "triggerOrderId": 1736012449498123500
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "WOO-USDT",
    "timestamp": "1702733469134"
    }
     */
    public function perpetualTradeQueryMarginMode($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/marginType", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "marginType": "CROSSED"
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "MINA-USDT",
    "marginType": "CROSSED",
    "recvWindow": "60000",
    "timestamp": "1702733445917"
    }
     */
    public function perpetualTradeSwitchMarginMode($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/marginType", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": ""
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "BCH-USDT",
    "timestamp": "1702733572940"
    }
     */
    public function perpetualTradeQueryLeverage($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/leverage", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "longLeverage": 50,
    "shortLeverage": 50,
    "maxLongLeverage": 75,
    "maxShortLeverage": 75
    }
    }
     */
    }

    /*
    paramsMap = {
    "leverage": "8",
    "side": "SHORT",
    "symbol": "ETH-USDT",
    "timestamp": "1702733704941"
    }
     */
    public function perpetualTradeSwitchLeverage($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/leverage", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "leverage": 8,
    "symbol": "ETH-USDT"
    }
    }
     */
    }

    /*
    paramsMap = {
    "symbol": "ATOM-USDT",
    "startTime": "1696291200",
    "timestamp": "1699982303257"
    }
     */
    public function perpetualTradeUsersForceOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/forceOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "orders": [
    {
    "symbol": "ATOM-USDT",
    "orderId": 172264854643022330000,
    "side": "SELL",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "2.36",
    "price": "8.096",
    "executedQty": "2.36",
    "avgPrice": "8.095",
    "cumQuote": "19",
    "stopPrice": "",
    "profit": "-0.9346",
    "commission": "-0.009553",
    "status": "FILLED",
    "time": 1699546393000,
    "updateTime": 1699546393000,
    "clientOrderId": "",
    "leverage": "21X",
    "takeProfit": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "endTime": "1702731995000",
    "limit": "500",
    "startTime": "1702688795000",
    "symbol": "PYTH-USDT",
    "timestamp": "1702731995838"
    }
     */
    public function perpetualTradeUsersHistoryOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/allOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "orders": [
    {
    "symbol": "PYTH-USDT",
    "orderId": 1736007506620112100,
    "side": "SELL",
    "positionSide": "SHORT",
    "type": "LIMIT",
    "origQty": "33",
    "price": "0.3916",
    "executedQty": "33",
    "avgPrice": "0.3916",
    "cumQuote": "13",
    "stopPrice": "",
    "profit": "0.0000",
    "commission": "-0.002585",
    "status": "FILLED",
    "time": 1702731418000,
    "updateTime": 1702731470000,
    "clientOrderId": "",
    "leverage": "15X",
    "takeProfit": {
    "type": "TAKE_PROFIT",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "stopLoss": {
    "type": "STOP",
    "quantity": 0,
    "stopPrice": 0,
    "price": 0,
    "workingType": ""
    },
    "advanceAttr": 0,
    "positionID": 0,
    "takeProfitEntrustPrice": 0,
    "stopLossEntrustPrice": 0,
    "orderType": "",
    "workingType": "MARK_PRICE",
    "stopGuaranteed": false,
    "triggerOrderId": 1736012449498123500
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "recvWindow": "10000",
    "symbol": "BTC-USDT",
    "type": "1",
    "amount": "3",
    "positionSide": "LONG",
    "timestamp": "1702718148654"
    }
     */
    public function perpetualTradeAdjustIsolatedMargin($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/positionMargin", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "amount": 3,
    "type": 1
    }
     */
    }

    /*
    paramsMap = {
    "endTs": "1702731530000",
    "startTs": "1702724330000",
    "symbol": "WLD-USDT",
    "tradingUnit": "COIN",
    "timestamp": "1702731530753"
    }
     */
    public function perpetualTradeQueryHistoricalTransactionOrders($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/allFillOrders", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "fill_orders": [
    {
    "filledTm": "2023-12-16T20:58:36Z",
    "volume": "4.10",
    "price": "3.1088",
    "amount": "12.7492",
    "commission": "-0.0025",
    "currency": "USDT",
    "orderId": "1736007768311123456",
    "liquidatedPrice": "",
    "liquidatedMarginRatio": "",
    "filledTime": "2023-12-16T20:58:36.000+0800",
    "clientOrderID": "",
    "symbol": "WLD-USDT"
    }
    ]
    }
    }
     */
    }

    /*
    paramsMap = {
    "dualSidePosition": "true",
    "timestamp": "1702731530753"
    }
     */
    public function perpetualTradeSetPositionMode($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v1/positionSide/dual", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "dualSidePosition": "true"
    }
    }
     */
    }

    /*
    paramsMap = {
    "timestamp": "1702731530753"
    }
     */
    public function perpetualTradeGetPositionMode($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v1/positionSide/dual", "GET", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "dualSidePosition": "true"
    }
    }
     */
    }

    /*
    paramsMap = {
    "cancelReplaceMode": "STOP_ON_FAILURE",
    "cancelClientOrderId": "abc123test",
    "cancelOrderId": 123456789,
    "cancelRestrictions": "ONLY_NEWS",
    "symbol": "BTC-USDT",
    "side": "BUY",
    "positionSide": "LONG",
    "type": "MARKET",
    "quantity": 5,
    "takeProfit": "{\"type\": \"TAKE_PROFIT_MARKET\", \"stopPrice\": 31968.0,\"price\": 31968.0,\"workingType\":\"MARK_PRICE\"}",
    "timestamp": "1702731530753"
    }
     */
    public function perpetualTradeCancelOrderAndPlaceNewOrder($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v1/trade/cancelReplace", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "cancelResult": "true",
    "cancelMsg": "",
    "cancelResponse": {
    "cancelClientOrderId": "",
    "cancelOrderId": 123456789,
    "symbol": "BTC-USDT",
    "orderId": 123456789,
    "side": "BUY",
    "positionSide": "LONG",
    "type": "LIMIT",
    "origQty": "1.0000",
    "price": "38000.0",
    "executedQty": "0.0000",
    "avgPrice": "0.0",
    "cumQuote": "0",
    "stopPrice": "",
    "profit": "0.0000",
    "commission": "0.000000",
    "status": "PENDING",
    "time": 1706858471000,
    "updateTime": 1706858471000,
    "clientOrderId": "",
    "leverage": "15X",
    "workingType": "MARK_PRICE",
    "onlyOnePosition": false,
    "reduceOnly": false
    },
    "replaceResult": "true",
    "replaceMsg": "",
    "newOrderResponse": {
    "orderId": 987654321,
    "symbol": "BTC-USDT",
    "positionSide": "LONG",
    "side": "BUY",
    "type": "LIMIT",
    "price": 38000,
    "quantity": 1,
    "stopPrice": 0,
    "workingType": "MARK_PRICE",
    "clientOrderID": "",
    "timeInForce": "GTC",
    "priceRate": 0,
    "stopLoss": "{\"type\": \"STOP\", \"stopPrice\": 37000, \"price\": 37000}",
    "takeProfit": "{\"type\": \"TAKE_PROFIT\", \"stopPrice\": 45000, \"price\": 45000}",
    "reduceOnly": false
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "type": "ACTIVATE",
    "timeOut": 10
    }
     */
    public function perpetualTradeCancelAllOrdersInCountdown($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/trade/cancelAllAfter", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "debugMsg": "",
    "data": {
    "triggerTime": 1710389137,
    "status": "ACTIVATED",
    "note": "All your spot pending orders will be closed automatically at 2024-03-14 04:05:37 UTC(+0),before that you can cancel the timer, or extend triggerTime time by this request"
    }
    }
     */
    }

    /*
    paramsMap = {
    "timestamp": "1702731721672",
    "positionId": "1769649551460794368"
    }
     */
    public function perpetualTradeClosePositionByID($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v1/trade/closePosition", "POST", $api_key, $secret_key, $payload);

        /*
    {
    "code": 0,
    "msg": "",
    "timestamp": 0,
    "data": {
    "orderId": 1769649628749234200,
    "positionId": "1769649551460794368",
    "symbol": "BTC-USDT",
    "side": "Ask",
    "type": "Market",
    "positionSide": "BOTH",
    "origQty": "1.0000"
    }
    }
     */
    }

    // Perpetual -------------------------------

    /*
    paramsMap = {
    "timestamp": "1702731518913"
    }

     */
    public function perpetualAccountAssetInformation($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/user/balance", "GET", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "balance": {
    "userId": "116***295",
    "asset": "USDT",
    "balance": "194.8212",
    "equity": "196.7431",
    "unrealizedProfit": "1.9219",
    "realisedProfit": "-109.2504",
    "availableMargin": "193.7609",
    "usedMargin": "1.0602",
    "freezedMargin": "0.0000"
    }
    }
    }
     */
    }

    /*
    paramsMap = {
    "recvWindow": "0",
    "symbol": "BNB-USDT",
    "timestamp": "1702731661854"
    }

     */
    public function perpetualAccountPositions($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/user/positions", "GET", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": [
    {
    "positionId": "1735*****52",
    "symbol": "BNB-USDT",
    "currency": "USDT",
    "positionAmt": "0.20",
    "availableAmt": "0.20",
    "positionSide": "SHORT",
    "isolated": true,
    "avgPrice": "246.43",
    "initialMargin": "9.7914",
    "leverage": 5,
    "unrealizedProfit": "-0.0653",
    "realisedProfit": "-0.0251",
    "liquidationPrice": 294.16914617776246
    }
    ]
    }
     */
    }

    /*
    paramsMap = {
    "startTime": "1702713615001",
    "endTime": "1702731787011",
    "limit": "1000",
    "timestamp": "1702731787011"
    }

     */
    public function perpetualAccountProfitAndLossFundFlow($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/user/income", "GET", $api_key, $secret_key, $payload);
        /*
    {
    {
    "code": 0,
    "msg": "",
    "data": [
    {
    "symbol": "LDO-USDT",
    "incomeType": "FUNDING_FEE",
    "income": "-0.0292",
    "asset": "USDT",
    "info": "Funding Fee",
    "time": 1702713615000,
    "tranId": "170***6*2_3*9_20***97",
    "tradeId": "170***6*2_3*9_20***97"
    }
    ]
    }
     */
    }

    /*
    paramsMap = {
    "timestamp": "1702732072912",
    "recvWindow": "5000"
    }

     */
    public function perpetualAccountlUserFeeRate($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/user/commissionRate", "GET", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "commission": {
    "takerCommissionRate": 0.0005,
    "makerCommissionRate": 0.0002
    }
    }
    }
     */
    }

    public function perpetualAccountQuoteContracts($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/swap/v2/quote/contracts", "GET", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "commission": {
    "takerCommissionRate": 0.0005,
    "makerCommissionRate": 0.0002
    }
    }
    }
     */
    }
    public function perpetualAccountQuoteContractsFormated($api_key, $secret_key, $payload, $symbol = false)
    {
        $response = json_decode($this->perpetualAccountQuoteContracts(
            $api_key,
            $secret_key,
            $payload
        ), true);

        $array = [];
        if (isset($response["data"])) {
            foreach ($response["data"] as $value) {
                $array[$value["asset"]] = $value;
            }
        }

        if ($symbol) {
            if (isset($array[strtoupper($symbol)])) {
                return $array[strtoupper($symbol)];
            } else {
                return array();
            }
        }

        return $array;
    }

    // Standard -------------------------------

    /*
    paramsMap = {
    "timestamp": "1702731518913"
    }

     */
    public function test($api_key, $secret_key, $payload)
    {

        return $this->doRequest("https", "open-api.bingx.com", "/openApi/spot/v1/account/balance", "GET", $api_key, $secret_key, $payload);
        /*
    {
    "code": 0,
    "msg": "",
    "data": {
    "balance": {
    "userId": "116***295",
    "asset": "USDT",
    "balance": "194.8212",
    "equity": "196.7431",
    "unrealizedProfit": "1.9219",
    "realisedProfit": "-109.2504",
    "availableMargin": "193.7609",
    "usedMargin": "1.0602",
    "freezedMargin": "0.0000"
    }
    }
    }
     */
    }

    // Common -------------------------------

    public function doRequest($protocol, $host, $api, $method, $API_KEY, $API_SECRET, $payload)
    {
        $timestamp = round(microtime(true) * 1000);
        $parameters = "timestamp=" . $timestamp;

        if ($payload != null) {
            foreach ($payload as $key => $value) {
                $parameters .= "&$key=$value";
            }
        }

        $sign = $this->calculateHmacSha256($parameters, $API_SECRET);

        try {
            $response = file_get_contents("{$protocol}://{$host}{$api}?{$parameters}&signature={$sign}", false, stream_context_create([
                "http" => [
                    "header" => "X-BX-APIKEY: {$API_KEY}",
                    "method" => $method,
                ],
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]));
        } catch (\Throwable $th) {
            return json_encode(
                array(
                    "code" => 200,
                    "message" => $th->getMessage(),
                )
            );
        }

        return $response;
    }

    public function calculateHmacSha256($input, $key)
    {
        $hash = hash_hmac("sha256", $input, $key, true);
        $hashHex = bin2hex($hash);
        return strtolower($hashHex);
    }

}
