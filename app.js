paypal.Buttons({
    // When payment button is clicked, an order is created and an order ID is returned
    createOrder: function (data, actions) {
      return fetch('backend.php?task=createOrder', {
        method: "post",
        // use the "body" param to optionally pass
        // additional order information like product ids or amount. 
      })
        .then((response) => response.json())
        .then((order) => order.id);
    },
    
    // Finalize the transaction after payer approval
    onApprove: function (data, actions) {
      return fetch(`backend.php?task=capturePayment&order=${data.orderID}`, {
        method: "post",
      })
        .then((response) => response.json())
        .then((orderData) => {
          // Successful capture! For dev/demo purposes:
          // Once the transaction is completed, we replace the checkout with a successful message and the API response
          let div_mycart = document.getElementById("mycart");
          let div_response = document.getElementById('api-response');
          let div_json = document.getElementById('api-json');
          let res = JSON.stringify(orderData, null, 2);
  
          div_mycart.style.display = "none";
          div_response.style.display = "block";
          div_json.innerHTML = res;
        });
    },
  
    onCancel(data) {
      // Show a cancel page, or return to cart
    },
  })
    .render("#paypal-button-container");