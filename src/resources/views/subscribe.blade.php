<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Subscription</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }

        .hero {
            display: flex;
            height: 100vh;
            align-items: stretch;
            justify-content: center;
            flex-wrap: wrap;
        }

        .form-container {
            background-color: #ffffff;
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #1f2937;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f9fafb;
        }

        button {
            margin-top: 10px;
            width: 100%;
            padding: 12px;
            background-color: #3b82f6;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #2563eb;
        }

        .image-section {
            flex: 1;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            height: 100vh;
            max-width: 800px;
            width: 100%;
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .image-section img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }


        .message {
            padding: 10px 14px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        .success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 1024px) {
            .hero {
                flex-direction: column-reverse;
            }

            .image-section {
                border-radius: 8px 8px 0 0;
            }

            .form-container {
                border-radius: 0 0 8px 8px;
            }
            .image-section, .form-container {
                position: absolute;
                transform: translate(-50%, -50%);
                top: 50%;
                left: 50%;
                max-width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>

<div class="hero">
    <div class="form-container">
        <h1>Subscribe to Weather Updates</h1>

        <div id="message" class="message" style="display: none;"></div>

        <form id="subscriptionForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="off" required placeholder="e.g. john@example.com">
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" autocomplete="off" required placeholder="e.g. New York">
            </div>

            <div class="form-group">
                <label for="frequency">Update Frequency</label>
                <select id="frequency" name="frequency" required>
                    <option value="hourly">hourly</option>
                    <option value="daily" selected>daily</option>
                </select>
            </div>

            <button type="submit">Subscribe</button>
        </form>
    </div>

    <div class="image-section" id="weatherImage">
        <img src="{{ asset('resources/img/hero.png') }}" alt="Weather Hero Image" />
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('subscriptionForm');
        const messageDiv = document.getElementById('message');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const city = document.getElementById('city').value;
            const frequency = document.getElementById('frequency').value;

            fetch('/api/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email, city, frequency })
            })
                .then(response => response.json())
                .then(data => {
                    messageDiv.textContent = data.message;
                    messageDiv.className = data.message.includes('already') ? 'message error' : 'message success';
                    messageDiv.style.display = 'block';
                    if (!data.message.includes('already')) form.reset();
                })
                .catch(() => {
                    messageDiv.textContent = 'An error occurred. Please try again later.';
                    messageDiv.className = 'message error';
                    messageDiv.style.display = 'block';
                });
        });
    });
</script>

</body>
</html>
