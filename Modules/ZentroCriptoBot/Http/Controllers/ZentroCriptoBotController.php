<?php

namespace Modules\ZentroCriptoBot\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Entities\TelegramBots;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;

class ZentroCriptoBotController extends JsonsController
{
    use UsesTelegramBot;

    public function __construct($botname, $instance = false)
    {
        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();

        if ($instance === false)
            $instance = $botname;
        $response = false;
        try {
            $bot = $this->getFirst(TelegramBots::class, "name", "=", "@{$instance}");
            $this->token = $bot->token;
            $this->data = $bot->data;

            $response = json_decode($this->TelegramController->getBotInfo($this->token), true);
        } catch (\Throwable $th) {
        }
        if (!$response)
            $response = array(
                "result" => array(
                    "username" => $instance
                )
            );

        $this->telegram = $response["result"];
    }

    public function processMessage()
    {
        /*
        "private": Indica que el mensaje proviene de un chat privado con un usuario.
        "group": Indica que el mensaje proviene de un grupo.
        "supergroup": Indica que el mensaje proviene de un supergrupo.
        "channel": Indica que el mensaje proviene de un canal.
        "bot": Indica que proviene de otro bot (en el campo from o sender).
         */
        switch (trim(strtolower($this->message["chat"]["type"]))) {
            case "private":
                break;
            case "group":
            case "supergroup":
                break;
            case "channel":
                break;
            default:
                break;
        }

        $reply = array(
            "text" => "🙇🏻 No se que responderle a “{$this->message['text']}”.\n Ud puede interactuar con este bot usando /menu o chequee /ayuda para temas de ayuda.",
        );
        $array = $this->getCommand($this->message["text"]);
        //echo strtolower($array["command"]);
        switch (strtolower($array["command"])) {
            case "/start":
            case "start":
            case "/menu":
            case "menu":
                //https://t.me/bot?start=816767995
                // /start 816767995
                $reply = $this->mainMenu($this->actor);
                break;
            case "/help":
            case "help":
            case "/ayuda":
            case "ayuda":
                $text = "📖 *¿Cómo usar este bot?*.\n_He aquí los principales elementos que debe conocer:_\n\n";
                $text .= "1️⃣ *Acceder al menú principal*: /menu\n_Escriba “menu” o simplemente cliquee en el comando_\n";
                $text .= "2️⃣ *Establecer zona horaria*: /utc\n_Escriba el comando para obtener el asistente correspondiente._\n\n";
                //$text .= "📚 *Manual de usuario*:\n_Puede encontrar el manual de usuario para REMESADORES aquí:_ [{request()->root()}/" . $this->telegram["username"] . ".pdf]\n\n";
                //$text .= "👮‍♂️ *Términos y condiciones*:\n_Para usar nuestro servicio ud debe ACEPTAR nuestros términos que puede examinar aquí:_ [{request()->root()}/TermsAndConditions.pdf]\n*Usar este bot se considera una ACEPTACIÓN IMPLÍCITA*";
                $reply = array(
                    "text" => $text,
                    "markup" => json_encode([
                        "inline_keyboard" => [
                            [
                                ["text" => "↖️ Ir al menú principal", "callback_data" => "menu"],
                            ],

                        ],
                    ]),
                );
                break;

            default:
                $text = $this->analizeToken($this->message["text"]);
                $reply = array(
                    "text" => $text,
                );
                break;

        }

        return $reply;
    }

