# Produkt Editor

## Oberer Bereich.

### Produkt reservieren
Such euch einfach ein Produkt, bei dem noch kein Name steht, und reserviert es
als eures.  Manchmal kann es hilfreich sein ein Produkt anzuschauen, das von einer anderen Person reserviert wurde.
Ihr könnt temporär euren Namen ändern, und dann dieses Produkt anschauen.


Die farbig hinterlegte Zahl gibt Auskunft, wie weit das Produkt schon bearbeitet
worden ist.

- 0 → noch gar nicht bearbeitet worden
- 5 → es wurde schon etwas gespeichert
- 10 → das Produkt wurde als fertig markiert (abschließen)
- 15 → das Produkt ist von uns schon aus dem Editor ins Shop system übertragen
  worden

Ihr habt die Möglichkeit eigene Zahlen zu setzen (siehe weiter unten)
Z.B. 1 → Inhaltsstoffe fehlen noch,...

*Bitte nur die Zahlen 1-9 dafür verwenden!*

Sobald Name und Marke gespeichert worden sind, kann man auch die im oberen
Bereich sehen.


## Allgemein & Nährwerte

Beispiel:

![Cremesuppe](9000275626715A.jpg)
![Cremesuppe Rückseite](9000275626715B.jpg)

### Name
Der Name soll ohne Marke und ohne zusätzlich Beitexte sein.
`Kaiser Teller Gourmet Cremesuppe von erlesenen Waldpilzen`
Die Faustregel: wenn jemand zum Einkaufen geht, sage ich ihm, er soll mir eine Packung Kaiser Teller Gourmet Cremesuppe von erlesenen Waldpilzen von Knorr mitbringen, damit er sicher das richtige findet. Genau das soll dann der Name sein, `Knorr` ist später im Feld `Marke` einzutragen. Bei Schokoladen würde man jemandem zum Beispiel "Bring mir eine Tafel Nuss-Splitter von Ritter Sport mit!" sagen, deshalb wäre der Name hier `Nuss-Splitter` und die Marke `Ritter Sport` (bei Behälter, siehe auch weiter unten, wäre in diesem Fall `Tafel` einzutragen). Wenn die Kombination aus Behälter, Name und Marke nicht intuitiv klingt, dann stimmt etwas nicht oder sie kann abgeändert werden, z.B. "~~eine Packung Rittersport Nuss-Splitter von Rittersport~~" wäre falsch.

Groß und Kleinschreibung anpassen (also nicht WALDPILZEN)

### Konzern
Hier soll der Name des Konzerns eingetragen werden, also sozusagen der des Besitzers der Marke. Hier einige Beispiele:
* `Unilever`
* `Mondelez International`
* `Nestle`
* `Procter & Gamble`
* `Tchibo`
* `Henkel`
* u.v.a.

Akzents wie bei Nestlé und Mondelēz sowie die Rechtsform wie GmbH oder ähnliches können weggelassen werden.

### Marke
`Knorr`  
(ohne ®)

### Herkunftsland
wird oft `--Unbekannt--` bleiben.  
Wird in der Regel extra beworben, dann z.B. `Österreich` auswählen.  Nicht verwechseln mit "Hergestellt in", da können
die Zutaten dann noch immer von irgendwo herkommen.

### Lagerung
sollte klar sein.

### Behälter
`Packung`, `Beutel`, `Karton`, `Dose`, `Glas`, `Glasflasche`, `Kunststoffflasche`, `Riegel`, `Tafel`...
(Neue Version sollte ein Drop-down haben).

### Beschreibung
Bei manchen Produkten gibt es ein paar Sätze als Beschreibung.
Bei unserer Cremesuppe ist auf der Rückseite unsere Beschreibung:
`Die Knorr Kaiser Teller Groumet Suppen wurden speziell [...] die Modena.`

Gäbe es den nicht, dann würde hier:
`Cremesuppe von erlesenen Waldpilzen mit Knoblauch und feiner Balsamiconote.`
passen (Text der Vorderseite).

### Inhalt (zur Preisberechnung)
Meistens das Gewicht oder die Flüssigkeitsmenge.  Die wird verwendet, um beim
Preisschild: xx€/100g anzeigen zu können.

Bei der Suppe ergibt ein Preis / Gewicht keinen Sinn.  Daher entweder leer
lassen, oder 2 `Anwendungen / Stück` auswählen. (2 Teller)

### Nährwertangaben
Die rechte Seite kann von der linken Seite ausgerechnet werden.  Rundungsfehler
sind uns in diesem Fall egal (selbst wenn die rechte Seite dann nicht mit den
Angaben auf dem Produkt zusammenpasst).

