<?php

class myOrder
{
    private $CLIENT_ID = "AQT9RqldMu7UqAJAMDb7HaiKYzal53a6RcFFWkVYH_p9gRrB6p_LAD2kj4kTwRoEHvs0j-1QUWjJC7xF";
    private $APP_SECRET = "EKa_vUhvZNpWW5A274YS-8FvZY9CoV8PL-EXIlb2xfapArTuKmrI3htYVC4eKV_SY0RLfI6F0qj1sHzE";
    private $accessToken;
    private $orderID;


    private function generateAccessToken()
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => "https://api.sandbox.paypal.com/v1/oauth2/token",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=client_credentials",
                CURLOPT_USERPWD => $this->CLIENT_ID . ":" . $this->APP_SECRET,
                CURLOPT_HEADER => 0,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                ],
            ]
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        $this->accessToken = $response->access_token;
    }

    // Create order

    public function createOrder()
    {
        $nomLivraison = "L'Alexandre";
        $prenomLivraison = "Tanguy";

        $this->generateAccessToken();

        $data = [
            "purchase_units" => [
                [

                    "custom_id" => "1067317 - Achat effectué sur la-botte.com . Mode de paiement : ",
                    "reference_id" => "1067317",
                    "description" => "Achat effectué sur la-botte.com . Mode de paiement : ",
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => "50",
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => "EUR",
                                "value" => "50"
                            ],
                            "tax_total" => [
                                "currency_code" => "EUR",
                                "value" => "0"
                            ],
                            "shipping" => [
                                "currency_code" => "EUR",
                                "value" => "0"
                            ],
                            "discount" => [
                                "currency_code" => "EUR",
                                "value" => "0"
                            ],
                        ]
                    ],
                    "shipping" => [
                        "address" => [
                            "address_line_1" => "Av. César Ossola",
                            "address_line_2" => "50 RUE GRANDE ",
                            "admin_area_2" => "Saint-Laurent-du-Var",
                            "admin_area_1" => "",
                            "postal_code" => "06700",
                            "country_code" => "FR"
                        ],
                        "type" => "SHIPPING",
                        "name" => [
                            "full_name" => $nomLivraison . " " . $prenomLivraison
                        ]
                    ],
                    "items" => [
                        [
                            "name" => "ARIZONA KIDS BIRKO MOCCA",
                            "description" => "30",
                            "unit_amount" => [
                                "currency_code" => "EUR",
                                "value" => "50"
                            ],
                            "quantity" => "1",
                        ]
                    ]
                ]
            ],
            "intent" => "CAPTURE",
            "payment_source" => [
                "paypal" => [
                    "name" => [
                        "given_name" => "Tanguy",
                        "surname" => "L'Alexandre"
                    ],
                    "email_address" => "tanguy.lalexandre@gmail.com",
                    "address" => [
                        "address_line_1" => "Av. César Ossola",
                        "address_line_2" => "50 RUE GRANDE",
                        "admin_area_2" => "Saint-Laurent-du-Var",
                        "admin_area_1" => "",
                        "postal_code" => "06700",
                        "country_code" => "FR"
                    ],
                    "experience_context" => [
                        "payment_method_selected" => "PAYPAL",
                        "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                        "landing_page" => "LOGIN",
                        "user_action" => "PAY_NOW",
                        "locale" => "fr-FR",
                        "return_url" => "https://example.com/returnUrl",
                        "cancel_url" => "https://example.com/cancelUrl",
                    ],
                ]
            ]

        ];

        $requestid = "new-order-" . date("Y-m-d-h-i-s");

        $json = json_encode($data);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->accessToken,
                    "PayPal-Request-Id: " . $requestid,
                    "Prefer: return=representation"
                ),
                CURLOPT_POSTFIELDS => $json,
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        print_r($response);
    }

    public function capturePayment()
    {
        $this->generateAccessToken();
        $this->orderID = $_GET['order'];

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/" . $this->orderID . "/capture",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->accessToken,
                    "Prefer: return=representation"
                ),
            ]
        );

        $response = curl_exec($curl);

        curl_close($curl);

        print_r($response);
    }


    public function webhookListener()
    {
        // Read the incoming webhook payload
        $payload = file_get_contents('php://input');
        $headers = getallheaders();

        // Extract required headers
        $transmissionId = $headers['Paypal-Transmission-Id'] ?? '';
        $timeStamp = $headers['Paypal-Transmission-Time'] ?? '';
        $transmissionSig = $headers['Paypal-Transmission-Sig'] ?? '';
        $certUrl = $headers['Paypal-Cert-Url'] ?? '';
        $authAlgo = $headers['Paypal-Auth-Algo'] ?? '';
        $webhookId = '80807288RF140422P'; // Replace with your actual webhook ID

        // Debugging logs
        error_log("Transmission ID: " . $transmissionId);
        error_log("Timestamp: " . $timeStamp);
        error_log("Signature: " . $transmissionSig);
        error_log("Certificate URL: " . $certUrl);
        error_log("Auth Algorithm: " . $authAlgo);
        error_log("Raw Payload: " . $payload);

        // Map PayPal's auth algorithm to OpenSSL's algorithm
        $opensslAlgo = match ($authAlgo) {
            'SHA256withRSA' => OPENSSL_ALGO_SHA256,
            default => null,
        };

        if ($opensslAlgo === null) {
            error_log("Unknown PayPal auth algorithm: $authAlgo");
            http_response_code(400);
            echo "Unknown auth algorithm.";
            return;
        }

        // Form the original message string
        $crc32 = sprintf('%u', crc32($payload));
        $message = $transmissionId . '|' . $timeStamp . '|' . $webhookId . '|' . $crc32;
        error_log("Original Message: " . $message);

        // Fetch the certificate from PayPal
        $cert = file_get_contents($certUrl);
        if (!$cert) {
            error_log("Failed to fetch PayPal certificate.");
            http_response_code(400);
            echo "Invalid certificate URL.";
            return;
        }

        // Verify the signature
        $pubKey = openssl_pkey_get_public($cert);
        if (!$pubKey) {
            error_log("Failed to extract public key from certificate.");
            http_response_code(400);
            echo "Invalid public key.";
            return;
        }

        $isValid = openssl_verify($message, base64_decode($transmissionSig), $pubKey, $opensslAlgo);

        if ($isValid === 1) {
            error_log("Webhook signature verified successfully.");
            error_log("Payload: " . $payload);
            http_response_code(200);
            echo "Webhook verified successfully.";
        } elseif ($isValid === 0) {
            error_log("Invalid webhook signature.");
            http_response_code(400);
            echo "Invalid webhook signature.";
        } else {
            error_log("Error verifying webhook signature: " . openssl_error_string());
            http_response_code(500);
            echo "Error verifying webhook signature.";
        }
    }
}


$myOrder = new MyOrder();

if (isset($_GET['task'])) {
    $task = $_GET['task'];

    if ($task == 'createOrder') $myOrder->createOrder();
    if ($task == 'capturePayment') $myOrder->capturePayment();
    if ($task == 'webhookListener') $myOrder->webhookListener();
}