    public function analizeToken($text)
    {
        $array = explode(" ", $text);

        // si hay mas de 2 palabras ya no es el formato /chain contract sino q es otra cosa y no se analiza
        if (count($array) > 2) {
            return "";
        }

        $contract = strtolower($array[0]);
        $command = "";

        if (count($array) > 1) {
            $command = strtolower($array[0]);
            $contract = $array[1];
        }

        $data = "";
        if ($this->detectBlockchain($contract)) {
            $response = array();
            try {
                $url = "https://api.gopluslabs.io/api/v1/";

                switch ($command) {
                    case "":
                        $command = "/bsc";
                    case '/eth':
                    case '/opt':
                    case '/cro':
                    case '/okc':
                    case '/heco':
                    case '/pol':
                    case '/fan':
                    case '/kcc':
                    case '/arb':
                    case '/ava':
                    case '/har':
                    case '/trx':
                    case '/bsc':
                        $url .= "token_security/" . ZentroCriptoBotController::$BLOCKCHAINS[str_replace("/", "", $command)]["gopluslabs"]["id"] . "?contract_addresses={$contract}";
                        break;
                    case '/sol':
                        $url .= "solana/token_security?contract_addresses={$contract}";
                        break;
                    default:
                        return "";
                }

                $response = json_decode(file_get_contents($url), true);
                //var_dump($response['result'][$contract]);
                /*
                {"code":4029,"message":"too many requests", "result":[]}
                1    Complete data prepared
                2    Partial data obtained. The complete data can be requested again in about 15 seconds.
                2004    Contract address format error!
                2018    ChainID not supported
                2020    Non-contract address
                2021    No info for this contract
                2022    Non-supported chainId
                2026    dApp not found
                2027    ABI not found
                2028    The ABI not support parsing
                4010    App_key not exist
                4011    Signature expiration (the same request parameters cannot be requested more than once)
                4012    Wrong Signature
                4023    Access token not found
                4029    Request limit reached (limit is 30 calls/minute)
                5000    System error
                5006    Param error!
                 */

                $gopluslabs = $response['result'][$contract];
                //var_dump($gopluslabs);

                $data = $this->getTokenInfo($gopluslabs, $command);

            } catch (\Throwable $th) {
                $error = "ZentroCriptoBotController analizeToken ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}\n";
                Log::error($error . " => " . json_encode($response));

                //Log::info("ZentroCriptoBotController processMessage " . $this->message["chat"]["type"]);
                $error .= "`{$contract}`\n\n🆔 *{$this->message['chat']['type']}* `{$this->message['chat']['id']}`:";
                if (isset($this->message['chat']['title'])) {
                    $error .= "\nℹ️ *{$this->message['chat']['title']}*";
                }
                if (isset($this->message['chat']['username'])) {
                    $error .= "\n🅰️ `{$this->message['chat']['username']}`";
                }
                if ($this->message['chat']['id'] != $this->message['from']['id']) {
                    $error .= "\n👤 {$this->message['from']['username']} `{$this->message['from']['id']}`";
                }
                $error .= "\n" . json_encode($response);

                //Log::info("TelegramBotController callback_query for {$botname} from {$controller->message['chat']['type']} {$controller->message['chat']['id']}: " . json_encode($request["callback_query"]));

                $request["message"] = array(
                    "text" => "🐞 {$error}",
                    "chat" => array(
                        "id" => config('metadata.system.app.telegram.bot.owner'),
                    ),
                );

                $this->TelegramController->sendMessage($request, $this->token);

                $error = "❌ *An error has occurred*: Contract: `{$contract}`\n";
                if (isset($response['message'])) {
                    $error .= "👀 " . $response['message'] . "\n";
                }

                $error .= "👨🏻‍💻 DEV was notified successfully!";

                return $error;
            }
        }

        return $data;
    }

