<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .header { padding: 24px; color: #fff; text-align: center; }
    .body { padding: 24px; color: #333; }
    .detail-row { display: flex; margin-bottom: 8px; }
    .detail-label { font-weight: bold; min-width: 140px; color: #555; }
    .motif-box { background: #f8f9fa; border-left: 4px solid #ccc; padding: 12px; margin-top: 16px; border-radius: 4px; }
    .footer { padding: 16px 24px; background: #f8f9fa; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
    @yield('content')
    <div class="footer">
        {{ config('app.name') }} — Cet email est automatique, merci de ne pas y répondre.
    </div>
</div>
</body>
</html>
