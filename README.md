# Forum
Ett forum om programmering. Systemet utvecklades som slutuppgift i gymnasiekursen Webbserverprogrammering 1.

# Kodstruktur
public - Publikt tillgängliga filer, t.ex css och javascript.
app - PHP backend kod
views - Olika vyer för olika sidor

public/index.php - Startsidan dit alla anrop kommer, den innehåller en router som anropar korrekt kontroller.

Webbroten ska vara satt till public mappen alternativt ska en .htaccess fil i kodroten finnas om Apache2 används

# Starta från källkod
```
git clone ...
cd Forum
cp .env.example .env
  Uppdatera .env
composer install
composer dump-autoload
```

## Nginx
Om Nginx används som webbserver behöver följande finnas i server-blocket
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

# Tredjepartskod
https://github.com/bramus/router
.htaccess från Laravel

# Övrigt
Satt upp autmoatisk inläsning av php klasser enligt https://vegibit.com/how-to-use-autoloading-in-php/

Varför View klass?
    Enkapsulerar vad vyn kan komma åt (vyn kan endast använda variabler kontrollern  uttryckligen skickats till den)
    Möjlighet att utöka med mer funktionalitet utan att behöva uppdatera alla ställen där en vy visas.
    Enhetligt sätt att rendera vyer