    public function getTokenInfo($gopluslabs, $chain)
    {
        $data = "";

        switch ($chain) {
            case "/sol":
                // $gopluslabs['trusted_token']
                // $gopluslabs['creator']['malicious_address']
                // $gopluslabs['metadata_mutable']['malicious_address']

                //$data .= ($gopluslabs['is_honeypot'] == 0 ? "✅ *Does not seem like a honeypot* ✅\n" : "🚨 *THIS IS HONEYPOT* 🚨\n") . "\n";

                $data .= "🆔 *Token Name*: `{$gopluslabs['metadata']['name']}`";
                switch ($gopluslabs['default_account_state']) {
                    case 0:
                    case "0":
                        //  The token is newly created and not ready for use
                        $data .= " 🆕\n";
                        break;
                    case 2:
                    case "2":
                        // The token is "frozen" would be locked and prohibited from performing any token transactions or operations, usually for security or compliance reasons, until it is manually unfrozen
                        $data .= " 🥶\n";
                        break;

                    default:
                        $data .= "\n";
                        break;
                }
                if ($gopluslabs['non_transferable']) {
                    $data .= "🛑 *{$gopluslabs['metadata']['symbol']} is non-transferable!*\n";
                }

                $data .= "🚚 *Total Supply*: " . number_format($gopluslabs['total_supply']) . " `{$gopluslabs['metadata']['symbol']}`\n";
                if ($gopluslabs['metadata']['description'] && $gopluslabs['metadata']['description'] != "") {
                    $data .= "_{$gopluslabs['metadata']['description']}_\n\n";
                } else {
                    $data .= "\n";
                }

                $data .= "👤 *Creators*:\n";
                foreach ($gopluslabs['creators'] as $creator) {
                    $data .= "`{$gopluslabs['creators'][0]['address']}`";
                    if ($creator['malicious_address']) {
                        $data .= "🚨 *Malicious address!*\n";
                    } else {
                        $data .= "\n";
                    }
                }

                /*
                $gopluslabs['transfer_fee']
                Configuration information for transfer fees.
                (1)current_fee_rate: Currently effective transfer fee rate.
                fee_rate: Fee rate (expressed as a part per ten thousand, e.g., 200 means 2%).
                maximum_fee: Maximum fee amount for a single transaction. Unit is lamports.
                (2)scheduled_fee_rate: Scheduled changes to transfer fee rates.
                fee_rate: Fee rate (expressed as a part per ten thousand, e.g., 200 means 2%).
                epoch: The epoch at which the fee rate will take effect.
                maximum_fee: Maximum fee amount for a single transaction. Unit is lamports.
                 */

                /*
                Hook may block user from trading
                $gopluslabs['transfer_hook']['address']
                $gopluslabs['transfer_hook']['malicious_address']
                If there is any external hook in the token programme.
                (1)address: Address of the hook.
                (2)malicious_address: Indicates whether the address is malicious, "1" means yes.
                 */

                /*
                $gopluslabs['holders']
                List of top 10 addresses holding the token and their balances.
                (1)token_account: Address of the holder.
                (2)tag: Tag information of the holder.
                (3)balance: Amount of tokens held.
                (4)percent: Percentage of total supply held.
                (5)is_locked: If the holder is lokcer. If value is 1 it means token of the holder has been locked.
                (6)locked_detail is an array, that describes the lock position info of this holder, and only shows when "locked": 1. This Array may contain multiple objects for multiple locking info. In every object, "amount" describes the number of tokens locked, "end_time" describes when the token will be unlocked, and "opt_time" describes when the token was locked.
                 */

                /*
                $gopluslabs['trusted_token']
                If the token is a famous and trustworthy one. "1" means yes.
                This field is intended to identify well-known and reputable tokens. Trusted tokens with special functions (such as the mintable function in USDC) are generally not considered risk items. Please note that a value other than “1” does not indicate that the token is untrustworthy. We recommend properly evaluating and handling tokens with values other than “1” to avoid unnecessary disputes.
                 */

                /*
                $gopluslabs['metadata_mutable']
                Whether the metadata is mutable.
                (1)status: Status indicator, where "1" means the funtcion is available.
                (2)metadata_upgrade_authority: Information on metadata upgrade authority.
                address: Address with upgrade authority.
                malicious_address: Indicates whether the address is malicious, "1" means yes.
                 */

                /*
                $gopluslabs['mintable']
                Whether the token is mintable.
                This field follows the same rules as the other similar fields.
                (1)status: Status indicator, where "1" means the funtcion is available.
                (2)authority: Information on metadata upgrade authority.
                address: Address with upgrade authority.
                malicious_address: Indicates whether the address is malicious, "1" means yes.
                "mintable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['mintable'] && $gopluslabs['mintable']['status']) {
                    $data .= "🚨 *{$gopluslabs['metadata']['symbol']} is mintable!*\n";
                }

                /*
                $gopluslabs['freezable']
                Whether the developer can block any other users from trading.
                This field follows the same rules as the other similar fields.
                "freezable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['freezable'] && $gopluslabs['freezable']['status']) {
                    $data .= "🚨 *{$gopluslabs['metadata']['symbol']} is freezable!*\n";
                }

                /*
                $gopluslabs['closable']
                Whether the developer can close the token programme at any time. If the programme is closed, all the assets would be eliminated.
                This field follows the same rules as the other similar fields.
                "closable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['closable'] && $gopluslabs['closable']['status']) {
                    $data .= "🚨 *{$gopluslabs['metadata']['symbol']} is closable!*\n";
                }

                /*
                $gopluslabs['transfer_fee_upgradable']
                Whether the transfer fee of the token can be upgraded
                This field follows the same rules as the other similar fields.
                "transfer_fee_upgradable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['transfer_fee_upgradable'] && $gopluslabs['transfer_fee_upgradable']['status']) {
                    $data .= "🚨 *The transfer fee of {$gopluslabs['metadata']['symbol']} can be upgraded!*\n";
                }

                /*
                $gopluslabs['default_account_state_upgradable']
                whether the default account state can be upgradable.
                This field follows the same rules as the other similar fields.
                "default_account_state_upgradable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['default_account_state_upgradable'] && $gopluslabs['default_account_state_upgradable']['status']) {
                    $data .= "🚨 *The default account state of {$gopluslabs['metadata']['symbol']} can be upgraded!*\n";
                }

                /*
                $gopluslabs['balance_mutable_authority']
                Whether the developer can temper with users token balance.
                This field follows the same rules as the other similar fields.
                "balance_mutable_authority": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['balance_mutable_authority'] && $gopluslabs['balance_mutable_authority']['status']) {
                    $data .= "🚨 *The balance mutable authority of {$gopluslabs['metadata']['symbol']} can be upgraded!*\n";
                }

                /*
                $gopluslabs['transfer_hook_upgradable']
                Whether the transfer hook is upgradable.
                This field follows the same rules as the other similar fields.
                "transfer_hook_upgradable": {
                "authority": [],
                "status": "0"
                },
                 */
                if ($gopluslabs['transfer_hook_upgradable'] && $gopluslabs['transfer_hook_upgradable']['status']) {
                    $data .= "🚨 *The transfer hook of {$gopluslabs['metadata']['symbol']} can be upgraded!*\n";
                }

                /*
                Dex Info
                $gopluslabs['dex']
                Dex Name    dexname    Name of the DEX.
                Dex Type    type    Type of the DEX, could be "standard" or "concentrated".
                Liquidity ID    id    Address of the liquidity pool.
                TVL    tvl    Total value locked (TVL) in the liquidity pool.
                LP Total Amount    lp_amount    Total amount of liquidity provider tokens, only shown when type is "standard"
                Fee Rate    fee_rate    Transaction fee rate.
                Day Volume    day    Trading data for last day.
                (1)volume: The volume of transactions during this period.
                (2)price_min: Minimum price during this period.
                (3)price_max: Maximum price during this period.
                Week Volume    week    Trading data for last week.
                This field follows the same rules as the other similar fields.
                Month Volume    month    Trading data for last month.
                This field follows the same rules as the other similar fields.
                Price    price    Current price (Unitless, count by two tokens in the pool).
                Open Time    open_time    The epoch when trading is opened.
                LP Holders    lp_holders    List of top10 liquidity holders and their balances of the largest main token(SOL, USDC, USDT) liquidity pool.
                (1)token_account: Address of the holder.
                (2)tag: Tag information of the holder.
                (3)balance: Amount of tokens held.
                (4)percent: Percentage of total supply held.
                (5)is_locked: If the holder is lokcer. If value is 1 it means liquidity of the holder has been locked.
                (6)locked_detail is an array, that describes the lock position info of this holder, and only shows when "locked": 1. This Array may contain multiple objects for multiple locking info. In every object, "amount" describes the number of tokens locked, "end_time" describes when the token will be unlocked, and "opt_time" describes when the liquidity was locked.

                "dex": [
                {
                "day": {
                "price_max": "10.61337020791815",
                "price_min": "3.240349192935436",
                "volume": "947173817.5092463"
                },
                "dex_name": "raydium",
                "fee_rate": "0.0025",
                "id": "HKuJrP5tYQLbEUdjKwjgnHs2957QKjR2iWhJKTtMa1xs",
                "lp_amount": "1773.699678364",
                "month": {
                "price_max": "640.8935805988501",
                "price_min": "3.240349192935436",
                "volume": "2073688013.5585845"
                },
                "open_time": "1737166781",
                "price": "5.334677410178249",
                "tvl": "10654640.90777448752",
                "type": "Standard",
                "week": {
                "price_max": "640.8935805988501",
                "price_min": "3.240349192935436",
                "volume": "2073688013.558584"
                }
                },
                ...
                 */

                $data .= "\n🏦 *DEX Info:*\n";
                $analized = array();
                foreach ($gopluslabs['dex'] as $dex) {
                    $dex['dex_name'] = strtoupper($dex['dex_name']);
                    $dexinfo = $dex['dex_name'] . " " . $dex['type'];
                    if (!isset($analized[$dexinfo])) {
                        $data .= "_{$dexinfo}:_ 💵 {$dex['price']} / 💧 {$dex['fee_rate']}\n";
                    }

                    $analized[$dexinfo] = true;
                }

                break;

            default:
                /*
                Contract Security
                Security Items    Parameter    Description    Notice
                Open Source    is_open_source    Returns "1" if the contract is open-source, "0" if the contract is closed-source.    Closed-sourced contracts may hide various unknown mechanisms and are extremely risky. When the contract is closed-source, other risk items will return null.
                Proxy Contract    is_proxy    Returns "1" if the contract is a proxy contract, "0" if the contract is not a proxy contract.
                This value will not be returned if the proxy status of the contract is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) Most proxy contracts are accompanied by implementation contracts which are modifiable, potentially containing significant risk. When the contract is a proxy, other risk items may not be returned.
                Mint Function    is_mintable    Returns "1" if the contract has the ability to mint tokens, "0" if the contract does not have the ability to mint tokens.
                This value will not be returned if the minting ability of the contract is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) May not be returned if "is_proxy" is 1.
                (3) Mint functions can trigger a massive sell-off, causing the coin price to plummet. It is an extremely risky function for a contract to have.
                (4) This function generally relies on ownership. When the contract does not have an owner (or if the owner is a black hole address) and the owner cannot be retrieved, this function will most likely be disabled.
                Owner Address    owner_address    This contract's owner address. No value will be returned if the owner address is unknown. An empty sting will be returned if the contract has no owner.    (1) Will not be returned if "is_open_source" is 0.
                (2) May not be returned if "is_proxy" is 1.
                (3) Ownership is usually used to adjust the parameters and status of the contract, such as minting, modification of slippage, suspension of trading, setting blacklist, etc. When the contract's owner cannot be retrieved, is a black hole address, or does not have an owner, ownership-related functionality will most likely be disabled.
                Take Back Ownership    can_take_back_ownership    Returns "1" if ownership can be reclaimed; "0" if it cannot. Will not be returned if reclamation data is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) May not be returned if "is_proxy" is 1.
                (3) Ownership is usually used to adjust the parameters and status of the contract, such as minting, modification of slippage, suspension of trading, setting blacklist, etc. When the contract's owner cannot be retrieved, is a black hole address, or does not have an owner, ownership-related functionality will most likely be disabled. These risky functions may be able to be reactivated if ownership is reclaimed.
                Owner Can Change Balance    owner_change_balance    Returns "1" if the contract owner can change token holder balances; "0" if it cannot. Will not be returned if reclamation data is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) May not be returned if "is_proxy" is 1.
                (3) Tokens with this feature allow the owner to modify anyone's balance, resulting in a holder's asset to be changed (i.e. to 0) or a massive minting and sell-off.
                (4) This function generally relies on ownership. When the contract's owner cannot be retrieved, is a black hole address, or does not have an owner, ownership-related functionality will most likely be disabled.
                With Hidden Owner    hidden_owner    Returns "1" if the contract has hidden owners;
                "0" if it does not.
                Will not be returned if hidden ownership status is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) May not be returned if "is_proxy" is 1.
                (3) Hidden ownership is used by developers to maintain ownership ability even after abandoning ownership, and is often an indicator of malicious intent. When a hidden owner exists, it is safe to assume that ownership has not been abandoned.
                Self-Destruct    selfdestruct    Returns "1" if the contract can self-destruct;
                "0" if it cannot.
                Will not be returned if self-destruct data is unknown.    (1) Will not be returned if "is_open_source" is 0.
                (2) When the self-destruct function is triggered, the contract will be destroyed, all of its functions will be unavailable, and all related assets will be erased.
                With External Call    external_call    Returns "1" if the contract can call functions in other contracts during the execution of primary methods;
                "0" if it does not.
                Will not be returned if external call capability is unknown.    (1) Will return no data if "is_open_source" is 0.
                (2) External calls causes the implementation of this contract to be dependent on other external contracts which may or may not be risky.
                Gas abuse    gas_abuse    Return "1" if the contract is using user's gas fee to mint other assets.
                No return means no evidence of gas abuse.    Any interaction with such addresses may result in loss of property.

                Trading Security
                Security Item    Parameter    Description    Notice
                Is in DEX    is_in_dex    Returns "1" if the token can be traded in a decentralized exchange; "0" if not.    Only true if the token has a marketing pair with mainstream coins/tokens.
                Buy Tax    buy_tax    Returns the buy tax of the token on a scale from 0 - 1. An empty string ("") means that the tax is unknown.    (1) Will not be returned if "is_in_dex" is 0.
                (2) When buying a token, a buy tax will cause the actual token value received to be less than the amount paid. An excessive buy tax may lead to heavy losses.
                (3) A "buy_tax" of "1", or a 100% buy tax, will result in all purchase funds to go towards the tax. This results in a token that is effectively not able to be purchased.
                (4) A token's anti-bot mechanism may affect our sandbox environment, resulting in a "cannot_buy" of "1". This will cause the "buy_tax" to also return "1".
                (5) Some tokens are designed not to be sold, indicated by the "cannot_buy" to return "1". A "cannot_buy" of "1" will cause the display of "buy_tax" to also be "1".
                Sell Tax    sell_tax    It describes the tax when selling the token. An empty string ("") means unknown.    (1) When "is_in_dex": "0", there will be no return.
                (2) Sell tax will cause the actual value received when selling a token to be less than expected, and too much buy tax may lead to large losses.
                (3) When "sell_tax": "1", it means sell-tax is 100% or this token cannot be sold.
                (4) Sometimes token's trading-cool-down mechanism would affect our sandbox system. When "trading_cooldown": "1", "sell_tax" may return "1".
                Cannot be bought    cannot_buy    It describes whether the Token can be bought. "1" means true; "0" means false; No return means unknown.    (1) Generally, "cannot_buy": "1" would be found in Reward Tokens. Such Tokens are issued as rewards for some on-chain applications and cannot be bought directly by users.
                (2) Sometimes token's anti-bot mechanism would affect our sandbox system, causing the display of "buy_tax": "1".
                (3) When “cannot_buy”: "1", our sandbox system might be blocked, causing the display of "buy_tax": "1" and "sell_tax": "1"
                Cannot Sell All    cannot_sell_all    It describes whether the contract has the function restricting the token holders from selling all the tokens. "1" means true; "0" means false; No return means unknown.    (1) When "is_in_dex": "0", there will be no return.
                (2) This feature means that you will not be able to sell all your tokens in a single sale. Sometimes you need to leave a certain percentage of the token, e.g. 10%, sometimes you need to leave a fixed number of tokens, such as 10 tokens.
                (3) When "buy_tax": "1", there will be no return.
                Modifiable Tax    slippage_modifiable    It describes whether the trading tax can be modifiable by token contract. "1" means true; "0" means false; No return means unknown.    (1) When When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) Token with modifiable tax means that the contract owner can modify the buy tax or sell tax of the token. This may cause some losses, especially since some contracts have unlimited modifiable tax rates, which would make the token untradeable.
                (4) This function generally relies on ownership. When the contract does not have an owner (or if the owner is a black hole address) and the owner cannot be retrieved, this function will most likely be disabled.
                Honeypot    is_honeypot    It describes whether the token is a honeypot. "HoneyPot" means that the token maybe cannot be sold because of the token contract's function, or the token contains malicious code. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) High risk, definitely scam.
                Pausable Transfer    transfer_pausable    It describes whether trading can be pausable by token contract. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) This feature means that the contract owner will be able to suspend trading at any time, after that anyone will not be able to sell, except those who have special authority.
                (4) This function generally relies on ownership. When the contract does not have an owner (or if the owner is a black hole address) and the owner cannot be retrieved, this function will most likely be disabled.
                Blacklist    is_blacklisted    It describes whether the blacklist function is not included in the contract. If there is a blacklist, some addresses may not be able to trade normally. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) The contract owner may add any address to the blacklist, and the token holder in the blacklist will not be able to trade. Abuse of the blacklist function will lead to great risks.
                (4) For contracts without an owner (or the owner is a black hole address), the blacklist will not be able to get updated. However, the existing blacklist is still in effect.
                Whitelist    is_whitelisted    It describes whether the whitelist function is not included in the contract. If there is a whitelist, some addresses may not be able to trade normally. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) Whitelisting is mostly used to allow specific addresses to make early transactions, tax-free, and not affected by transaction suspension.
                (4) For contracts without an owner (or the owner is a black hole address), the whitelist will not be able to get updated. However, the existing whitelist is still in effect.
                Dex info    dex    It describes Dex's information on where the token can be traded.
                (1) "liquidity_type" the type of the liquidity pool. Only UniV2 and UniV3 is supported.
                (2) "name" is the name of the pool contact.
                (3) "liquidity" is the total USD value of the liquidity pool.
                (4) "pair" is the address of the liquidity pool.    (1) When "is_in_dex": "0", there will be an empty array.
                (2) It only counts when the token has a marketing pair with mainstream coins/tokens.
                (3) Liquidity is converted to USDT denomination.
                Anti Whale    is_anti_whale    It describes whether the contract has the function to limit the maximum amount of transactions or the maximum token position for a single address. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                Modifiable anti whale    anti_whale_modifiable    It describes whether the contract has the function to modify the maximum amount of transactions or the maximum token position. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3)When the anti-whale value is set to a very small value, all trading would fail.
                Trading with CooldownTime    trading_cooldown    It describes whether the contract has a trading-cool-down mechanism that can limit the minimum time between two transactions. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                Assigned Address' Slippage is Modifiable    personal_slippage_modifiable    It describes whether the owner can set a different tax rate for every assigned address. "1" means true; "0" means false; No return means unknown.    (1) When "is_open_source": "0", there will be no return.
                (2) Sometimes, when "is_proxy": "1", there will be no return.
                (3) The contract owner may set a very outrageous tax rate for an assigned address to block it from trading. Abuse of this function will lead to great risks.
                (4) For contracts without an owner (or the owner is a black hole address), this function would not be able to be used. However, the existing tax rate would be still in effect.

                Info Security
                Security Items    Parameter    Description    Notice
                Token Name    token_name
                Token Symbol    token_symbol
                Token holder number    holder_count    It describes the number of token holders. Example:"holder_count": "4342"
                Token Total Supply    total_supply    It describes the supply number of the token. Example:"total_supply": 100000000
                Top10 holders info    holders    It describes the top 10 holders' info. The info includes:
                (1) "address" describes the holder's address; (2) "locked." describes whether the tokens owned by the holder are locked "1" means true; "0" means false;
                (3) "tag" describes the address's public tag. Example: Burn Address/Deployer;
                (4) "is_contract" describes whether the holder is a contract "1" means true; "0" means false;
                (5) "balance" describes the balance of the holder.
                (6) "percent" describes the percentage of tokens held by this holder;
                (7) "locked_detail" is an array, that describes the lock position info of this holder, and only shows when "locked": 1. This Array may contain multiple objects for multiple locking info. In every object, "amount" describes the number of tokens locked, "end_time" describes when the token will be unlocked, and "opt_time" describes when the token was locked.    (1) About "locked": We only support the token lock addresses or black hole addresses that we have included.
                (2) When "locked":0, or lock address is a black hole address, "locked_detail" will be no return.
                (3) About "percent": 1 means 100% here.
                Owner Balance    owner_balance    It describes the balance of the contract owner.
                Example:"owner_balance": 100000000
                No return or return empty means there is no ownership or can't find ownership.    When "owner_address" returns empty, or no return, there will be no return.
                Token Percentage of Owner    owner_percent    It describes the percentage of tokens held by the contract owner.
                Example: "owner_balance": "0.1".
                No return or return empty means there is no ownership or can't find ownership.    (1) 1 means 100% here.
                (2) When "owner_address" returns empty, or no return, there will be no return.
                Creator Address    creator_address    It describes this contract's owner address.
                Creator Balance    creator_balance    It describes the balance of the contract owner. Example:"owner_balance": 100000000.
                Token Percentage of Creator    creator_percent    It describes the percentage of tokens held by the contract owner. Example:"owner_balance": 0.1.    1 means 100% here.
                LP token holder number    lp_holder_count    It describes the number of LP token holders.
                Example: "lp_holder_count": "4342".
                No return means no LP.    When "is_in_dex": "0", there will be no return.
                LP Token Total Supply    lp_total_supply    It describes the supply number of the LP token.
                Example: "lp_total_supply": "100000000".
                No return means no LP.    (1) When "is_in_dex": "0", there will be no return.
                (2) It is LP token number, NOT a token number
                Top10 LP token holders info    lp_holders    It describes the top 10 LP holders' info. The info includes:
                (1) "address" describes the holder's address;
                (2) "locked" describes whether the tokens owned by the holder are locked. "1" means true; "0" means false;
                (3) “tag” describes the address's public tag. Example: Burn Address/Deployer;
                (4) “is_contract” describes whether the holder is a contract "1" means true; "0" means false.
                (5) “balance” describes the balance of the holder.
                (6) "percent" describes the percentage of tokens held by this holder.
                (7) "NFT_list":When "liquidity_type" is UniV3, this parameter will appear. It describes the details of the UniV3 LP Holder's actual position.
                I. "value" is the total USD value corresponding to the NFT.
                II. "NFT_id" is the NFTID corresponding to the NFT.
                III. "amount" is the liquidity quantity corresponding to the NFT.
                IV "in_effect" indicates whether the liquidity corresponding to this NFT is effective at the current price.
                V. "NFT_percentage" is the proportion of this NFT in the total liquidity.
                (7) "locked_detail" is an array, that describes the lock position info of this holder, and only shows when "locked": "1". This Array may contain multiple objects for multiple locking info. In every object, "amount" describes the number of tokens locked, "end_time" describes when the token will be unlocked, and "opt_time" describes when the token was locked.
                No return means no LP. When "liquidity_type" is UniV3, the fields "value," "NFT_id," "amount," "in_effect," and "NFT_percentage" will also appear here. The rules are consistent with the "NFT_list."    (1) When "is_in_dex": "0", there will be no return.
                (2) About "locked": We only support the token lock addresses or black hole addresses that we have included.
                (3) About "percent": 1 means 100% here.
                (4) When "locked": "0", or the lock address is a black hole address, "locked_detail" will be no return.
                Airdrop Scam    is_airdrop_scam    It describes whether the token is an airdrop scam. "1" means true; "0" means false; None means no result (Because We did not find conclusive information on whether the token is an airdrop scam).    Only "is_airdrop_scam": "1" means it is an airdrop scam.
                Trust List    trust_list    It describes whether the token is a famous and trustworthy one. "1" means true; No return no result (Because We did not find conclusive information on whether the token is an airdrop scam).    (1) Only "trust_list": "1" means it is a famous and trustworthy token. (2) No return doesn't mean it is risky.
                Other Potential Risks    other_potential_risks    It describes whether the contract has other potential risks. Example: "other_potential_risks": "Owner can set different transaction taxes for each user, which can trigger serious losses."    (1) If we haven't found any other potential risk yet, there will be no return.
                (2) Type: string.
                Note    note    It describes whether the contract has other things investors need to know. Example: "note": "Contract owner is a multisign contract."    (1) If we haven't found any other thing which is valuable yet, there will be no return.
                (2) Type: string.
                Fake Token    "fake_token": { "true_token_address": "", "value": },    It indicates whether the token is a counterfeit of a mainstream asset. If it is, the value is set to 1, and true_token_address is the address of the authentic mainstream asset that the token is imitating on this public chain. If there are multiple mainstream assets with the same name, they will be separated by commas.
                Example:
                "fake_token": { "true_token_address": "0xff970a61a04b1ca14834a43f5de4533ebddb5cc8, 0xaf88d065e77c8cc2239327c5edb3a432268e5831", "value": 1 }    If there is no evidence indicating that it is a counterfeit asset, there will be no return.
                In Major Cex    "is_in_cex": { "listed": "1", "cex_list": [ ] },    Whether this token has been listed on major centralized exchanges and can be considered widely trusted within the industry, with relatively low risk.
                Example
                "is_in_cex": { "listed": "1", "cex_list": [ "Binance" ] },    (1) If we cannot find this token in our known CEXs, this field will not be returned. It doesn't mean it is risky
                (2) When 'listed' is 1, other risk alerts can generally be ignored, and the token can be considered safe.

                 */
                $data .= ($gopluslabs['is_honeypot'] == 0 ? "✅ *Does not seem like a honeypot* ✅\n" : "🚨 *THIS IS HONEYPOT* 🚨\n") . "\n";

                $data .= "🆔 *Token Name*: `{$gopluslabs['token_name']}`\n";
                $data .= "🚚 *Total Supply*: " . number_format($gopluslabs['total_supply']) . " `{$gopluslabs['token_symbol']}`\n\n";

                $gopluslabsbuytax = isset($gopluslabs['buy_tax']) ? floatval($gopluslabs['buy_tax']) * 100 : 'undefined';
                $data .= "🟢 *Buy Tax*: " . ($gopluslabsbuytax === 'undefined' ? '_undefined_' : "{$gopluslabsbuytax}%") . "\n";

                $gopluslabsselltax = isset($gopluslabs['sell_tax']) ? floatval($gopluslabs['sell_tax']) * 100 : 'undefined';
                $data .= "🔴 *Sell Tax*: " . ($gopluslabsselltax === 'undefined' ? '_undefined_' : "{$gopluslabsselltax}%") . "\n\n";

                $data .= "👤 *Creator*: `{$gopluslabs['creator_address']}`\n";
                $data .= "🫰🏻 *Balance*: " . number_format($gopluslabs['creator_balance']) . " | ";
                $data .= "👉 *Percent*: " . (floatval($gopluslabs['creator_percent']) * 100) . "%\n\n";

                $data .= "👥 *Token Holders*: " . number_format($gopluslabs['holder_count']) . "\n";
                $data .= "🧊 *LP Holders*: " . number_format($gopluslabs['lp_holder_count']) . "\n\n";
                break;
        }

        return $data;
    }

