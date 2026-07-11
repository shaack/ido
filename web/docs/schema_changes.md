# Schemaänderungen

Das Projekt nutzt keine Migrationen. Schemaänderungen werden hier als SQL
festgehalten und von Hand auf der Produktivdatenbank ausgeführt.

## 2026-07-11: Contacts entfernt (Tabelle optional)

Kontakte werden nicht mehr gebraucht. Controller, Entity, Table, Templates und
Tests sind gelöscht, ebenso die hasMany-Assoziation an Customers und der Block
"Related Contacts" in der Kundenansicht.

Die Tabelle `contacts` bleibt vorerst stehen. Der Code rührt sie nicht mehr an,
sie stört also niemanden. Wer sie loswerden will, führt das hier aus. **Das ist
unumkehrbar**, in der Entwicklungsdatenbank stehen 30 Kontakte mit 27
E-Mail-Adressen, 15 Telefonnummern und 13 Notizen.

```sql
DROP TABLE contacts;
```

Nicht nötig für den Deploy. Der Code läuft mit und ohne die Tabelle.

## 2026-07-11: Festpreis wandert vom Projekt an die Leistung

Der Festpreis gehört fachlich an die Leistung, nicht ans Projekt.

Bisher gab es zwei Felder, die zusammenspielten. `projects.fixed_price` war ein
Ja/Nein-Schalter, und `services.estimation_or_fixed_price` trug den Eurobetrag.
War der Schalter am Projekt gesetzt, wurden alle Leistungen **ohne** eigenen
Betrag von der Rechnung ausgeschlossen. Das führte zu einer Falle: Ein
Festpreisprojekt, bei dem keine einzige Leistung einen Betrag trug, ergab eine
Rechnung über 0,00 Euro. Genau das ist bei den Projekten 179 (Rechnung 20043)
und 256 (Rechnung 22056) passiert, beide sind bezahlt, die App würde für sie
aber 0 Euro ausweisen.

Jetzt entscheidet allein die Leistung. Trägt sie einen Festpreis, wird dieser
berechnet und die erfasste Zeit ignoriert. Trägt sie keinen, wird nach Zeit
abgerechnet. Der Schalter am Projekt entfällt.

**Der Name war ebenfalls irreführend.** Trotz "Estimation Or" wurde das Feld nie
als Schätzung verwendet. Die Schätzung steckt in `services.effort_est` und ist
eine Stundenzahl fürs Angebot. Hier geht es um Euro fürs Abrechnen.

### SQL

```sql
ALTER TABLE services CHANGE estimation_or_fixed_price fixed_price DOUBLE NULL;
```

### Reihenfolge beim Deploy

Der Code erwartet die neue Spalte. Das SQL muss deshalb **vor** dem Deploy auf
der Produktivdatenbank laufen. Zwischen SQL und Deploy liefert die Seite Fehler,
weil der alte Code die alte Spalte sucht. Bei einem Einzelnutzer hinter Basic
Auth ist das Fenster vertretbar.

1. SQL auf dem Server ausführen
2. sofort danach `./deploy.sh`
3. in Plesk `tmp/cache/models/` leeren, sonst hält CakePHP den alten Spaltennamen
   im Schema-Cache fest

### Nicht gelöschte Spalte

`projects.fixed_price` (tinyint) bleibt in der Datenbank stehen, wird vom Code
aber nicht mehr gelesen oder geschrieben. So gehen die historischen Werte der 44
Festpreisprojekte nicht verloren.

Achtung beim Lesen von SQL: Es gibt jetzt `services.fixed_price` (double, ein
Betrag) und `projects.fixed_price` (tinyint, ein Ja/Nein ohne Funktion).
Verschiedene Tabellen, gleicher Name, verschiedene Bedeutung.
