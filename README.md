# GumboAdministratie
Administratiesysteem voor Gumbo Millennium. Wordt gebruikt door het bestuur om de persoonsgegevens van de leden te beheren. Gebaseerd op het Aurora framework van Bart Willemsen

# Installatie
Voeg het volgende toe aan je hosts bestand
```sh
127.0.0.1 gumbo.local
```

Voeg een VirtualHost toe. De DocumentRoot verwijst naar de public map van het project. Dit kan voor jou uiteraard een ander pad zijn. Vergeet niet na deze wijzigingen je webserver opnieuw op te starten. 
```sh
<VirtualHost *:80>
    ServerName gumbo.local
    DocumentRoot C:/xampp/htdocs/gumbo/public
</VirtualHost>
```
In de map mysql staan 2 .sql bestanden. Voer deze in de onderstaande volgorde uit.  
```sh
Gumbo.sql (structuur)
Data.sql (data)
Changes.sql (database wijzigingen na de initiële creatie)
```
Er is standaard 1 account aanwezig. De inloggegevens hiervan zijn: 
```sh
admin@gumbo.nl
Welkom123
```
Als alles goed is gegaan kan je nu inloggen met bovenstaande gegevens via http://gumbo.local