- pro 100 `ml` oder `g` auswählen
- links alle Felder soweit bekannt eintragen
- rechts oben bei *pro* die Menge pro Mahlzeit eintragen
- *Generate right* anklicken um die Werte rechts auszurechnen.

Bei manchen Produkten gibt es keine Angabe / Mahlzeit und es gibt den seltenen Fall wo die linke Seite pro 100 g angegeben ist, und die rechte Seite in Milliliter (oder eine andere Kombination von Maßeinheiten). In diesen Fällen kann die rechte Seite leer gelassen werden.

Bei unserer Cremesuppe sind die Nährwertangaben 'pro 100ml zubereitet'

with: / mit: ist für den Fall, dass andere Zutaten für den Konsum mitberechnet
werden.  Z.B. bei Cornflakes steht dann bei / Portion _mit 100ml Milch_.
Das dann einfach dort eintragen.

### Anmerkung(en)...
*SOLLTET IHR EUCH IRGENDWO NICHT SICHER SEIN...  TRAGT HIER EINE NACHRICHT AN
UNS EIN*

außerdem ist es hilfreich, wenn ihr in solchen Fällen den Status beim Speichern
z.B. auf 9 setzt.  Wir schauen uns dann gerne alles an.
Bsp: `Habe 2 Stück bei Inhalt (zur Preisberechnung) genommen, ok?`


Damit ist die erste Seite fertig.  Rechts oben zur nächsten Seite:

## Inhaltsstoffe, Kategorisierung, Tagging

### Inhaltsstoffe
Die Zutaten der Reihe nach abtippen.  Schon einmal eingegebene Zutaten
werden vorgeschlagen.  *Ohne % Angabe*. D.h. bei der Cremesuppe sollte
die 5 Zutat `Pilze (Champignons, Steinpilze, Butterpilze)` sein.  Groß und
Kleinschreibung anpassen: also nicht WEIZENMEHL, sondern `Weizenmehl`.

Anmerkungen (oft Hochzahlen oder *) wie 'aus biologischem Anbau',
'gewonnen aus natürlichen Kaliummineralien' ignorien.

Sollte eine Zutat ein Allergen beinhalten, dann wird dies direkt bei der
Zutat gespeichert.

- Text ändert sich von `Allergene für ...:` auf `Allergene für XYZ:`
- links das Allergen auswählen.  Die rechte Seite wird automatisch
  berechnet.

Getreide Zutaten haben in der Regel das Allergen 'Gluten'.

*Zusätzlich zu den normalen Allergenen haben wir HONIG und FLEISCH!*

Diese Felder bitte unbedingt ausfüllen.

(Die neue Version solle eine Warnung/Checkbox haben.)

Es gibt keine Möglichkeit eine Zutat umzubenennen.

Meldet uns das bitte im Anmerkungsfeld.

### Kann Spurent von enthalten

### Enthält eine geringe Menge
manche Produkte haben diese Angabe.


### Kategorie

Leider ist das Kategorie system derzeit noch in Englisch.

Wir arbeiten an deutschen Übersetzungen.

Für unsere Suppe `Food/Beverage/Tobacco` → `Prepared/Preserved Foods` → `Prepared Soups` → `Soups - Prepared (Shelf Stable)`

Solltet ihr euch nicht sicher sein, bitte in der Anmerkung schreiben.

### GS1 Tags
Je nach ausgewählter Kategorie werden hier "Fragen" gestellt.
Wenn Antworten klar sind, dann auswählen.  `Formation POWDER`.
Wenn eine Frage nicht klar ist: ins Anmerkungsfeld schreiben!
Bei der Suppe wird abgefragt ob:
*Suitability for Vegetarians/Vegans Claim*
In unserem Fall bleibt das `UNIDENTIFIED`!  Weil auf der Packung
nirgends steht!, dass sie vegetarisch / vegan ist!

Die Zutaten würden das vermuten lassen, aber solange der Hersteller
das nicht sagt, bleibt der claim auf unidentified.

### Eigene Tags/ Numerische Tags
wenn vorhanden bitte auch ausfüllen.


## abschließen / sichern

*abschließen* setzt den Status auf 10.  Das bedeutet für uns, dass das Produkt
fertig für den Export ist.

*sichern* setzt den Status (außer im Textfeld daneben wird eine Zahl eingegeben)
auf 5.  Dadurch ist klar, dass das Produkt noch nicht frei gegeben ist.



