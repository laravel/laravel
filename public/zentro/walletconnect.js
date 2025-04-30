import {
    EthereumClient,
    w3mConnectors,
    w3mProvider,
    WagmiCore,
    WagmiCoreChains,
    WagmiCoreConnectors,
} from "https://unpkg.com/@web3modal/ethereum@2.6.2";

import { Web3Modal } from "https://unpkg.com/@web3modal/html@2.6.2";

// 0. Import wagmi dependencies
const { sepolia, mainnet } = WagmiCoreChains;
const {
    configureChains,
    createConfig,
    disconnect,
    sendTransaction,
    signMessage,
    switchNetwork,
    watchNetwork,
} = WagmiCore;
import { parseEther } from "https://esm.sh/viem";

// 1. Define chains
const chains = [sepolia, mainnet];
const projectId = "3b8c51125d36693970c6405025a5dfee";

// 2. Configure wagmi client
const { publicClient } = configureChains(chains, [w3mProvider({ projectId })]);
const wagmiConfig = createConfig({
    autoConnect: true,
    connectors: [
        ...w3mConnectors({ chains, version: 2, projectId }),
        new WagmiCoreConnectors.CoinbaseWalletConnector({
            chains,
            options: {
                appName: "html wagmi example",
            },
        }),
    ],
    publicClient,
});

// 3. Create ethereum and modal clients
window.ethereumClient = new EthereumClient(wagmiConfig, chains);
window.web3Modal = new Web3Modal(
    {
        //themeMode: "dark",
        themeVariables: {
            //"--w3m-font-family": "Roboto, sans-serif",
            "--w3m-accent-color": "#ff9511",
            //"--w3m-accent-fill-color": "#28a745",
            "--w3m-background-color": "#ff9511",
            //"--w3m-background-border-radius": "20px",
            //"--w3m-container-border-radius": "1px",
        },
        projectId,
        /*
        walletImages: {
            safe: "https://pbs.twimg.com/profile_images/1566773491764023297/IvmCdGnM_400x400.jpg",
        },
        */
    },
    window.ethereumClient
);

window.disconnect = async function () {
    await disconnect();
};

window.initModule = function (onChangeNetwork) {
    watchNetwork(onChangeNetwork);

    /*
    const { hash } = sendTransaction({
        to: '0x7A287633fE25D5C4611D732e788A137c63DbB1cb',
        value: 10,
        })
		
	const signature = await signMessage({
	  message: 'gm wagmi frens',
	})
	
	const network = await switchNetwork({
	  chainId: 11155111,
	})
	
		*/
    /*
	const unwatch = watchNetwork((network) => console.log(network))
	
	
	watchAccount((accountData) => {
            console.log('accountData', accountData);
            if (accountData.isConnected) {
                // do your stuff
            } else {
                // do your stuff
            }
        });
		*/
};