    public function mainMenu($actor)
    {
        $reply = array();

        $text = "👋 *Bienvenido al " . $this->telegram["username"] . "*!\n\n" .
            "_Este bot esta diseñado para analizar contratos de monedas en varias blockchains_.\n\n";

        $menu = array();

        $this->ActorsController->updateData(Actors::class, "user_id", $actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);

        $text .= "👇 En qué le puedo ayudar hoy?";

        array_push($menu, [
            ["text" => "⚙️ Configuración", "callback_data" => "configmenu"],
            ["text" => "🆘 Ayuda", "callback_data" => "help"],
        ]);

        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        return $reply;
    }
    public function detectBlockchain($address)
    {
        // Validadores específicos por formato
        if (preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
            // Formato hexadecimal para Ethereum y blockchains similares
            return ["eth", "opt", "cro", "bsc", "gno", "heco", "pol", "fan", "kcc", "era", "arb", "ava"];
        } elseif (preg_match('/^[1-9A-HJ-NP-Za-km-z]{43,44}$/', $address)) {
            // Formato Base58 para blockchains como Solana y Tron
            return ["tron"];
        } elseif (preg_match('/^[a-zA-Z0-9]{34}$/', $address)) {
            // Formato para blockchains como Bitcoin o derivadas
            return ["btc"];
        } else {
            return false;
        }
    }

