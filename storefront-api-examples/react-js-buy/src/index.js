import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import Client from 'shopify-buy';
import '../../shared/app.css';

const client = Client.buildClient({
  storefrontAccessToken: '1415aa94669d7aa14e790711fc6f064f',
  domain: 'dnadistribution.myshopify.com'
});

ReactDOM.render(
  <App client={client}/>,
  document.getElementById('root')
);
