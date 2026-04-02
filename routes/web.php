<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>PolitiScan</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f8fafc;
                    color: #0f172a;
                    margin: 0;
                    padding: 40px;
                    text-align: center;
                }
                .box {
                    max-width: 700px;
                    margin: 80px auto;
                    background: white;
                    padding: 40px;
                    border-radius: 16px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                }
                h1 {
                    margin-bottom: 10px;
                }
                p {
                    line-height: 1.6;
                }
                .ok {
                    color: #16a34a;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="box">
                <h1>PolitiScan</h1>
                <p class="ok">Application déployée avec succès sur Railway.</p>
                <p>Le backend Laravel est en ligne.</p>
                <p>URL de production active pour démonstration académique.</p>
            </div>
        </body>
        </html>
    ');
});