    public static $BLOCKCHAINS = array(
        "eth" => [
            "gopluslabs" => [
                "id" => "1",
                "name" => "Ethereum",
            ],
        ],
        "opt" => [
            "gopluslabs" => [
                "id" => "10",
                "name" => "Optimism",
            ],
        ],
        "cro" => [
            "gopluslabs" => [
                "id" => "25",
                "name" => "Cronos",
            ],
        ],
        "heco" => [
            "gopluslabs" => [
                "id" => "128",
                "name" => "HECO",
            ],
        ],
        "pol" => [
            "gopluslabs" => [
                "id" => "137",
                "name" => "Polygon",
            ],
        ],
        "fan" => [
            "gopluslabs" => [
                "id" => "250",
                "name" => "Fantom",
            ],
        ],
        "kcc" => [
            "gopluslabs" => [
                "id" => "321",
                "name" => "KCC",
            ],
        ],
        "arb" => [
            "gopluslabs" => [
                "id" => "42161",
                "name" => "Arbitrum",
            ],
        ],
        "ava" => [
            "gopluslabs" => [
                "id" => "43114",
                "name" => "Avalanche",
            ],
        ],
        "har" => [
            "gopluslabs" => [
                "id" => "1666600000",
                "name" => "",
            ],
        ],
        "trx" => [
            "gopluslabs" => [
                "id" => "tron",
                "name" => "Tron",
            ],
        ],
        "sol" => [
            "gopluslabs" => [
                "id" => "solana",
                "name" => "Solana",
            ],
        ],
        "bsc" => [
            "gopluslabs" => [
                "id" => "56",
                "name" => "BSC",
            ],
        ],
        "era" => [
            "gopluslabs" => [
                "id" => "324",
                "name" => "zkSync Era",
            ],
        ],
        "10001" => [
            "gopluslabs" => [
                "id" => "10001",
                "name" => "ETHW",
            ],
        ],
        "201022" => [
            "gopluslabs" => [
                "id" => "201022",
                "name" => "FON",
            ],
        ],
        "59144" => [
            "gopluslabs" => [
                "id" => "59144",
                "name" => "Linea Mainnet",
            ],
        ],
        "8453" => [
            "gopluslabs" => [
                "id" => "8453",
                "name" => "Base",
            ],
        ],
        "534352" => [
            "gopluslabs" => [
                "id" => "534352",
                "name" => "Scroll",
            ],
        ],
        "204" => [
            "gopluslabs" => [
                "id" => "204",
                "name" => "opBNB",
            ],
        ],
        "5000" => [
            "gopluslabs" => [
                "id" => "5000",
                "name" => "Mantle",
            ],
        ],
        "42766" => [
            "gopluslabs" => [
                "id" => "42766",
                "name" => "ZKFair",
            ],
        ],
        "81457" => [
            "gopluslabs" => [
                "id" => "81457",
                "name" => "Blast",
            ],
        ],
        "169" => [
            "gopluslabs" => [
                "id" => "169",
                "name" => "Manta Pacific",
            ],
        ],
        "80085" => [
            "gopluslabs" => [
                "id" => "80085",
                "name" => "Berachain Artio Testnet",
            ],
        ],
        "4200" => [
            "gopluslabs" => [
                "id" => "4200",
                "name" => "Merlin",
            ],
        ],
        "200901" => [
            "gopluslabs" => [
                "id" => "200901",
                "name" => "Bitlayer Mainnet",
            ],
        ],
        "810180" => [
            "gopluslabs" => [
                "id" => "810180",
                "name" => "zkLink Nova",
            ],
        ],
        "196" => [
            "gopluslabs" => [
                "id" => "196",
                "name" => "X Layer Mainnet",
            ],
        ],
        "gno" => [
            "gopluslabs" => [
                "id" => "100",
                "name" => "Gnosis",
            ],
        ],
    );

}
