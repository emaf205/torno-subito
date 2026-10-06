# TORNO SUBITO — Stati dell'opera

Questo documento descrive gli stati previsti dal sito pubblico.  
Non fa parte dell'esperienza visibile della bottega: serve come documentazione del progetto.

## 1. APERTO

**Lunedì–Sabato**
- 08:00–12:30
- 15:00–19:00

Il sito è pienamente navigabile.  
La cassa è disponibile.  
La saracinesca è alzata.

---

## 2. PAUSA PRANZO

**Lunedì–Sabato**
- 12:30–15:00

La saracinesca scende ma rimane parzialmente sollevata.

Il visitatore intravede ancora il negozio sotto la serranda, ma non può acquistare.

Messaggio:

> **PAUSA PRANZO**  
> Riapriamo alle 15:00.

---

## 3. CHIUSURA SERALE

**Dalle 19:00 alle 08:00**

Se un visitatore è già dentro, pochi secondi prima della chiusura compare:

> Stiamo per chiudere.

Alle 19:00 il sito cambia stato e la saracinesca scende.

Messaggio:

> **LA FERRAMENTA È CHIUSA**  
> Torneremo domani!

---

## 4. RIPOSO SETTIMANALE

**Domenica**

La bottega rimane chiusa per l'intera giornata.

Messaggio:

> **RIPOSO SETTIMANALE**  
> Ci rivediamo lunedì alle 08:00.

---

## 5. TEMPO NATURALE

Parallelamente agli orari commerciali, il sito modifica gradualmente la propria luce.

Gli stati sono:

- alba;
- giorno;
- tramonto;
- notte.

L'orario di alba e tramonto varia nel corso dell'anno tramite un calcolo stagionale interno, senza API esterne.

Il sito quindi segue contemporaneamente due tempi:

**tempo commerciale**  
apertura → pausa → riapertura → chiusura

**tempo naturale**  
alba → giorno → tramonto → notte

---

## 6. PRINCIPIO DELL'OPERA

La chiusura non è un messaggio promozionale e non crea scarsità artificiale.

Il sito potrebbe tecnicamente funzionare 24 ore su 24.

Semplicemente, sceglie di non farlo.

> **Non simulare il limite. Renderlo reale.**
