# JTL-Shop PayPal Plugin Changelog

## [1.16]
* Bugfix: Deutlicherer Hinweis bei erneuter Autorisierung nach PSD2-Overcharge.

## [1.15]
* Bugfix: Wenn PayPal-Express-Zahlungen wg. PSD2-Overcharge abgelehnt werden, kommt es zu doppelten Bestellungen [SHOP-6560](https://issues.jtl-software.de/issues/SHOP-6560)

## [1.14]

* Bugfix: Bei PayPal Lastschrift oder Kreditkarte wird Lieferadresse als Rechnungsadresse übermittelt [SHOP-4391](https://issues.jtl-software.de/issues/SHOP-4391)
* Bugfix: Fehlermeldungen für Endkunden optimieren [SHOP-4518](https://issues.jtl-software.de/issues/SHOP-4518)

## [1.13]

* Die Zahlungsart PayPal Finance wurde entfernt
* PayPal Ratenzahlung Banner wurden hinzugefügt
* Bugfix: Falsches Verhalten im eingeloggten Zustand, wenn PayPal Emailadresse abweicht [SHOP-4484](https://issues.jtl-software.de/issues/SHOP-4484)

## [1.12]

* Bugfix: Fehler "Invalid Token" abfangen [SHOP-3369](https://issues.jtl-software.de/issues/SHOP-3369)
* Bugfix: Integration des PayPal-Bannersnippets für Ratenzahlung verursacht JS-Fehler [SHOP-4182](https://issues.jtl-software.de/issues/SHOP-4182)

## [1.11]

* Bugfix: PayPal PLUS: Bestellnummer / Rechnungsnummer wird nicht an PayPal gesendet [SHOP-3352](https://issues.jtl-software.de/issues/SHOP-3352)
* Bugfix: PayPal plus beachtet Kundengruppenbeschränkung nicht [SHOP-2350](https://issues.jtl-software.de/issues/SHOP-2350)
* Bugfix: Bei PP-Express muss das Fehlen der Bestellung abgefangen werden [SHOP-3812](https://issues.jtl-software.de/issues/SHOP-3812)
* PayPal Express: Nach Installation sollen alle Anzeigeorte für die Express-Buttons aktiv sein [SHOP-3704](https://issues.jtl-software.de/issues/SHOP-3704)

## [1.10]

* Bugfix: Währungsumrechnung PayPal Basic
* Bugfix: PayPal Finance abweichende Lieferadresse
* PayPal Plus übermittelt die Rechnungsadresse
* PayPal Express Auswahl einer alternativen Finanzierungsquelle ([?](https://developer.paypal.com/docs/classic/express-checkout/ht_ec_fundingfailure10486))
* Bestellabschluss optimiert
* PHP 7.2 kompatibel

## [1.09]

* Übersicht (Modus, Zugangsdaten, Mit Versandart verknüpft)
* PayPal Express Button für Mini-Warenkorb / Accountauswahl
* Bugfix: PHP 7.x kompatibel
* Bugfix: TrustedShops Template
* Bugfix: Session Timeout bei Bezahlung vor Bestellabschluss

## [1.08]

**Ratenzahlung Powered by PayPal: Testphase erfolgreich abgeschlossen**

* TrustedShops Excellence-Integration in PayPal PLUS
* Sandbox-Bestellungen werden nun gesondert markiert (Anmerkung "SANDBOX-BESTELLUNG")
* Unterstützung des verkürzten Checkouts (JTL-Shop 4.06)
* Weitere Anzeigemöglichkeiten der Finanzierungsbox (Ratenzahlung Powered by PayPal)
* PayPal Express übernimmt Käufer-Telefonnummer
* Bugfix: Fehlerhafte Umlaute in Adressdaten (PayPal PLUS)
* Bugfix: Payment Wall wird nur in Sprache Deutsch dargestellt
* Bugfix: PayPal Express bei Einstellung (Warenkorb-Weiterleitung) ohne Funktion
* Bugfix: Aufpreise/Rabatte zuvor gewählter Zahlungsarten werden nicht entfernt

## [1.07]

* Zahlart "Rechnung (PayPal PLUS)" umbenannt in "Kauf auf Rechnung (PayPal PLUS)". Bitte in JTL-Wawi eine Zahlart mit identischer Bezeichnung hinterlegen, damit eine Zuordnung möglich ist. 
* Diverse Bugfixes für Ratenzahlung Powered by PayPal
* Bestellübersicht aller PayPal Bestellungen
* Mehrfachverwendung von PayPal-Apps (Webhooks)
* Bugfix: PayPal Express: Button wird nicht angezeigt
* Bugfix: PayPal Express: Fatal Error im Bestellabschluss
* Bugfix: PayPal Plus: Falsche Bestellnummer auf der Bestellabschlussseite
* Bugfix: PayPal Basic: Nachträglicher Zahlungsversuch funktioniert nicht

## [1.06]

* Authorization-Cache nutzen, um Fehler "too many requests" zu vermeiden (#361)
* Bei sofortigem Negativ-PaymentStatus Rückleitung zur Zahlungsartwahl (Bestellung nicht persistieren!) (#284)
* PayPal PLUS: Zahlungsartname bei Auswahl von Kauf auf Rechnung ändern in "Rechnung (PayPal PLUS)"
* Gratisgeschenke werden nun als Item gelistet
* PayPal Basic: Guthaben nutzen leitet auf Zahlungsart-Auswahl (#313)
* PayPal Basic: Invalidierung des Warenkorbs bei Abbruch der Zahlung in PayPal und Rückleitung zum Shop (#103)
* PayPal Basic: TrustedShops Excellence Käuferschutzgebühr wird nun an PayPal übertragen (#315)
* PayPal PLUS: Third Party Zahlungsarten werden nun unterhalb der Payment Wall gelistet (keine Limitierung mehr)
* PayPal PLUS Payment Wall Style-Support (neue Einstellungen)
* PayPal PLUS: Bei Kauf auf Rechnung soll keine Zahlungsbestätigungsmail gesendet werden (#618)
* Bugfix: TLS-Check lieferte teilweise falsche Ergebnisse, da Version nicht festgelegt war
* Bugfix: Einlösen eines Kupons in Kombination mit der Zahlungsart PayPal Basic nicht möglich (#373)
* Bugfix: PayPal PLUS: Bestellnummer wird nicht korrekt an PayPal übersendet (#437)
* Bugfix: Bundesland (Freitext) wird nicht an PayPal PLUS übergeben
* Bugfix: PayPal PLUS: Rundungsdifferenzen bei mehr als 2 Nachkommastellen werden nicht ausgeglichen ("validation error") (#317)
* Bugfix: Rundungsfehler bei PayPal mit Kuponnutzung (#272)
* Bugfix: PayPal PLUS Summenfehler: Einzelpositions-Wert ergibt nicht Gesamtsumme (#339)
* Bugfix: PayPal Express Plugin setzt UStId auf NOVATID, obwohl Feld optional ist
* Bugfix: PayPal Express Button wird nicht im Warenkorb gezeigt
* Bugfix: PayPal Express: Einstellung "Kunde soll ein Kundenkonto erhalten" funktionslos (#427)
* Bugfix: PayPal IPNs loggen einen Fehler, wenn die Bestellung bereits bezahlt wurde (#24)
* Bugfix: PayPal Express: Vor-/Nachname werden inkorrekt ermittelt (#669)
* Bugfix: Validation Error bei Lieferland Mexico (field state missing) (#779)
