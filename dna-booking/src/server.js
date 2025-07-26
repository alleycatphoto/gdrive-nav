const jwt = require("jsonwebtoken");
const express = require("express");
const app = express();

// server.js
//
// Use this sample code to handle webhook events in your expressjs server.
//
// 1) Paste this code into a new file (server.js)
//
// 2) Install dependencies
//   npm install jsonwebtoken
//   npm install express
//
// 3) Run the server on http://localhost:3000
//   node server.js

// consider loading your public key from a file or an environment variable
const PUBLIC_KEY = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhpuRQCEGHKWveoqV/1ru
+qIsaAAcSEF3LpDUHkGUAEPvtLmcNtrVpdkSDARleW+Eeda7q/aj/UjHaR31kead
5Rp3g1FC7t6gIqaHoNANs9hTxR78h0YZCCJbrJi/v5aWD6pv07z9ll2xXX+LUn5s
SHeo+DJtK2zJlyR/AcYV2WPy1okTTtqLUqCpVE0SKBpcSl+TYOc7lGZU+Yv3knlN
j1wDQV26Fvst7+6B0eTAUPBFGU1QjLwchNqmL9hxABXI9413DhHjDnEm7KOKr0gX
Rh+P4/GfBNUu9ypwqaPVWvQBSg4Dwb1swJ20iGVxSOEF97n8mX325a/baPEMPH3P
TwIDAQAB
-----END PUBLIC KEY-----`;

app.post('/webhook', express.text(), (request, response) => {
  let event;
  let eventData;

  try {
    const rawPayload = jwt.verify(request.body, PUBLIC_KEY);
    event = JSON.parse(rawPayload.data);
    eventData = JSON.parse(event.data);
  } catch (err) {
    console.error(err);
    response.status(400).send(`Webhook error: ${err.message}`);
    return;
  }

  switch (event.eventType) {
    case "com.wixpress.formbuilder.api.v1.FormSubmittedEvent":
      console.log(`com.wixpress.formbuilder.api.v1.FormSubmittedEvent event received with data:`, eventData);
      //
      // handle your event here
      //
      break;
    default:
      console.log(`Received unknown event type: ${event.eventType}`);
      break;
  }

  response.status(200).send();

});

app.listen(3000, () => console.log("Server started on port 3000"));
