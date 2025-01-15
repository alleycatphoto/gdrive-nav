import fetch from 'node-fetch';
import Client from 'shopify-buy';

global.fetch = fetch;

const client = Client.buildClient({
	storefrontAccessToken: '1415aa94669d7aa14e790711fc6f064f',
  	domain: 'dnadistribution.myshopify.com'
});

export default client;
