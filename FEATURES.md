# Branch-Features: JWT Authentication

Dieser Branch (`ki/jwt-auth`) erweitert die API um Benutzerkonten, JWT-basierte
Authentifizierung und eine geschuetzte Benutzerressource.

## Authentifizierung

- `POST /api/login_check` akzeptiert JSON mit `username` und `password`.
- Bei gueltigen Zugangsdaten wird ein RSA-signiertes JWT mit einer
  Standardlaufzeit von 3600 Sekunden ausgegeben.
- API-Anfragen sind stateless und erwarten
  `Authorization: Bearer <token>`.
- Fehlgeschlagene Login-Versuche werden auf maximal 5 Versuche pro Minute
  begrenzt.
- JWT-Schluessel und die Passphrase werden ueber Umgebungsvariablen bezogen.
  Die PEM-Dateien bleiben durch `.gitignore` ausserhalb des Repositories.
- Bei einer neuen lokalen Installation muessen die Schluessel erzeugt werden:

  ```bash
  ddev console lexik:jwt:generate-keypair
  ```

## Benutzerkonto

Die Doctrine-Entity `App\\Entity\\User` implementiert
`UserInterface` und `PasswordAuthenticatedUserInterface`.

- UUID als Primaerschluessel
- E-Mail-Adresse als Login-Identifier und eindeutiger Datenbankwert
- E-Mail-Normalisierung durch Trim und Kleinschreibung
- Rollen als JSON-Wert mit implizitem `ROLE_USER`
- Gehashte Passwoerter, niemals als API-Feld serialisiert
- Automatische Passwort-Hash-Upgrades ueber `UserRepository`
- `createdAt` und `updatedAt` ueber `TimestampableTrait`
- Validierung von E-Mail, Passwort, Rollen und eindeutiger E-Mail-Adresse

## Benutzer-API

Die Resource `App\\ApiResource\\User` ist bewusst schreibgeschuetzt und
veroeffentlicht keine Passwortdaten.

| Methode | Pfad | Berechtigung |
|---|---|---|
| `GET` | `/users` | `ROLE_ADMIN` |
| `GET` | `/users/{id}` | Eigener Datensatz oder `ROLE_ADMIN` |

Die Collection unterstuetzt API-Platform-Paginierung mit dem Parameter
`items` und maximal 50 Eintraegen pro Seite. Der eigene State Provider mappt
Doctrine-Entities auf das oeffentliche DTO.

## Benutzeranlage per CLI

Der Befehl `app:user:create` legt Benutzer interaktiv an:

```bash
ddev console app:user:create [email] [--role=ROLE_NAME]
```

- Interaktive Eingabe von E-Mail, Passwort und Passwortbestaetigung
- Passwoerter muessen mindestens 12 Zeichen lang sein
- E-Mail-Adressen werden validiert und normalisiert
- Rollen koennen mehrfach angegeben werden und muessen dem Format
  `ROLE_[A-Z][A-Z0-9_]*` entsprechen
- Nicht-interaktive Ausfuehrung wird abgelehnt, damit Passwoerter nicht in der
  Shell-History landen
- Duplikate werden vor dem Schreiben geprueft und zusaetzlich durch den
  Datenbank-Unique-Index abgesichert

## Datenbank und Migrationen

- PostgreSQL 17 wird fuer die lokale DDEV-Umgebung verwendet.
- SQLite wird fuer die Tests verwendet.
- `Version20260805183440` legt die User-Tabelle an.
- `Version20260805193807` ergaenzt die Zeitstempel und behandelt SQLite-
  Tabellenmigrationen separat.

## Qualitaetssicherung

- Codeception Unit- und Functional-Tests decken Entity, Repository, CLI,
  Validierung und API-Berechtigungen ab.
- PHP CS Fixer, PHPStan, Psalm und Container-/Schema-Linting sind aktiviert.
- Codeception-Testabdeckung ist konfiguriert mit einem Zielbereich von 30 bis
  75 Prozent.
- OpenAPI/Swagger ist unter `/docs` verfuegbar, wenn die entsprechende
  Umgebung aktiv ist.

## Review-Hinweise

- Die API-Dokumentation liegt unter dem geschuetzten `/docs`-Pfad. Ohne JWT
  liefert `/docs` daher `401`; das sollte fuer die gewuenschte
  Dokumentationsstrategie bewusst entschieden werden.
- Die CI-Konfiguration fuehrt die gesamte Codeception-Suite zweimal aus. Das
  ist kein funktionaler Fehler dieses Features, verlaengert aber die Pipeline.
